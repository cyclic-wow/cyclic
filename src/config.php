<?php 
declare(strict_types=1);

class Config 
{
	const 

	// Database.
	DB = ['localhost', 'php', 'ubuntu', 'cyclic', null, '/var/run/mysqld/mysqld.sock'],

	// Data.
	WOW_SERVER_NAME = 'faerlina-horde',

	// Modes.
	UPDATE_ITEM_LIST       = false,
	UPDATE_AUCTION_HISTORY = true;
}