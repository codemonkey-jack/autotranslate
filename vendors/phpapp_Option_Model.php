<?php

/**
 * Author: Jack Kitterhing
 */
if ( ! class_exists( 'phpapp_Option_Model' ) ) {
	class phpapp_Option_Model extends phpapp_Model {
		public function __construct( $multisite = false ) {
			if ( $multisite ) {
				$data = get_site_option( $this->tbl_name() );
			} else {
				$data = get_option( $this->tbl_name() );
			}
			if ( is_array( $data ) && count( $data ) ) {
				$this->import( $data );
			}
		}

		public function save( $multisite = false ) {
			$this->before_save();
			$data = $this->export();
			if ( $multisite == false ) {
				update_option( $this->tbl_name(), $data );
			} else {
				update_site_option( $this->tbl_name(), $data );
			}
			$this->after_save();
		}

		public function is_new_record( $multisite = false ) {
			if ( $multisite ) {
				$data = get_site_option( $this->tbl_name() );
			} else {
				$data = get_option( $this->tbl_name() );
			}
			if ( is_array( $data ) && count( $data ) ) {
				return false;
			}

			return true;
		}
	}
}