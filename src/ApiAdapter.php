<?php

namespace KignOrg\GraphApiAdapter;

use KignOrg\GraphApiAdapter\Secrets\Secrets;
use Microsoft\Graph\Beta\GraphRequestAdapter;
use Microsoft\Graph\Beta\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Kiota\Authentication\PhpLeagueAuthenticationProvider;

class ApiAdapter
{
    private Secrets $secrets;
    private PhpLeagueAuthenticationProvider $authProvider;
    private GraphRequestAdapter $requestAdapter;

    /**
     * @param Secrets $secrets
     */
    public function __construct(Secrets $secrets)
    {
        $this->secrets = $secrets;
        $this->initializeAuthProvider();
        $this->initializeRequestAdapter();
    }

    public function getGraphServiceClient(): GraphServiceClient
    {
        return new GraphServiceClient($this->requestAdapter);
    }

    private function initializeAuthProvider(): void
    {
        $tokenRequestContext = new ClientCredentialContext(
            $this->secrets->getTenantId(),
            $this->secrets->getClientId(),
            $this->secrets->getClientSecret()
        );
        $scopes = ['https://graph.microsoft.com/.default'];
        $this->authProvider = new PhpLeagueAuthenticationProvider($tokenRequestContext, $scopes);
    }

    private function initializeRequestAdapter(): void
    {
        $this->requestAdapter = new GraphRequestAdapter($this->authProvider);
    }

//    public function createCollectionRequest(string $requestType, string $endpoint): GraphCollectionRequest
//    {
//        $this->appClient->setAccessToken($this->accessToken);
//        return $this->appClient->createCollectionRequest($requestType, $endpoint);
//    }
}


