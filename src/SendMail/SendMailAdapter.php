<?php

namespace KignOrg\GraphApiAdapter\SendMail;

use Exception;
use KignOrg\GraphApiAdapter\ApiAdapter;
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

    /**
     * @throws Exception
     */
    public function sendMail(string $userId, SendMailPostRequestBody $request) {
        return $this->apiAdapter->getGraphServiceClient()->usersById($userId)->sendMail()->post($request)->wait();
    }
}
