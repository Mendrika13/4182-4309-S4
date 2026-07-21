<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    public string $defaultGroup = 'default';

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