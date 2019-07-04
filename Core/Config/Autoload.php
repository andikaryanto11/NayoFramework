<?php

$corenamespaces = [
    'Core' => [
        'Controller',
        'CLI',
        'Exception',
        'Loader',
        'Model',
        'Request',
        'CSRF',
        'Session'     
    ],
    'Core\Database' => [
        'Connection',
        'DBBuilder',
        'DBResults',
        'Migration',
        'Seed',
        'Table'
    ],
    'Core\Interfaces' => [
        'IClsList',
        'IDbDriver'
    ],
    'Core\Database\Driver' => [
        'Mssql',
        'Mysqli',
        'Sqlsrv'
    ],
    'Core\Database\PDO' => [
        'PDOMsSQL',
        'PDOMySQL'
    ],
    'Core\Libraries' => [
        'ClsList',
        'Datatables',
        'Dictionary',
        'File',
        'Ftp',
        'Helper'
    ],
    'Core\Rest' => [
        'Response'
    ]

];