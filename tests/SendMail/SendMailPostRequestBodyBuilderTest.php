<?php

namespace Tests\KignOrg\GraphApiAdapter\SendMail;

use InvalidArgumentException;
use KignOrg\GraphApiAdapter\SendMail\SendMailPostRequestBodyBuilder;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use function PHPUnit\Framework\assertEquals;

class SendMailPostRequestBodyBuilderTest extends TestCase
{
    private string $from = 'sender@email.com';
    private string $recipient = 'recipient@email.com';
    private string $subject = 'test subject';
    private string $bodyType = SendMailPostRequestBodyBuilder::BODY_TYPE_TEXT;
    private string $body = 'test body';

    /**
     * @throws ReflectionException
     */
    public function testBuild_withFromNotSet()
    {
        $this->expectException(InvalidArgumentException::class);
        SendMailPostRequestBodyBuilder::getBuilder()
            ->setSubject($this->subject)
            ->setBody($this->bodyType, $this->body)
            ->addRecipient($this->recipient)
            ->build();
    }

    /**
     * @throws ReflectionException
     */
    public function testBuild_withSubjectNotSet()
    {
        $this->expectException(InvalidArgumentException::class);
        SendMailPostRequestBodyBuilder::getBuilder()
            ->setFrom($this->from)
            ->setBody($this->bodyType, $this->body)
            ->addRecipient($this->recipient)
            ->build();
    }

    /**
     * @throws ReflectionException
     */
    public function testBuild_withRecipientNotSet()
    {
        $this->expectException(InvalidArgumentException::class);
        SendMailPostRequestBodyBuilder::getBuilder()
            ->setFrom($this->from)
            ->setSubject($this->subject)
            ->setBody($this->bodyType, $this->body)
            ->build();
    }

    public function testBuild_withBodyNotSet()
    {
        $this->expectException(InvalidArgumentException::class);
        SendMailPostRequestBodyBuilder::getBuilder()
            ->setFrom($this->from)
            ->setSubject($this->subject)
            ->addRecipient($this->recipient)
            ->build();
    }

    /**
     * @throws ReflectionException
     */
    public function testBuild_withAllNecessaryValuesSet()
    {
        $requestBody = SendMailPostRequestBodyBuilder::getBuilder()
            ->setFrom($this->from)
            ->setSubject($this->subject)
            ->setBody($this->bodyType, $this->body)
            ->addRecipient($this->recipient)
            ->setSaveToSentItems(false)
            ->build();

        assertEquals($this->from, $requestBody->getMessage()->getSender()->getEmailAddress()->getAddress());
        assertEquals($this->from, $requestBody->getMessage()->getFrom()->getEmailAddress()->getAddress());
        assertEquals($this->subject, $requestBody->getMessage()->getSubject());
        assertEquals($this->bodyType, $requestBody->getMessage()->getBody()->getContentType()->value());
        assertEquals($this->body, $requestBody->getMessage()->getBody()->getContent());
        assertEquals(1, count($requestBody->getMessage()->getToRecipients()));
        assertEquals($this->recipient, $requestBody->getMessage()->getToRecipients()[0]->getAddress());
        self::assertFalse($requestBody->getSaveToSentItems());
    }

    /**
     * @throws ReflectionException
     */
    public function testBuild_withAllNecessaryValuesSet_withSaveToSentItemsTrue()
    {
        $requestBody = SendMailPostRequestBodyBuilder::getBuilder()
            ->setFrom($this->from)
            ->setSubject($this->subject)
            ->setBody($this->bodyType, $this->body)
            ->addRecipient($this->recipient)
            ->setSaveToSentItems(true)
            ->build();

        self::assertTrue($requestBody->getSaveToSentItems());
    }
}
