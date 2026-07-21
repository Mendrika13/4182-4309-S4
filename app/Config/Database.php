<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    /**
     * Path to the directory that holds the database files.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * The default database connection group.
     */
    public string $defaultGroup = 'default';

    /**
     * Default database connection.
     */
    public array $default = [
        'DSN'          => '',
        'DBDriver'     => 'SQLite3',
        'database'     => WRITEPATH . 'mobile_money.db',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => ENVIRONMENT !== 'production',
        'foreignKeys'  => true,
        'busyTimeout'  => 1000,
        'synchronous'  => null,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * Database connection for testing.
     */
    public array $tests = [
        'DSN'          => '',
        'DBDriver'     => 'SQLite3',
        'database'     => ':memory:',
        'DBPrefix'     => 'db_',
        'pConnect'     => false,
        'DBDebug'      => true,
        'foreignKeys'  => true,
        'busyTimeout'  => 1000,
        'synchronous'  => null,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}