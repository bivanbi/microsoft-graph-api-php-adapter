<?php

namespace KignOrg\GraphApiAdapter;

use Exception;
use Microsoft\Graph\Beta\Generated\Users\Item\SendMail\SendMailPostRequestBody;

class SendMailAdapter
{
    private ApiAdapter $apiAdapter;

    /**
     * @param ApiAdapter $apiAdapter
     */
    public function __construct(ApiAdapter $apiAdapter)
    {
        $this->apiAdapter = $apiAdapter;
    }

    public function createSendMailPostRequestBody(): SendMailPostRequestBody
    {
        return new SendMailPostRequestBody();
    }

    /**
     * @throws Exception
     */
    public function sendMail(string $userId, SendMailPostRequestBody $request) {
        return $this->apiAdapter->getGraphServiceClient()->usersById($userId)->sendMail()->post($request)->wait();
    }
}
