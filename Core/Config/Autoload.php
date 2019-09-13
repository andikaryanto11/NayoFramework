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
    'Core/Database' => [
        'Connection',
        'DBBuilder',
        'DBResults',
        'DbTrans',
        'Migration',
        'Seed',
        'Table',

    ],
    'Core/Interfaces' => [
        'IClsList',
        'IDbDriver'
    ],
    'Core/Database/Driver' => [
        'Mssql',
        'Mysqli',
        'Sqlsrv'
    ],
    'Core/Database/PDO' => [
        'PDOMsSQL',
        'PDOMySQL'
    ],
    'Core/Libraries' => [
        'Clslist',
        'Datatables',
        'Dictionary',
        'File',
        'Ftp',
        'Helper',
        'Redirect'
    ],
    'Core/Rest' => [
        'Response'
    ]

];