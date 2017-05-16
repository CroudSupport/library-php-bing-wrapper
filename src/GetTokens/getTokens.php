<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Microsoft\BingAds\Auth\OAuthWebAuthCodeGrant;

class GetTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bing:getTokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get access token and refresh token via bing oauth.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $clientId = $this->ask('What is your client ID');
        $clientSecret = $this->ask('What is your client Secret');

        $authentication = (new OAuthWebAuthCodeGrant())
            ->withClientId($clientId)
            ->withClientSecret($clientSecret)
            ->withRedirectUri('https://login.live.com/oauth20_desktop.srf')
            ->withState(rand(0,999999999));

        $this->info('Navigate to the following URL grant permissions then past in URL from the address bar.');
        $callBackUrl = $this->ask($authentication->GetAuthorizationEndpoint());


        $authentication->RequestOAuthTokensByResponseUri($callBackUrl);

        $this->info('Access Token: ' . $authentication->OAuthTokens->AccessToken);
        $this->info('Refresh Token: ' . $authentication->OAuthTokens->RefreshToken);
    }
}
