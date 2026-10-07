<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

class DataTypeString extends DataType {
	protected readonly bool $binary;
	public readonly string $character_set;
	public readonly string $collate;

	
	public function offsetGet(mixed $offset): mixed {
		if($offset == 'character set'){
			return $this->character_set ?? null;
		} else {
			return parent::offsetGet($offset);
		}
	}
	public function offsetSet(mixed $offset, mixed $value): void {
		match($offset){
			'character set' => $this->character_set = $value,
			'collate' => $this->collate = $value,
			default => parent::offsetSet($offset, $value)
		};
	}

	public function offsetExists(mixed $offset): bool {
		return $offset == 'character set' && isset($this->character_set) || parent::offsetExists($offset);
	}

	public function is_lossless(DataType $other): bool {
		return is_a($other, self::class)
			&& $this->byte_size() <= $other->byte_size();
	}

	protected function byte_size(): int {
		return 2 ** $this->storage_power() - 1;
	}

	protected function attributes(){
		$attr = parent::attributes();
		if(isset($this->character_set)){
			$attr[] = 'CHARACTER SET '.$this->character_set;
		}
		if(isset($this->collate)){
			$attr[] = 'COLLATE '.$this->collate;
		}
		return $attr;
	}
}
