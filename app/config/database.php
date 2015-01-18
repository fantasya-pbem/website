<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

	'fetch' => PDO::FETCH_CLASS,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => $_ENV['MYSQL_DB_MANAGEMENT'],

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(
		$_ENV['MYSQL_DB_MANAGEMENT'] => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => $_ENV['MYSQL_DB_MANAGEMENT'],
			'username'  => $_ENV['MYSQL_USER_MANAGEMENT'],
			'password'  => $_ENV['MYSQL_PASS_MANAGEMENT'],
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),
		$_ENV['MYSQL_DB_MAIN'] => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => $_ENV['MYSQL_DB_MAIN'],
			'username'  => $_ENV['MYSQL_USER_MAIN'],
			'password'  => $_ENV['MYSQL_PASS_MAIN'],
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),
		$_ENV['MYSQL_DB_BETA'] => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => $_ENV['MYSQL_DB_BETA'],
			'username'  => $_ENV['MYSQL_USER_BETA'],
			'password'  => $_ENV['MYSQL_PASS_BETA'],
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),
		$_ENV['MYSQL_DB_SKIRMISH'] => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => $_ENV['MYSQL_DB_SKIRMISH'],
			'username'  => $_ENV['MYSQL_USER_SKIRMISH'],
			'password'  => $_ENV['MYSQL_PASS_SKIRMISH'],
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		)
	),

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => array(

		'cluster' => false,

		'default' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		),

	),

);
