<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataTypeNumericPoint extends DataTypeNumeric {
	protected const SIZE = 'precision';
	public readonly string $decimals;

	public function offsetSet(mixed $offset, mixed $value): void {
		match($offset){
			'decimals' => $this->decimals = $value,
			default => parent::offsetSet($offset, $value)
		};
	}

	protected function parameter_string(){
		if(isset($this->decimals)){
			return '('.($this->size??$this->default_size()).', '.($this->decimals??0).')';
		} else {
			return parent::parameter_string();
		}
	}
}
