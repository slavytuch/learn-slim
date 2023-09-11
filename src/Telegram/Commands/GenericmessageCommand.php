<?php

/**
 * This file is part of the PHP Telegram Bot example-bot package.
 * https://github.com/php-telegram-bot/example-bot/
 *
 * (c) PHP Telegram Bot Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 *
 * In this conversation-related context, we must ensure that active conversations get executed correctly.
 */

namespace Slavytuch\LearnSlim\Telegram\Commands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Slavytuch\LearnSlim\Telegram\Overwrite\ConversationRedis;

class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        // If a conversation is busy, execute the conversation command after handling the message.
        $conversation = new ConversationRedis(
            $message->getFrom()->getId(),
            $message->getChat()->getId()
        );

        // Fetch conversation command if it exists and execute it.
        if ($conversation->exists() && $command = $conversation->getCommand()) {
            return $this->telegram->executeCommand($command);
        }

        return Request::emptyResponse();
    }

    public function preExecute(): ServerResponse
    {
        if ($this->isPrivateOnly() && $this->removeNonPrivateMessage()) {
            $message = $this->getMessage();

            if ($user = $message->getFrom()) {
                return Request::sendMessage([
                    'chat_id'    => $user->getId(),
                    'parse_mode' => 'Markdown',
                    'text'       => sprintf(
                        "/%s command is only available in a private chat.\n(`%s`)",
                        $this->getName(),
                        $message->getText()
                    ),
                ]);
            }

            return Request::emptyResponse();
        }

        return $this->execute();
    }

}
