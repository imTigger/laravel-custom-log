<?php

namespace Imtigger\LaravelCustomLog;

use App;
use Imtigger\LaravelCustomLog\MysqlHandler;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Monolog\Handler\GelfHandler;
use Monolog\Handler\RedisHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Handler\GroupHandler;
use Monolog\Handler\WhatFailureGroupHandler;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

class CustomLog
{
    private static $channels = [];

    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        return self::getSystemLogger();
    }

    public static function getChannel($channel) {
        if (isset(self::$channels[$channel])) {
            return self::$channels[$channel];
        } else {
            $log = new Logger($channel);

            if (Config::get('custom-log.failsafe')) {
                $log->pushHandler(new WhatFailureGroupHandler(self::getHandlers($channel)));
            } else {
                $log->pushHandler(new GroupHandler(self::getHandlers($channel)));
            }

            self::$channels[$channel] = $log;

            return $log;
        }
    }

    public static function getHandlers($channel) {
        $handlers = [];

        $formatter = new LineFormatter(null, null, true, true);
        
        if (Config::get('custom-log.stacktrace')) {
            $formatter->includeStacktraces(true);
        }
        
        if (Config::get('custom-log.console.enable', true)) {
            $consoleHandler = new StreamHandler('php://stdout', Logger::DEBUG);
            $consoleHandler->setFormatter($formatter);
            $handlers[] = $consoleHandler;
        }

        if (Config::get('custom-log.file.enable', true)) {
            $fileHandler = new RotatingFileHandler(storage_path() . "/logs/{$channel}.log",  0, Logger::DEBUG, true, 0666, false);
            $fileHandler->setFormatter($formatter);
            $handlers[] = $fileHandler;
        }

        if (Config::get('custom-log.mysql.enable')) {
            $mysqlHandler = new MysqlHandler(Config::get('custom-log.mysql.connection'), Config::get('custom-log.mysql.table'));
            $mysqlHandler->setFormatter($formatter);
            $handlers[] = $mysqlHandler;
        }

        if (Config::get('custom-log.redis.enable')) {
            $redisHandler = new RedisHandler(Redis::connection(Config::get('custom-log.redis.connection'))->client(), Config::get('custom-log.redis.key'));
            $handlers[] = $redisHandler;
        }

        if (Config::get('custom-log.syslog.enable')) {
            if (Config::get('custom-log.syslog.host')) {
                $handlers[] = new SyslogUdpHandler(Config::get('custom-log.syslog.host'), Config::get('custom-log.syslog.port'), LOG_USER, Logger::DEBUG, true, Config::get('times.application_name'));
            } else {
                $handlers[] = new SyslogHandler(Config::get('times.application_name'));
            }
        }

        if (Config::get('custom-log.gelf.enable')) {
            if (Config::get('custom-log.gelf.protocol') == 'TCP') {
                $transport = new \Gelf\Transport\TcpTransport(Config::get('custom-log.gelf.host'), Config::get('custom-log.gelf.port'));
            } else {
                $transport = new \Gelf\Transport\UdpTransport(Config::get('custom-log.gelf.host'), Config::get('custom-log.gelf.port'), \Gelf\Transport\UdpTransport::CHUNK_SIZE_LAN);
            }
            $publisher = new \Gelf\Publisher($transport);
            $gelfHandler = new GelfHandler($publisher);
            $handlers[] = $gelfHandler;
        }

        return $handlers;
    }

    public static function getSystemLogger() {
        return self::getChannel('laravel');
    }

    public static function getSystemHandler() {
        if (Config::get('custom-log.failsafe')) {
            return new WhatFailureGroupHandler(self::getSystemLogger()->getHandlers());
        } else {
            return new GroupHandler(self::getSystemLogger()->getHandlers());
        }
    }

    public static function emergency($channel = 'laravel', $content = null, $context = []) {
        self::log(Logger::EMERGENCY, $channel, $content, $context);
    }

    public static function alert($channel = 'laravel', $content = null, $context = []) {
        self::log(Logger::ALERT, $channel, $content, $context);
    }

    public static function critical($channel = 'laravel', $content = null, $context = []) {
        self::log(Logger::CRITICAL, $channel, $content, $context);
    }

    public static function error($channel = 'laravel', $content = null, $context = []) {
        self::log(Logger::ERROR, $channel, $content, $context);
    }

    public static function warning($channel = 'laravel', $content = null, $context = []) {
        self::log(Logger::WARNING, $channel, $content, $context);
    }

    public static function notice($channel = 'laravel', $content = null, $context = []) {
        self::log(Logger::NOTICE, $channel, $content, $context);
    }

    public static function info($channel = 'laravel', $content = null, $context = []) {
        self::log(Logger::INFO, $channel, $content, $context);
    }

    public static function debug($channel = 'laravel', $content = null, $context = []) {
        self::log(Logger::DEBUG, $channel, $content, $context);
    }

    public static function log($level, $channel = 'laravel', $content = null, $context = []) {
        $log = self::getChannel($channel);
        $log->addRecord($level, $content, $context);
    }
}