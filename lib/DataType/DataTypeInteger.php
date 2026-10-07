<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataTypeInteger extends DataTypeNumeric {
	protected const SIZE = 'display_width';

	public function is_lossless(DataType $other): bool {
		return (
			parent::is_lossless($other)
			&& $this->storage_bytes() <= $other->storage_bytes()
			&& !( // note ! outside parenthesis
				(!$this->signed && $other->signed)
				// unsigned -> signed requires larger target
				&& $this->storage_bytes() < $other->storage_bytes()
			)
		);
	}
}
