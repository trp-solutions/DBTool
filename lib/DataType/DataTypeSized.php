<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataTypeSized extends DataType {
	protected const SIZE = 'length';

	public function is_lossless(DataType $other): bool {
		return $this->normalized_type() == $other->normalized_type()
			&& ($this->size ?? 0) <= ($other->size ?? 0);
	}
}
