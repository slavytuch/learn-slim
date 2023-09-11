<?php

namespace Slavytuch\LearnSlim\Controllers;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Telegram;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class BotController
{
    public function __construct(protected Telegram $telegram, protected LoggerInterface $logger)
    {
    }

    public function process(Request $request, Response $response)
    {
        $this->logger->info('incoming request', ['request' => json_decode($request->getBody()->getContents(), true)]);

        try {
            $this->telegram->handle();
        } catch (\Exception $exception) {
            $this->logger->error('Error while sending message', ['error' => $exception->getMessage()]);
        }

        return $response;
    }
}