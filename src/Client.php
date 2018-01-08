<?php
namespace BingDeCrapperWrapper;

use Microsoft\BingAds\Auth\ServiceClient;
use Microsoft\BingAds\Auth\ApiEnvironment;
use Microsoft\BingAds\Auth\AuthorizationData;
use Microsoft\BingAds\Auth\ServiceClientType;
use Microsoft\BingAds\Auth\OAuthAuthorization;
use Microsoft\BingAds\Auth\OAuthWebAuthCodeGrant;
use Microsoft\BingAds\Auth\PasswordAuthentication;
use Microsoft\BingAds\Auth\OAuthDesktopMobileAuthCodeGrant;
use Microsoft\BingAds\Auth\OAuthDesktopMobileImplicitGrant;

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
        string $clientId,
        string $clientSecret,
        string $refreshToken,
        string $developerToken,
        string $service = null
    ) {
        $service = $service ?? ServiceClientType::ReportingVersion11;

        $authentication = (new OAuthDesktopMobileAuthCodeGrant())
            ->withClientId($clientId)
            ->withClientSecret($clientSecret);

        $authentication = (new AuthorizationData())
            ->withAuthentication($authentication)
            ->withDeveloperToken($developerToken);

        $authentication->Authentication->RequestOAuthTokensByRefreshToken($refreshToken);

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
