<?php

/*
Plugin Name: AutoTranslate
Plugin URI: #
Description: Connect to Bing Translate, Google Translate or Yandex translate and automatically translate your site. 
Author: WPSatchel
Version: 1.0
Author URI: #
*/

class Auto_Translator {
	/**
	 * URL to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_url = '';

	/**
	 * Path to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_path = '';

	/**
	 * @var string
	 * The google place api for using to queury info
	 */
	public $prefix = '';

	/**
	 * @var string
	 */
	public $domain = '';
	/**
	 * @var array
	 */
	public $global = array();

	public function __construct() {
		$this->init();
		//load domain
		add_action( 'plugins_loaded', array( &$this, 'localization' ) );
		//autoload
		spl_autoload_register( array( &$this, 'autoload' ) );
		//assets
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_script' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'script' ) );
	}

	public function script() {
		wp_register_style( $this->prefix . 'style', $this->plugin_url . 'assets/style.css' );
	}

	public function admin_script() {
		wp_enqueue_style( $this->prefix . 'admin_style', $this->plugin_url . '/assets/admin.css' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
	}

	function get_languages() {
		return array(
			'en' => 'English',
			'aa' => 'Afar',
			'ab' => 'Abkhazian',
			'af' => 'Afrikaans',
			'am' => 'Amharic',
			'ar' => 'Arabic',
			'as' => 'Assamese',
			'ay' => 'Aymara',
			'az' => 'Azerbaijani',
			'ba' => 'Bashkir',
			'be' => 'Byelorussian',
			'bg' => 'Bulgarian',
			'bh' => 'Bihari',
			'bi' => 'Bislama',
			'bn' => 'Bengali/Bangla',
			'bo' => 'Tibetan',
			'br' => 'Breton',
			'ca' => 'Catalan',
			'co' => 'Corsican',
			'cs' => 'Czech',
			'cy' => 'Welsh',
			'da' => 'Danish',
			'de' => 'German',
			'dz' => 'Bhutani',
			'el' => 'Greek',
			'eo' => 'Esperanto',
			'es' => 'Spanish',
			'et' => 'Estonian',
			'eu' => 'Basque',
			'fa' => 'Persian',
			'fi' => 'Finnish',
			'fj' => 'Fiji',
			'fo' => 'Faeroese',
			'fr' => 'French',
			'fy' => 'Frisian',
			'ga' => 'Irish',
			'gd' => 'Scots/Gaelic',
			'gl' => 'Galician',
			'gn' => 'Guarani',
			'gu' => 'Gujarati',
			'ha' => 'Hausa',
			'hi' => 'Hindi',
			'hr' => 'Croatian',
			'hu' => 'Hungarian',
			'hy' => 'Armenian',
			'ia' => 'Interlingua',
			'ie' => 'Interlingue',
			'ik' => 'Inupiak',
			'in' => 'Indonesian',
			'is' => 'Icelandic',
			'it' => 'Italian',
			'iw' => 'Hebrew',
			'ja' => 'Japanese',
			'ji' => 'Yiddish',
			'jw' => 'Javanese',
			'ka' => 'Georgian',
			'kk' => 'Kazakh',
			'kl' => 'Greenlandic',
			'km' => 'Cambodian',
			'kn' => 'Kannada',
			'ko' => 'Korean',
			'ks' => 'Kashmiri',
			'ku' => 'Kurdish',
			'ky' => 'Kirghiz',
			'la' => 'Latin',
			'ln' => 'Lingala',
			'lo' => 'Laothian',
			'lt' => 'Lithuanian',
			'lv' => 'Latvian/Lettish',
			'mg' => 'Malagasy',
			'mi' => 'Maori',
			'mk' => 'Macedonian',
			'ml' => 'Malayalam',
			'mn' => 'Mongolian',
			'mo' => 'Moldavian',
			'mr' => 'Marathi',
			'ms' => 'Malay',
			'mt' => 'Maltese',
			'my' => 'Burmese',
			'na' => 'Nauru',
			'ne' => 'Nepali',
			'nl' => 'Dutch',
			'no' => 'Norwegian',
			'oc' => 'Occitan',
			'om' => '(Afan)/Oromoor/Oriya',
			'pa' => 'Punjabi',
			'pl' => 'Polish',
			'ps' => 'Pashto/Pushto',
			'pt' => 'Portuguese',
			'qu' => 'Quechua',
			'rm' => 'Rhaeto-Romance',
			'rn' => 'Kirundi',
			'ro' => 'Romanian',
			'ru' => 'Russian',
			'rw' => 'Kinyarwanda',
			'sa' => 'Sanskrit',
			'sd' => 'Sindhi',
			'sg' => 'Sangro',
			'sh' => 'Serbo-Croatian',
			'si' => 'Singhalese',
			'sk' => 'Slovak',
			'sl' => 'Slovenian',
			'sm' => 'Samoan',
			'sn' => 'Shona',
			'so' => 'Somali',
			'sq' => 'Albanian',
			'sr' => 'Serbian',
			'ss' => 'Siswati',
			'st' => 'Sesotho',
			'su' => 'Sundanese',
			'sv' => 'Swedish',
			'sw' => 'Swahili',
			'ta' => 'Tamil',
			'te' => 'Tegulu',
			'tg' => 'Tajik',
			'th' => 'Thai',
			'ti' => 'Tigrinya',
			'tk' => 'Turkmen',
			'tl' => 'Tagalog',
			'tn' => 'Setswana',
			'to' => 'Tonga',
			'tr' => 'Turkish',
			'ts' => 'Tsonga',
			'tt' => 'Tatar',
			'tw' => 'Twi',
			'uk' => 'Ukrainian',
			'ur' => 'Urdu',
			'uz' => 'Uzbek',
			'vi' => 'Vietnamese',
			'vo' => 'Volapuk',
			'wo' => 'Wolof',
			'xh' => 'Xhosa',
			'yo' => 'Yoruba',
			'zh' => 'Chinese',
			'zu' => 'Zulu',
		);;
	}

	public function load_controllers() {
		$path  = $this->plugin_path . 'app/controllers';
		$files = glob( $path . '/*.php' );
		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				include_once $file;
			}
		}
	}

	public function load_components() {
		$path  = $this->plugin_path . 'app/components';
		$files = glob( $path . '/*.php' );
		foreach ( $files as $file ) {
			if ( file_exists( $file ) ) {
				include_once $file;
			}
		}
	}

	public function load_engine() {
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX == true ) ) {
			$engine = pct_setting()->engine;
			if ( ! empty( $engine ) ) {
				new $engine;
			}
		} elseif ( is_admin() ) {
			$engines = apply_filters( 'pct_translate_engine', array() );
			foreach ( $engines as $key => $e ) {
				new $key;
			}
		}
	}


	//----------------------------------------------------------------------------------------------------------------------------//

	public function init() {
		//some defined
		$this->plugin_url  = plugins_url( '/', __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->prefix      = 'pct_';
		$this->domain      = 'pct_comment_translator';

		require_once $this->plugin_path . 'app/components/pct_Translate_Widget.php';
	}

	public function localization() {
		load_plugin_textdomain( $this->domain, false, '/phpapp_comment_translator/languages/' );
	}

	public function autoload( $classname ) {
		$path = '';
		//$core_path = dirname(plugin_dir_path(__FILE__)).'/phpapp_core';
		$core_path = $this->plugin_path;
		$vendors   = array(
			'phpapp_Form'         => $core_path . 'vendors/form/phpapp_Form.php',
			'phpapp_Active_Form'  => $core_path . 'vendors/form/phpapp_Active_Form.php',
			'phpapp_Model'        => $core_path . 'vendors/phpapp_Model.php',
			'phpapp_Option_Model' => $core_path . 'vendors/phpapp_Option_Model.php',
			'phpapp_Controller'   => $core_path . 'vendors/phpapp_Controller.php'
		);
		if ( isset( $vendors[ $classname ] ) ) {
			$path = $vendors[ $classname ];
			if ( file_exists( $path ) ) {
				include $path;

				return;
			}
		}

		$lower = strtolower( $classname );
		//first we need to check the prefix to prevent conflict
		if ( strpos( $lower, $this->prefix ) === 0 ) {
			if ( stristr( $lower, 'controller' ) ) {
				$path = $this->plugin_path . 'app/controllers/' . $classname . '.php';
			} elseif ( stristr( $lower, 'model' ) || stristr( $lower, 'table' ) ) {
				$path = $this->plugin_path . 'app/models/' . $classname . '.php';
			}
		}
		if ( file_exists( $path ) ) {
			include $path;
		}
	}

	public function render_view( $view, $args = array(), $return = false ) {
		$path = $this->plugin_path . 'app/views/' . $view . '.php';
		if ( file_exists( $path ) ) {
			ob_start();
			extract( $args );
			include $path;
			$content = ob_get_contents();
			ob_end_clean();
			if ( ! empty( $this->layout ) ) {
				$layout_path = $this->plugin_path . 'app/views/layouts/' . $this->layout . '.php';
				if ( file_exists( $layout_path ) ) {
					ob_start();
					include $layout_path;
					ob_end_flush();
				} else {
					throw new Exception( 'Layout not found!' );
				}
			}

			if ( $return == false ) {
				echo $content;
			}

			return $content;
		}
	}
}

global $auto_translator;
$auto_translator = new Auto_Translator();
function pct_instance() {
	global $auto_translator;

	return $auto_translator;
}

function pct_setting() {
	$setting = new pct_Setting_Model();

	return $setting;
}

$auto_translator->load_controllers();
$auto_translator->load_components();
$auto_translator->load_engine();