<?php

namespace Slavytuch\LearnSlim\Telegram\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Slavytuch\LearnSlim\Redis\Connection;
use Slavytuch\LearnSlim\Telegram\Overwrite\ConversationRedis;

class AddEntryCommand extends UserCommand
{
    protected $name = 'add';                      // Your command's name
    protected $description = 'AddEntryCommand'; // Your command description
    protected $usage = '/add';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command


    /**
     * Conversation Object
     */
    protected Conversation $conversation;

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $chat    = $message->getChat();
        $user    = $message->getFrom();
        $text    = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();
        // Preparing response
        $data = [
            'chat_id'      => $chat_id,
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];

        // Conversation start
        $this->conversation = new ConversationRedis($user_id, $chat_id, $this->getName());

        // Load any existing notes from this conversation
        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        // Load the current state of the conversation
        $state = $notes['state'] ?? 0;

        $result = Request::emptyResponse();

        switch ($state) {
            case 0:
                $notes['state'] = 1;

                $this->conversation->update();

                $data['text'] = 'Write your review:';

                $result = Request::sendMessage($data);
                break;
            case 1:
                Connection::getInstance()->redis->set('entries:' . $user->getFirstName(), $text);

                $data['text'] = 'Entry added';
                $result = Request::sendMessage($data);

                $this->conversation->stop();
                break;
        }

        return $result;
    }
}