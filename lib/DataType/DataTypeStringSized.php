<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataTypeStringSized extends DataTypeString {
	protected const SIZE = 'char_max_length';

	protected function byte_size(): int {
		if(!isset($this->size)) parent::byte_size();
		$max_bytes_per_char = $this->is_text() ? 4 : 1;
		return $this->size * $max_bytes_per_char;
	}
}
