<?php
/*
DBTool is licensed under the Apache License 2.0 license
https://github.com/trp-solutions/DBTool/blob/main/LICENSE
*/
declare(strict_types=1);
namespace TRP\DBTool\DataType;

enum SQLType: string {
	public static function known_types(){
		return array_map(fn($case) => $case->value, self::cases());
	}

	/*** Integer types ***/
	case Int = 'INT';
	case Integer = 'INTEGER';
	case Tinyint = 'TINYINT';
	case Smallint = 'SMALLINT';
	case Mediumint = 'MEDIUMINT';
	case Bigint = 'BIGINT';
	case Bool = 'BOOL';
	case Boolean = 'BOOLEAN';
	public function is_integer(){
		return $this == self::Int
			|| $this == self::Integer
			|| $this == self::Tinyint
			|| $this == self::Smallint
			|| $this == self::Mediumint
			|| $this == self::Bigint
			|| $this == self::Bool
			|| $this == self::Boolean;
	}

	/*** Fixed point types ***/
	case Decimal = 'DECIMAL';
	case Numeric = 'NUMERIC';

	public function is_fixed_point(){
		return $this == self::Decimal
			|| $this == self::Numeric;
	}

	/*** Floating point types ***/
	case Float = 'FLOAT';
	case Real = 'REAL';
	case Double = 'DOUBLE';

	public function is_floating_point(){
		return $this == self::Float
			|| $this == self::Real
			|| $this == self::Double;
	}

	/*** Text types ***/
	case Varchar = 'VARCHAR';
	case Char = 'CHAR';
	case Text = 'TEXT';
	case Tinytext = 'TINYTEXT';
	case Mediumtext = 'MEDIUMTEXT';
	case Longtext = 'LONGTEXT';

	public function is_text(){
		return $this == self::Varchar
			|| $this == self::Char
			|| $this == self::Text
			|| $this == self::Tinytext
			|| $this == self::Mediumtext
			|| $this == self::Longtext;
	}

	/*** Binary string types ***/
	case Varbinary = 'VARBINARY';
	case Binary = 'BINARY';
	case Blob = 'BLOB';
	case Tinyblob = 'TINYBLOB';
	case Mediumblob = 'MEDIUMBLOB';
	case Longblob = 'LONGBLOB';
	
	public function is_binary(){
		return $this == self::Varbinary
			|| $this == self::Binary
			|| $this == self::Blob
			|| $this == self::Tinyblob
			|| $this == self::Mediumblob
			|| $this == self::Longblob;
	}

	/*** Enum types ***/
	case Enum = 'ENUM';
	case Set = 'SET';

	public function is_enum(){
		return $this == self::Enum
			|| $this == self::Set;
	}

	/*** Date/Time types ***/
	case Timestamp = 'TIMESTAMP';
	case Time = 'TIME';
	case Datetime = 'DATETIME';
	case Date = 'DATE';
	case Year = 'YEAR';

	public function has_seconds(){
		return $this == self::Timestamp
			|| $this == self::Time
			|| $this == self::Datetime;
	}

	public function can_default_current(){
		return $this == self::Timestamp
			|| $this == self::Datetime;
	}

	/*** Other types ***/
	case Bit = 'BIT';
	case Json = 'JSON';
	case Uuid = 'UUID';

	/*** End of types ***/

	public function normalized_type(){
		if($this == self::Bool || $this == self::Boolean) return self::Tinyint;
		if($this == self::Integer) return self::Int;
		if($this == self::Numeric) return self::Decimal;
		if($this == self::Real) return DataType::$real_is_double ? self::Double : self::Float;
		return $this;
	}

	public function normalized_name(){
		return $this->normalized_type()->value;
	}

	public function size_optional(){
		return $this->is_numeric()
			|| $this == self::Bit
			|| $this == self::Binary
			|| $this == self::Char;
	}

	public function size_required(){
		return $this == self::Varbinary
			|| $this == self::Varchar;
	}

	public function size_name(){
		if($this->is_integer()) return 'display_width';
		if($this->is_floating_point()) return 'precision';
		if($this->is_fixed_point()) return 'precision';
		if($this->is_string()) return 'char_max_length';
		return 'length';
	}

	public function is_string(){
		return $this->is_text() || $this->is_binary() || $this->is_enum();
	}

	public function accepts_string_literal(){
		return $this->is_string()
			|| $this == self::Timestamp
			|| $this == self::Time
			|| $this == self::Datetime
			|| $this == self::Date
			|| $this == self::Year;
	}

	public function is_numeric(){
		return $this->is_integer() || $this->is_fixed_point() || $this->is_floating_point();
	}

	public function default_size(){
		return match($this){
			self::Bool, self::Boolean => 1,
			self::Tinyint => 4,
			self::Smallint => 6,
			self::Mediumint => 9,
			self::Int, self::Integer => 11,
			self::Bigint => 20,
			self::Decimal, self::Numeric => 10,
			self::Char => 1,
			self::Binary => 1,
			self::Bit => 1,
			default => null
		};
	}

	public function is_nullable_by_default(){
		return $this != self::Timestamp;
	}

	public function zero_value(){
		return match($this){
			self::Timestamp => "'0000-00-00 00:00:00'",
			self::Uuid => "'00000000-0000-0000-0000-000000000000'",
			default => null
		};
	}

	public function storage_bytes(){
		return match($this){
			self::Tinyint => 1,
			self::Smallint => 2,
			self::Mediumint => 3,
			self::Int, self::Integer => 4,
			self::Bigint => 8,
			default => 0
		};
	}

	public function storage_power(){
		// type can store up to 2^N bytes of data
		return match($this){
			// Tinytext and Tinyblob holds up to 2^8-1 bytes
			// Length marker is 1 byte = 16 bits, one byte used by 0x00 string terminator
			self::Tinytext,self::Tinyblob => 8,
			// Text and Blob holds up to 2^16-1 bytes
			// Length marker is 2 bytes = 16 bits, one byte used by 0x00 string terminator
			self::Text,self::Blob => 16,
			// Mediumtext and Mediumblob holds up to 2^24-1 bytes
			// Length marker is 3 bytes = 24 bits, one byte used by 0x00 string terminator
			self::Mediumtext,self::Mediumblob => 24,
			// Longtext and Longblob holds up to 2^32-1 bytes
			// Length marker is 4 bytes = 32 bits, one byte used by 0x00 string terminator
			self::Longtext,self::Longblob => 32,
			// Binary holds up to 2^8-1 bytes
			self::Binary => 8,
			// Char holds up to 2^8-1 characters = 2^10-4 bytes
			// Characters can be up to 4 bytes depending on character set
			self::Char => 10,
			// Varbinary holds up to 2^16-1 bytes
			self::Varbinary => 16,
			// Varchar holds up to 2^16-1 characters = 2^18-4 bytes
			// Characters can be up to 4 bytes depending on character set
			self::Varchar => 18,
			default => 0
		};
	}
}
