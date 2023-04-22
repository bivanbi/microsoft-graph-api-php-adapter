
<?php

use Dotenv\Dotenv;
use KignOrg\GraphApiAdapter\ApiAdapter;
use KignOrg\GraphApiAdapter\Secrets\SecretsDotenvAdapter;
use KignOrg\GraphApiAdapter\SendMailAdapter;
use Microsoft\Graph\Beta\Generated\Models\BodyType;
use Microsoft\Graph\Beta\Generated\Models\EmailAddress;
use Microsoft\Graph\Beta\Generated\Models\ItemBody;
use Microsoft\Graph\Beta\Generated\Models\Message;
use Microsoft\Graph\Beta\Generated\Models\ODataErrors\ODataError;
use Microsoft\Graph\Beta\Generated\Models\Recipient;

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
        $request = $adapter->createSendMailPostRequestBody();
        $userId = $_ENV['MAIL_SEND_USER_ID'];
        $message = new Message();
        $message->setSubject('Test Email');
        $messageBody = new ItemBody();
        $messageBody->setContentType(new BodyType('text'));
        $messageBody->setContent("Hello World!");
        $message->setBody($messageBody);

        $sender = new Recipient();
        $senderAddress = new EmailAddress();
        $senderAddress->setAddress($_ENV['MAIL_SEND_FROM_ADDRESS']);
        $sender->setEmailAddress($senderAddress);
        $message->setFrom($sender);

        $recipient = new Recipient();
        $recipientAddress = new EmailAddress();
        $recipientAddress->setAddress($_ENV['MAIL_SEND_TO_ADDRESS']);
        $recipient->setEmailAddress($recipientAddress);
        $message->setToRecipients([$recipient]);

        $request->setMessage($message);
        $request->setSaveToSentItems(true);

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
