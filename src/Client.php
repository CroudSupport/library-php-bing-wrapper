<?php
namespace BingDeCrapperWrapper;

use Microsoft\BingAds\Auth\ApiEnvironment;
use Microsoft\BingAds\Auth\AuthorizationData;
use Microsoft\BingAds\Auth\PasswordAuthentication;
use Microsoft\BingAds\Auth\ServiceClient;

class Client
{
    /**
     * @var ServiceClient
     */
    private $client;

    /**
     * @param string $userName
     * @param string $password
     * @param string $developerToken
     * @param string $service
     */
    public function __construct(
        string $userName,
        string $password,
        string $developerToken,
        string $service
    ) {
        $passwordAuthentication = new PasswordAuthentication();
        $passwordAuthentication->withUserName($userName);
        $passwordAuthentication->withPassword($password);

        $authentication = new AuthorizationData();
        $authentication->withAuthentication($passwordAuthentication);
        $authentication->withDeveloperToken($developerToken);

        $this->client = new ServiceClient($service, $authentication, ApiEnvironment::Production);
    }

    /**
     * @return ServiceClient
     */
    public function getClient(): ServiceClient
    {
        return $this->client;
    }
}
