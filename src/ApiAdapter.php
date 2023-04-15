<?php

namespace KignOrg\GraphApiAdapter;

use KignOrg\GraphApiAdapter\Secrets\Secrets;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphCollectionRequest;

class ApiAdapter
{
    private Secrets $secrets;
    private string $appToken;
    private Client $tokenClient;
    private Graph $appClient;

    /**
     * @param Secrets $secrets
     */
    public function __construct(Secrets $secrets)
    {
        $this->secrets = $secrets;
        $this->tokenClient = new Client();
        $this->appClient = new Graph();
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function getAppOnlyToken(): string {
        // If we already have a token, just return it
        // Tokens are valid for one hour, after that a new token needs to be
        // requested
        if (isset($this->appToken)) {
            return $this->appToken;
        }

        // https://learn.microsoft.com/azure/active-directory/develop/v2-oauth2-client-creds-grant-flow
        $tokenRequestUrl = 'https://login.microsoftonline.com/'.$this->secrets->getTenantId().'/oauth2/v2.0/token';

        // POST to the /token endpoint
        $tokenResponse = $this->tokenClient->post($tokenRequestUrl, [
            'form_params' => [
                'client_id' => $this->secrets->getClientId(),
                'client_secret' => $this->secrets->getClientSecret(),
                'grant_type' => 'client_credentials',
                'scope' => 'https://graph.microsoft.com/.default'
            ],
            // These options are needed to enable getting
            // the response body from a 4xx response
            'http_errors' => false,
            'curl' => [
                CURLOPT_FAILONERROR => false
            ]
        ]);

        $responseBody = json_decode($tokenResponse->getBody()->getContents());
        if ($tokenResponse->getStatusCode() == 200) {
            // Return the access token
            $this->appToken = $responseBody->access_token;
            return $responseBody->access_token;
        } else {
            $error = $responseBody->error ?? $tokenResponse->getStatusCode();
            throw new Exception('Token endpoint returned '.$error, 100);
        }
    }

    /**
     * @throws GraphException
     * @throws GuzzleException
     */
    public function createCollectionRequest(string $requestType, string $endpoint): GraphCollectionRequest
    {
        $this->applyToken();
        return $this->appClient->createCollectionRequest($requestType, $endpoint);
    }

    /**
     * @throws GuzzleException
     */
    private function applyToken(): void
    {
        $token = $this->getAppOnlyToken();
        $this->appClient->setAccessToken($token);
    }
}


