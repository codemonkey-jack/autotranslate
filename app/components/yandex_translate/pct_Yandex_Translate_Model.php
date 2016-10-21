<?php

/**
 * Author: Cody Agent
 */
class pct_Yandex_Translate_Model extends phpapp_Option_Model {
	public $yandex_api;

	public function tbl_name() {
		return pct_instance()->prefix . 'yandex_translate';
	}

	public function rules() {
		return array(
			array( 'required', 'yandex_api' )
		);
	}

	public function addition_validate() {
		$test = $this->translate( 'hello', 'vi' );
		if ( $test['code'] != 200 ) {
			$this->set_error( 'yandex_api', __( 'There\' an error when validate your key: ', pct_instance()->domain ) . $test['message'] );
		}
	}

	function translate( $text, $lang ) {
		$url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $this->yandex_api . '&lang=' . $lang . '&text=' . rawurlencode( nl2br( $text ) );
		$ch  = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		$result = curl_exec( $ch );
		curl_close( $ch );
		$result = json_decode( $result, true );

		return $result;
	}
}