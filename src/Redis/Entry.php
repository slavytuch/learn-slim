<?php

namespace Slavytuch\LearnSlim\Redis;

class Entry
{
    public function __construct(private Connection $connection)
    {
    }

    public function add(array $entry)
    {
        $this->connection->redis->set('entries:' . $entry['name'], $entry['text']);
    }
}