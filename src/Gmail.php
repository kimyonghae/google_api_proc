<?php
namespace GoogleApiProc;

use Google_Client;
use Google_Service_Gmail;
/**
 * Class Gmail
 * @package App\Http\Controllers\GoogleApi
 */
class Gmail
{
    protected $client;

    public function __construct()
    {
        $this->getClient();
    }

    public function getClient()
    {
        try
        {
            $client = new Google_Client();
            $client->setApplicationName('Gmail API PHP Quickstart');
            $client->setScopes(Google_Service_Gmail::MAIL_GOOGLE_COM);
            $client->setAccessType('offline');
            $credentials = env('GMAIL_CREDENTIALS');
            $client->setAuthConfig($credentials);

            // Load previously authorized credentials from a file.
            $credentialsPath = env('GMAIL_TOKEN');
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
