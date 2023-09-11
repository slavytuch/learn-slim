<?php

namespace Slavytuch\LearnSlim\Telegram\Overwrite;

use Slavytuch\LearnSlim\Redis\Connection;

class ConversationRedis extends \Longman\TelegramBot\Conversation
{
    protected Connection $connection;

    public function __construct(int $user_id, int $chat_id, string $command = '')
    {
        $this->connection = Connection::getInstance();

        parent::__construct($user_id, $chat_id, $command);
    }

    protected function updateStatus(string $status): bool
    {
        if ($this->exists()) {
            return $this->update(['status' => $status]);
        }

        return false;
    }

    public function update(array $fields = []): bool
    {
        $currentValues = [
            'user_id' => $this->user_id,
            'chat_id' => $this->chat_id,
            'command' => $this->command,
            'notes' => $this->notes,
        ];

        return $this->connection->redis->set(
            'conversations:' . $this->user_id . ':' . $this->chat_id,
            json_encode(array_merge($currentValues, $fields))
        );
    }

    protected function load(): bool
    {
        $this->conversation =  json_decode(
            $this->connection->redis->get('conversations:' . $this->user_id . ':' . $this->chat_id),
            true
        );

        if ($this->conversation['command']) {
            $this->command = $this->conversation['command'];
        }

        if ($this->conversation['notes']) {
            $this->notes = $this->conversation['notes'];
        }

        return $this->exists();
    }

    public function stop(): bool
    {
        return $this->connection->redis->del('conversations:' . $this->user_id . ':' . $this->chat_id);
    }

    public function cancel(): bool
    {
        return $this->connection->redis->del('conversations:' . $this->user_id . ':' . $this->chat_id);
    }
}