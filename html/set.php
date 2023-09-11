<?php

require '../vendor/autoload.php';

$apiKey = $_ENV['TELEGRAM_BOT_TOKEN'];
$botName = $_ENV['TELEGRAM_BOT_NAME'];
$webhook = 'https://liberal-trusting-boxer.ngrok-free.app/telegram';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($apiKey, $botName);

    // Set webhook
    $result = $telegram->setWebhook($webhook);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
}