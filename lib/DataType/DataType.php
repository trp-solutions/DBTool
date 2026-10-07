<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataType implements \ArrayAccess, \JsonSerializable {
	public static $real_is_double = true;

	protected const SIZE = null;
	public readonly int $size;

	public static function from(SQLType $type): self {
		if($type->is_integer()){
			return new DataTypeInteger($type);
		} elseif($type->is_fixed_point()){
			return new DataTypeFixedPoint($type);
		} elseif($type->is_floating_point()){
			return new DataTypeFloatingPoint($type);
		} elseif($type->is_text() || $type->is_binary()){
			if($type->size_required() || $type->size_optional()){
				return new DataTypeStringSized($type);
			} else {
				return new DataTypeString($type);
			}
		} elseif($type->is_enum()){
			return new DataTypeEnum($type);
		} elseif($type->has_seconds()){
			return new DataTypeTime($type);
		} elseif($type->size_optional()){
			return new DataTypeSized($type);
		}
		return new self($type);
	}

	protected function __construct(public readonly SQLType $type){}

	public function offsetExists(mixed $offset): bool {
		return property_exists($this, $offset) && isset($this->$offset) || $offset == static::SIZE && isset($this->size);
	}
	public function offsetGet(mixed $offset): mixed {
		if($offset == 'name'){
			return $this->normalized_name();
		} elseif($offset == static::SIZE){
			return $this->size ?? null;
		} elseif(property_exists($this, $offset)) {
			return $this->$offset ?? null;
		} else {
			throw new \Exception("invalid offset on {$this->type->value} (".static::class."): '$offset'");
		}
	}
	public function offsetSet(mixed $offset, mixed $value): void {
		if($offset == static::SIZE || $offset == 'size'){
			$this->size = intval($value);
		} else {
			throw new \Exception("invalid offset on {$this->type->value} (".static::class."): '$offset'");
		}
	}
	public function offsetUnset(mixed $offset): void {

	}

	public function __call($name, $arguments){
		if(method_exists($this->type, $name)){
			return $this->type->$name(...$arguments);
		}
	}

	public function __toString(){
		return $this->normalized_name().$this->parameter_string();
	}

	public function string_with_attribute(){
		$attr = $this->attributes();
		if(!empty($attr)){
			return $this.' '.implode(' ',$attr);
		} else {
			return (string) $this;
		}
	}

	protected function parameter_string(){
		$size = $this->size ?? $this->default_size();
		return isset($size) ? '('.$size.')' : '';
	}

	protected function attributes(){
		return [];
	}

	public function to_array(){
		$array = get_object_vars($this);
		if(static::SIZE !== null && isset($array['size'])){
			$array[static::SIZE] = $array['size'];
			unset($array['size']);
		}
		$array['type'] = $this->type->normalized_type();
		return $array;
	}

	public function jsonSerialize(): mixed {
		return $this->to_array();
	}

	public function is_lossless(DataType $other): bool {
		return $this->normalized_type() == $other->normalized_type();
	}
}
