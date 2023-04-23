
<?php

use Dotenv\Dotenv;
use KignOrg\GraphApiAdapter\ApiAdapter;
use KignOrg\GraphApiAdapter\Secrets\SecretsDotenvAdapter;
use KignOrg\GraphApiAdapter\SendMail\SendMailAdapter;
use KignOrg\GraphApiAdapter\SendMail\SendMailPostRequestBodyBuilder;
use Microsoft\Graph\Beta\Generated\Models\ODataErrors\ODataError;

require __DIR__ . '/../vendor/autoload.php';

function initDotenv(string $directory): void
{
    $dotenv = Dotenv::createImmutable($directory);
    $dotenv->load();
    $dotenv->required(SecretsDotenvAdapter::getRequiredEnvVariables());
}

function sendMail(SendMailAdapter $adapter): void
{
    try {
        $userId = $_ENV['MAIL_SEND_USER_ID'];
        $request = SendMailPostRequestBodyBuilder::getBuilder()
            ->setFrom($_ENV['MAIL_SEND_FROM_ADDRESS'])
            ->addRecipient($_ENV['MAIL_SEND_TO_ADDRESS'])
            ->setSubject('Test Email')
            ->setBody(SendMailPostRequestBodyBuilder::BODY_TYPE_TEXT, "Hello World!")
            ->setSaveToSentItems(true)
            ->build();
        var_dump($adapter->sendMail($userId, $request));
        print(PHP_EOL . 'sent' . PHP_EOL . PHP_EOL);
    } catch (ODataError $e) {
        print(PHP_EOL . 'Error sending email: ' . $e . PHP_EOL . PHP_EOL);
        var_dump($e->getError());
        var_dump($e->getAdditionalData());
    } catch (Throwable $e) {
        print(PHP_EOL . 'Error sending email: ' . $e . PHP_EOL . PHP_EOL);
    }
}

function main(): void
{
    initDotenv(__DIR__ . '/../');
    $secrets = new SecretsDotenvAdapter();
    $graphClientAdapter = new ApiAdapter($secrets);
    $adapter = new SendMailAdapter($graphClientAdapter);
    sendMail($adapter);
}

main();
