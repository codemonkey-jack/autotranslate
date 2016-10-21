<?php

/**
 * Author: Jack Kitterhing
 */
class pct_Setting_Controller {

	public function __construct() {
		//menu
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'parse_request' ) );
	}

	public function admin_menu() {
		add_management_page( __( 'Comment Translator', pct_instance()->domain ), __( 'Comment Translator', pct_instance()->domain ), 'manage_options', pct_instance()->prefix . 'setting', array(
			&$this,
			'main_setting_screen'
		) );
	}

	public function parse_request() {
		if ( isset( $_POST[ pct_instance()->prefix . 'setting' ] ) ) {
			$model = new pct_Setting_Model();
			if ( isset( $_POST['supported_languages'] ) ) {
				$model->supported_languages = implode( ',', $_POST['supported_languages'] );
			}
			$model->import( $_POST['pct_Setting_Model'] );
			if ( $model->validate() ) {
				$model->save();
				$is_done = apply_filters( 'pct_settings_saved', true, $model );
				if ( $is_done ) {
					wp_redirect( admin_url( 'tools.php?page=' . pct_instance()->prefix . 'setting' ) );
				}
			} else {
				pct_instance()->global['model'] = $model;
			}
		}
	}

	public function main_setting_screen() {
		wp_enqueue_script( 'pct-tag-it', pct_instance()->plugin_url . 'assets/tag-it/js/tag-it.min.js' );
		wp_enqueue_script( 'pct-cm', pct_instance()->plugin_url . 'assets/code-minor/codemirror.js' );
		wp_enqueue_script( 'pct-css', pct_instance()->plugin_url . 'assets/code-minor/css/css.js' );
		wp_enqueue_script( 'pct-showhint', pct_instance()->plugin_url . 'assets/code-minor/hint/show-hint.js' );
		wp_enqueue_script( 'pct-csshint', pct_instance()->plugin_url . 'assets/code-minor/hint/css-hint.js' );
		wp_enqueue_style( 'pct-cm', pct_instance()->plugin_url . 'assets/code-minor/codemirror.css' );
		wp_enqueue_style( 'pct-showhint', pct_instance()->plugin_url . 'assets/code-minor/hint/show-hint.css' );
		//wp_enqueue_style( 'pct-jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css' );
		wp_enqueue_style( 'pct-tag-it', pct_instance()->plugin_url . 'assets/tag-it/css/tagit.ui-zendesk.css' );
		$model = null;

		if ( isset( pct_instance()->global['model'] ) ) {
			$model = pct_instance()->global['model'];
		}
		if ( ! $model instanceof pct_Setting_Model ) {
			$model = new pct_Setting_Model();
		}
		pct_instance()->render_view( 'settings', array(
			'model' => $model
		) );
	}
}

new pct_Setting_Controller();