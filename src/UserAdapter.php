<?php

namespace KignOrg\GraphApiAdapter;

use Exception;
use Microsoft\Graph\Beta\Generated\Models\UserCollectionResponse;

class UserAdapter
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
    public function getUsers(): UserCollectionResponse
    {
        return $this->apiAdapter->getGraphServiceClient()->users()->get()->wait();
    }
}
