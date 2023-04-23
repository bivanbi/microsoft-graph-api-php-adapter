<?php

namespace KignOrg\GraphApiAdapter\SendMail;

use InvalidArgumentException;
use Microsoft\Graph\Beta\Generated\Models\BodyType;
use Microsoft\Graph\Beta\Generated\Models\EmailAddress;
use Microsoft\Graph\Beta\Generated\Models\ItemBody;
use Microsoft\Graph\Beta\Generated\Models\Message;
use Microsoft\Graph\Beta\Generated\Models\Recipient;
use Microsoft\Graph\Beta\Generated\Users\Item\SendMail\SendMailPostRequestBody;
use ReflectionException;

class SendMailPostRequestBodyBuilder
{
    public const BODY_TYPE_TEXT = BodyType::TEXT;

    private string $subject;
    private Recipient $sender;
    private array $recipients = [];
    private ItemBody $body;
    private bool $saveToSentItems = false;

    public static function getBuilder(): static
    {
        return new SendMailPostRequestBodyBuilder();
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function setFrom(string $emailAddress): static
    {
        $this->sender = new Recipient();
        $senderAddress = $this->getEmailAddress($emailAddress);
        $this->sender->setEmailAddress($senderAddress);
        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public function setBody(string $contentType, string $content): static
    {
        $this->body = new ItemBody();
        $this->body->setContentType(new BodyType($contentType));
        $this->body->setContent($content);
        return $this;
    }

    public function addRecipient(string $emailAddress): static
    {
        $this->recipients[] = $this->getEmailAddress($emailAddress);
        return $this;
    }

    public function setSaveToSentItems(bool $saveToSentItems): static
    {
        $this->saveToSentItems = $saveToSentItems;
        return $this;
    }

    public function build(): SendMailPostRequestBody
    {
        $this->assertAllNecessaryValueSet();
        $request = new SendMailPostRequestBody();

        $message = new Message();
        $message->setSubject($this->subject);
        $message->setBody($this->body);
        $message->setFrom($this->sender);
        $message->setSender($this->sender);
        $message->setToRecipients($this->recipients);

        $request->setMessage($message);
        $request->setSaveToSentItems($this->saveToSentItems);

        return $request;
    }

    private function assertAllNecessaryValueSet(): void
    {
        if (!isset($this->sender)) {
            throw new InvalidArgumentException("Sender must be set");
        }

        if (!isset($this->subject)) {
            throw new InvalidArgumentException("Subject must be set");
        }

        if (!isset($this->body)) {
            throw new InvalidArgumentException("Body must be set");
        }

        if (empty($this->recipients)) {
            throw new InvalidArgumentException("Recipient must be set");
        }
    }

    private function getEmailAddress(string $address): EmailAddress
    {
        $emailAddress = new EmailAddress();
        $emailAddress->setAddress($address);
        return $emailAddress;
    }
}
