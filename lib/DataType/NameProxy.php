<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class NameProxy implements \JsonSerializable {
	public function __construct(public readonly DataType $datatype){}
	public function __toString(){
		return $this->datatype->normalized_name();
	}

	public function jsonSerialize(): mixed {
		return (string) $this;
	}
}
