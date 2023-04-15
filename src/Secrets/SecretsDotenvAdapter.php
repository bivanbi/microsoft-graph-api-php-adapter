<?php

namespace KignOrg\GraphApiAdapter\Secrets;

class SecretsDotenvAdapter implements Secrets
{
    const NAMESPACE = 'GRAPH_SECRETS_';
    const ENV_TENANT_ID = self::NAMESPACE . 'TENANT_ID';
    const ENV_CLIENT_ID = self::NAMESPACE . 'CLIENT_ID';
    const ENV_CLIENT_SECRET = self::NAMESPACE . 'CLIENT_SECRET';

    public static function getRequiredEnvVariables(): array
    {
        return [self::ENV_TENANT_ID, self::ENV_CLIENT_ID, self::ENV_CLIENT_SECRET];
    }

    public function getTenantId()
    {
        return $_ENV[self::ENV_TENANT_ID];
    }

    public function getClientId()
    {
        return $_ENV[self::ENV_CLIENT_ID];
    }

    public function getClientSecret()
    {
        return $_ENV[self::ENV_CLIENT_SECRET];
    }
}
