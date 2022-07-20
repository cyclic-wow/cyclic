<?php
declare(strict_types=1);

class Database 
{
	public object $mysql;

	function __construct(){
		$this->mysql = new mysqli(Config::DB[0], Config::DB[1], Config::DB[2], Config::DB[3], Config::DB[4], Config::DB[5]); 
	}

	public function query(string $q): object|bool
	{
		$result = $this->mysql->query($q);
	
		if ($result === false)	 
		{
			if (str_contains($q, 'INSERT INTO')) 
			{
				$values = explode('VALUES (', $q);

				printf("%s\n%s", $this->mysql->error, $values[0]);
				
				foreach (explode('),(', $values[1]) as $k => $v) {
					printf("#%d - %s", $k + 1, $v);
				}
			} else {
				printf('%s - %s', $this->mysql->error, $q);
			}			
		} 

		return $result;
	}
	
	public function getValue(string $q): string|bool
	{
		$result = $this->query($q);

		return $result->num_rows ?? false ? ($result->fetch_row()[0] ?? false) : false;
	}	
	
	public function getColumn(string $q): array|bool
	{ 
		$result = $this->query($q);

		return $result->num_rows ?? false ? array_column($result->fetch_all(MYSQLI_NUM), 0) : false;
	}

	public function getRow(string $q, int $type = MYSQLI_NUM): array|bool
	{ 
		$result = $this->query($q);
	
		return $result->num_rows ?? false ? $result->fetch_array($type) : false;
	}

	public function getRows(string $q, int $type = MYSQLI_NUM): array|bool 
	{ 
		$result = $this->query($q);
	
		return $result->num_rows ?? false ? $result->fetch_all($type) : false;
	}
}