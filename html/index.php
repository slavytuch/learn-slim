<?php

use DI\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;

require '../vendor/autoload.php';
$container = new Container();

$dotenv = \Dotenv\Dotenv::createImmutable('/var/www');

$dotenv->load();

$container->set(
    \Longman\TelegramBot\Telegram::class,
    function () {
        $telegram = new \Longman\TelegramBot\Telegram(
            $_ENV['TELEGRAM_BOT_TOKEN'],
            $_ENV['TELEGRAM_BOT_NAME']
        );

        $telegram->addCommandClasses(
            [
                \Slavytuch\LearnSlim\Telegram\Commands\ListCommand::class,
                \Slavytuch\LearnSlim\Telegram\Commands\AddEntryCommand::class,
                \Slavytuch\LearnSlim\Telegram\Commands\GenericmessageCommand::class,
            ]
        );

        return $telegram;
    }
);

$container->set(
    \Psr\Log\LoggerInterface::class,
    fn() => (new \Monolog\Logger('bot'))->pushHandler(
        new \Monolog\Handler\StreamHandler($_SERVER['DOCUMENT_ROOT'] . '/logs/bot.log')
    )
);

AppFactory::setContainer($container);

$app = AppFactory::create();


$app->get('/', [\Slavytuch\LearnSlim\Controllers\ReviewsController::class, 'list']);
$app->get('/form', [\Slavytuch\LearnSlim\Controllers\ReviewsController::class, 'form']);
$app->post('/add', [\Slavytuch\LearnSlim\Controllers\ReviewsController::class, 'add']);
$app->get('/edit/{reviewHash}', [\Slavytuch\LearnSlim\Controllers\ReviewsController::class, 'edit']);
$app->post('/edit/{reviewHash}', [\Slavytuch\LearnSlim\Controllers\ReviewsController::class, 'update']);
$app->post('/delete/{reviewHash}', [\Slavytuch\LearnSlim\Controllers\ReviewsController::class, 'delete']);
$app->post('/telegram', [\Slavytuch\LearnSlim\Controllers\BotController::class, 'process']);

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorLogger = (new \Monolog\Logger('slim-error'))->pushHandler(
    new \Monolog\Handler\StreamHandler($_SERVER['DOCUMENT_ROOT'] . '/logs/slim-error.log')
);

$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    ?LoggerInterface $logger = null
) use ($app, $errorLogger) {

    $errorLogger?->error($exception->getMessage());

    $payload = ['error' => $exception->getMessage()];

    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode($payload, JSON_UNESCAPED_UNICODE)
    );

    return $response;
};

$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

$app->run();