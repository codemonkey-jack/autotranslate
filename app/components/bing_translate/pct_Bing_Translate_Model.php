<?php

/**
 * Author: Cody Agent
 */
class pct_Bing_Translate_Model extends phpapp_Option_Model {
	public $client_secret;
	public $client_id;

	public function tbl_name() {
		return pct_instance()->prefix . 'bing_translate';
	}

	public function rules() {
		return array(
			array( 'required', 'client_secret,client_id' )
		);
	}

	public function addition_validate() {
		$test = $this->translate( 'hello', 'vi' );
		if($test['status']==false){
			$this->set_error( 'client_id', __( 'There\' an error when validate your key: ', pct_instance()->domain ) . $test['error']);
		}
	}

	function translate( $text, $lang ) {
		try {
			//Client ID of the application.
			$clientID = $this->client_id;
			//Client Secret key of the application.
			$clientSecret = $this->client_secret;
			//OAuth Url.
			$authUrl = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
			//Application Scope Url
			$scopeUrl = "http://api.microsofttranslator.com";
			//Application grant type
			$grantType = "client_credentials";

			//Create the AccessTokenAuthentication object.
			$authObj = new AccessTokenAuthentication();
			//Get the Access token.
			$accessToken = $authObj->getTokens( $grantType, $scopeUrl, $clientID, $clientSecret, $authUrl );
			//Create the authorization Header string.
			$authHeader = "Authorization: Bearer " . $accessToken;

			//Create the Translator Object.
			$translatorObj = new HTTPTranslator();

			//Input String.
			$inputStr = $text;
			//HTTP Detect Method URL.
			$detectMethodUrl = "http://api.microsofttranslator.com/V2/Http.svc/Translate?text=" . urlencode( $inputStr ).'&to='.$lang;
			//Call the curlRequest.
			$strResponse = $translatorObj->curlRequest( $detectMethodUrl, $authHeader );
			//Interprets a string of XML into an object.
			$xmlObj = (array)simplexml_load_string( $strResponse );
			if ( !isset( $xmlObj['body']) ) {
				return array(
					'status'     => true,
					'translated' => reset($xmlObj)
				);
			} else {
				return array(
					'status' => false,
					'error'  => @implode( ' ',$xmlObj['body']->p)
				);
			}

		} catch ( Exception $e ) {
			echo "Exception: " . $e->getMessage() . PHP_EOL;
		}

	}
}