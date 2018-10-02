<?php
namespace GoogleApiProc;

use Google_Client;
use Google_Service_Directory;
/**
 * Class GSuiteAdmin
 * @package App\Http\Controllers\GoogleApi
 */
class GSuiteAdmin
{
    protected $client;

    /**
     * GSuiteAdmin constructor.
     */
    public function __construct()
    {
        $this->getClient();
    }

    /**
     * @return Google_Client
     */
    public function getClient()
    {
        try
        {
            $client = new Google_Client();
            $client->setApplicationName('G Suite Directory API PHP Quickstart');
            $client->setScopes(Google_Service_Directory::ADMIN_DIRECTORY_USER);
            $credentials = Config::get('credentials.GSUITE_CREDENTIALS');
            $client->setAuthConfig($credentials);
            $client->setAccessType('offline');
            $client->setPrompt('select_account consent');

            // Load previously authorized credentials from a file.
            $credentialsPath = Config::get('credentials.GSUITE_TOKEN');
            if (file_exists($credentialsPath)) {
                $accessToken = json_decode(file_get_contents($credentialsPath), true);
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }

                // Store the credentials to disk.
                if (!file_exists(dirname($credentialsPath))) {
                    mkdir(dirname($credentialsPath), 0700, true);
                }
                file_put_contents($credentialsPath, json_encode($accessToken));
                printf("Credentials saved to %s\n", $credentialsPath);
            }
            $client->setAccessToken($accessToken);

            // Refresh the token if it's expired.
            if ($client->isAccessTokenExpired()) {
                // save refresh token to some variable
                $refreshTokenSaved = $client->getRefreshToken();
                // update access token
                $client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
                // pass access token to some variable
                $accessTokenUpdated = $client->getAccessToken();
                // append refresh token
                $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;
                // save to file
                file_put_contents($credentialsPath, json_encode($accessTokenUpdated));
            }
            return $this->client = $client;
        }
        catch (Exception $e)
        {
            echo 'An error occurred: ' . $e->getMessage();
        }
    }
}
