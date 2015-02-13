<?php
namespace Application\Library\ApiConnect;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client;
use Zend\Http\Request;

/**
 * Class ApiConnect
 * This class connects to an api
 */
class ApiConnect
{

    public	$url_auth   = "";
    public	$url_api    = "";
    public	$params     = [];
    public 	$client		= "";
    public  $response   = "";

    function connect( ) {

        /**
         * Set the auth URL and the LotteryDataApi url's
         */
        $url_auth = "http://your-api-location.com/oauth";
        $url_api  = "http://your-api-location/api/rpc/dosomething";


        /**
         * IMPORTANT
         *
         * Once you have a token, store this to a session [as well as the re-fresh token], then before you request a token on every call,
         * you simply re-use the session token. If it has expired, use the stored re-fresh token to re-set it...
         * Here is a basic session class: https://github.com/chateaux/Zf2SessionStorage
         *
         */

        //Run checks to check for a pre-existing token

        /**
         * Set our parameters that will be used in our headers
         */
        $auth_params = [
            'grant_type'    => 'client_credentials',
            'response_type' => 'code',
            'client_id'     => 'testclient',
            'client_secret' => 'testpass',
        ];

        /**
         * Instantiate the client
         */
        $auth_client = new Client(
            $url_auth,
            array(
                'maxredirects'	=> 0,
                'timeout'		=> 30)
        );

        /**
         * Set the headers
         */
        $auth_client->setHeaders(
            [
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
            ]
        );

        /**
         * Add the parameters to the clients
         */
        $auth_client->setParameterPost( $auth_params );
        $auth_client->setMethod('POST');

        /**
         * Instantiate and get a response
         */
        $response = $auth_client->send();

        /**
         * Put the response into an object
         */
        $responseObject = json_decode($response->getBody());

        /**
         * Credentials will look something like:

        object(stdClass)[245]
        public 'access_token' => string '7b6cfaf90d835d65ee179255a481e6db5f12b5df' (length=40)
        public 'expires_in' => int 3600
        public 'token_type' => string 'Bearer' (length=6)
        public 'scope' => null

         */

        $token = $responseObject->access_token;

        //Run some checks

        /**
         * Now use the returned credentials to access the API
         */

        $api_client = new Client(
            $url_api,
            array(
                'maxredirects'	=> 0,
                'timeout'		=> 30)
        );

        $api_client->setHeaders(
            [
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                'Authorization' => 'Bearer '.$token,
                'Accept' =>  'application/json'
            ]
        );

        $response = $api_client->send();

        /**
         * Put the response into an object
         */
        $responseArray = json_decode($response->getBody(),\Zend\Json\Json::TYPE_ARRAY);

        $success = $responseArray['data']['success'];
        $message = $responseArray['data']['reason'];
        $lotteryData = $responseArray['data']['specific-data'];

        if ($success) echo "success!<br/>";

        if (!$success) echo "Failed with response ".$message."<br/>";

        foreach ($specificData AS $data)
        {
            /**
             * Check if the required arrays exist
             */

            if ( ! isset( $data['some-data'] )  )
            {
                //Skip this iteration
                echo "Skipping due to missing games data...<br/>";
                continue;
            }

            if ( ! isset( $data['some-other-data'] )  )
            {
                //Skip this iteration
                echo "Skipping due to missing some other data...<br/>";
                //continue;
            }

            $games = $data['some data'];
            $games_parent = $data['some other data'];

            foreach ($games AS $game)
            {
                echo "Building... ".$game['some_name']." a game based on: ".$games_parent['some other daya']['name']."<br/>";
            }




        }

        die("complete");


    }




}

