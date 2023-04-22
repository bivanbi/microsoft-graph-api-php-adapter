<?php

use KignOrg\GraphApiAdapter\ApiAdapter;
use KignOrg\GraphApiAdapter\Secrets\SecretsDotenvAdapter;
use KignOrg\GraphApiAdapter\UserAdapter;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

function initDotenv(string $directory): void
{
    $dotenv = Dotenv::createImmutable($directory);
    $dotenv->load();
    $dotenv->required(SecretsDotenvAdapter::getRequiredEnvVariables());
}

function listUsers(UserAdapter $userAdapter): void
{
    try {
        $users = $userAdapter->getUsers();
        foreach ($users->getValue() as $user) {
            print("User: {$user->getDisplayName()}\n");
            print("\tID: {$user->getId()}\n");
            print("\tprincipal: {$user->getUserPrincipalName()}\n");
            print("\n");
        }
        $moreAvailable = $users->getOdataNextLink() === null ? 'False' : 'True';
        print(PHP_EOL . 'More users available? ' . $moreAvailable . PHP_EOL . PHP_EOL);
    } catch (Throwable $e) {
        print(PHP_EOL . 'Error getting users: ' . $e->getMessage() . PHP_EOL . PHP_EOL);
    }
}

function main(): void
{
    initDotenv(__DIR__ . '/../');
    $secrets = new SecretsDotenvAdapter();
    $graphClientAdapter = new ApiAdapter($secrets);
    $userAdapter = new UserAdapter($graphClientAdapter);
    listUsers($userAdapter);
}

main();
