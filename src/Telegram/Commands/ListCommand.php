<?php

namespace Slavytuch\LearnSlim\Telegram\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Slavytuch\LearnSlim\Redis\Connection;

class ListCommand extends UserCommand
{
    protected $name = 'list';                      // Your command's name
    protected $description = 'List all entries'; // Your command description
    protected $usage = '/list';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute(): ServerResponse
    {
        foreach ($this->getEntries() as $entry) {
            Request::sendMessage(['chat_id' => $this->getMessage()->getFrom()->getId(), 'text' => $entry]);
        }

        return Request::emptyResponse();
    }

    public function getEntries()
    {
        $redis = Connection::getInstance()->redis;
        $keys = $redis->keys('entries:*');

        if (!$keys) {
            return ['No entries so far'];
        }

        $result = [];
        foreach ($keys as $key) {
            $result[] = 'Review by ' . str_replace('entries:', '', $key) . PHP_EOL . $redis->get($key);
        }

        return $result;
    }
}