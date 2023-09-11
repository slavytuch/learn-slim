<?php

namespace Slavytuch\LearnSlim\Redis;

use Redis;

final class Connection
{
    public readonly ?Redis $redis;

    protected static ?Connection $instance = null;
    protected function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('redis');
        $this->redis->auth('eYVX7EwVmmxKPCDmwMtyKVge8oLd2t81');
    }

    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new Connection();
        }

        return self::$instance;
    }

    protected function __clone() {
    }


}