<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataTypeFloatingPoint extends DataTypeNumericPoint {
	public function is_lossless(DataType $other): bool {
		return (
			parent::is_lossless($other)
			&& ($this->normalized_type() == SQLType::Float || $other->normalized_type() != SQLType::Float)
		);
	}
}
