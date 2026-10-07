<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataTypeNumeric extends DataType {
	public readonly bool $signed;
	public readonly bool $zerofill;
	protected readonly int $bitwidth;
	protected $debug = false;

	public function offsetSet(mixed $offset, mixed $value): void {
		if($offset == 'signed'){
			$this->signed   = (bool) $value;
		} elseif($offset == 'unsigned'){
			$this->signed   = !$value;
		} elseif($offset == 'zerofill'){
			$this->zerofill = (bool) $value;
		} else {
			parent::offsetSet($offset, $value);
		}
	}

	public function offsetGet(mixed $offset): mixed {
		if($offset == 'unsigned'){
			return !$this->signed;
		} else {
			return parent::offsetGet($offset);
		}
	}

	public function offsetExists(mixed $offset): bool {
		return $offset == 'unsigned' || parent::offsetExists($offset);
	}
	
	public function is_lossless(DataType $other): bool {
		return (
			is_a($other, self::class)
			&& isset($this->signed)
			&& isset($other->signed)
			// signed -> unsigned is not lossless
			&& !($this->signed && !$other->signed)
		);
	}

	protected function attributes(){
		$attr = parent::attributes();
		if(!($this->signed ?? true)){
			$attr[] = 'UNSIGNED';
		}
		if($this->zerofill ?? false){
			$attr[] = 'ZEROFILL';
		}
		return $attr;
	}
}
