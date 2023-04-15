<?php

namespace KignOrg\GraphApiAdapter;

use GuzzleHttp\Exception\GuzzleException;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Http\GraphCollectionRequest;
use Microsoft\Graph\Model\User;

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
     * @throws GraphException
     * @throws GuzzleException
     */
    public function getUsers(): GraphCollectionRequest {
        // Only request specific properties
        $select = '$select=displayName,id,mail';
        // Sort by display name
        $orderBy = '$orderBy=displayName';

        $requestUrl = '/users?'.$select.'&'.$orderBy;
        return $this->apiAdapter->createCollectionRequest('GET', $requestUrl)
            ->setReturnType(User::class)
            ->setPageSize(25);
    }
}
