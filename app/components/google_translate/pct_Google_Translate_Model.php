<?php

/**
 * Author: Jack Kitterhing
 */
class pct_Google_Translate_Model extends phpapp_Option_Model {
	public $google_api;

	public function tbl_name() {
		return pct_instance()->prefix . 'google_translate';
	}

	public function rules() {
		return array(
			array( 'required', 'google_api' )
		);
	}

	public function addition_validate() {
		$test = $this->translate( 'hello', 'vi' );
		$test = json_decode( $test, true );
		if ( isset( $test['error'] ) ) {
			$this->set_error( 'google_api', __( 'There\' an error when validate your key: ', pct_instance()->domain ) . $test['error']['errors'][0]['reason'] );
		}
	}

	function translate( $text, $lang ) {
		$url = 'https://www.googleapis.com/language/translate/v2?';

		$url .= 'key=' . trim($this->google_api) . ( ! empty( $source ) ? '&source=' . $source : null ) . '&target=' . $lang . '&q=' . rawurlencode( nl2br( $text ) );
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		$result = curl_exec( $ch );
		curl_close( $ch );

		return $result;
	}
}