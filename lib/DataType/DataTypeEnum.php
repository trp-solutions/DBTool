<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataTypeEnum extends DataTypeString {
	public readonly array $values;

	public function offsetSet(mixed $offset, mixed $value): void {
		match($offset){
			'values' => $this->values = $value,
			default => parent::offsetSet($offset, $value)
		};
	}

	protected function parameter_string(){
		return isset($this->values) ? "(".implode(",",$this->values).")" : '()';
	}

	public function is_lossless(DataType $other): bool {
		// parenthesis for ease of reading
		return (
			is_a($other, self::class) // lossless conversion only possible to this type
			&& !($this->type == SQLType::Set && $other->type == SQLType::Enum) // Set can't convert losslessly to Enum
			&& !($this->type == SQLType::Enum && $other->type == SQLType::Set && count($this->values) > 64) // Set can't have more than 64 members
			&& empty(array_diff($this->values, $other->values)) // find possible values in this enum, that aren't in the other enum
		);
	}

	public function values_diff(DataType $other): ?array {
		if(!is_a($other, self::class)) return null;
		return array_diff($this->values, $other->values);
	}
}
