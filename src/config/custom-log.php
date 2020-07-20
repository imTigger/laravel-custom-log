<?php

return [
    'failsafe' => env('CUSTOM_LOG_FAILSAFE', true),
    'stacktrace' => env('CUSTOM_LOG_STACKTRACE', false),
    'console' => [
        'enable' => env('CUSTOM_LOG_CONSOLE_ENABLE', false),
    ],
    'file' => [
        'enable' => env('CUSTOM_LOG_FILE_ENABLE', true),
    ],
    'mysql' => [
        'enable' => env('CUSTOM_LOG_MYSQL_ENABLE', false),
        'connection' => env('DB_LOG_CONNECTION', 'mysql'),
        'table' => env('DB_LOG_TABLE', 'logs'),
    ],
    'redis' => [
        'enable' => env('CUSTOM_LOG_REDIS_ENABLE', false),
        'connection' => env('REDIS_LOG_CONNECTION', 'default'),
        'key' => env('REDIS_LOG_KEY'),
    ],
    'syslog' => [
        'enable' => env('CUSTOM_LOG_SYSLOG_ENABLE', false),
        'host' => env('CUSTOM_LOG_SYSLOG_HOST'),
        'port' => env('CUSTOM_LOG_SYSLOG_PORT', 514),
    ],
    'gelf' => [
        'enable' => env('CUSTOM_LOG_GELF_ENABLE', false),
        'protocol' => env('CUSTOM_LOG_GELF_PROTOCOL', 'UDP'),
        'host' => env('CUSTOM_LOG_GELF_HOST'),
        'port' => env('CUSTOM_LOG_GELF_PORT', 12201),
    ]
];
