<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataTypeFixedPoint extends DataTypeNumericPoint {
	public function is_lossless(DataType $other): bool {
		return parent::is_lossless($other) && $this->is_fixed_point() && $other->is_fixed_point();
	}

	public function offsetGet(mixed $offset): mixed {
		if($offset == 'decimals'){
			return $this->decimals ?? 0;
		} else {
			return parent::offsetGet($offset);
		}
	}

	public function offsetExists(mixed $offset): bool {
		return match($offset){
			'decimals' => true,
			default => parent::offsetExists($offset)
		};
	}

	protected function parameter_string(){
		return '('.($this->size??$this->default_size()).', '.($this->decimals ?? 0).')';
	}
}
