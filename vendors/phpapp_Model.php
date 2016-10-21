<?php

/**
 * Author: Cody Agent
 */
if ( ! class_exists( 'phpapp_Model' ) ) {
	class phpapp_Model {
		/**
		 * @var array
		 */
		private $errors = array();
		/**
		 * @var
		 */
		private static $instance;

		/**
		 * @var array
		 */

		public function tbl_name() {

		}

		public function rules() {
			return array();
		}

		public function generate_model_form( $fields, $action = '#', $method = 'POST', $html_options = array() ) {
			$form = phpapp_Form::createForm( $action, $method, $html_options );
			$form .= '<table class="form-table">';
			foreach ( $fields as $field ) {
				if ( $field['type'] != 'hidden' ) {
					$form .= '<tr>';
					$form .= '<th scope="row">';
					if ( isset( $field['label'] ) ) {
						$form .= $field['label'];
					} else {
						$form .= ucwords( str_replace( '_', ' ', $field['name'] ) );
					}
					$form .= '</th>';
				}

				switch ( $field['type'] ) {
					case 'textbox':
						$form .= '<td>' . phpapp_Form::textField( $field['name'], $field['value'], isset( $field['html_options'] ) ? $field['html_options'] : array() );
						if ( isset( $this->errors[$field['name']] ) ) {
							$form .= '<p class="pap_error_msg">';
							$form .= implode( '<br/>', $this->errors[$field['name']] );
							$form .= '</p>';
						}
						$form .= '</td>';
						break;
					case 'password':
						$form .= '<td>' . phpapp_Form::passWordField( $field['name'], $field['value'], isset( $field['html_options'] ) ? $field['html_options'] : array() );
						if ( isset( $this->errors[$field['name']] ) ) {
							$form .= '<p class="pap_error_msg">';
							$form .= implode( '<br/>', $this->errors[$field['name']] );
							$form .= '</p>';
						}
						$form .= '</td>';
						break;
					case 'hidden':
						$form .= phpapp_Form::hiddenField( $field['name'], $field['value'], isset( $field['html_options'] ) ? $field['html_options'] : array() );
						break;
					case 'dropdown':
						$form .= '<td>' . phpapp_Form::dropDownList( $field['name'], $field['value'], $field['data'], isset( $field['html_options'] ) ? $field['html_options'] : array() );
						if ( isset( $this->errors[$field['name']] ) ) {
							$form .= '<p class="pap_error_msg">';
							$form .= implode( '<br/>', $this->errors[$field['name']] );
							$form .= '</p>';
						}
						$form .= '</td>';
						break;
					case 'textarea':
						$form .= '<td>' . phpapp_Form::textArea( $field['name'], $field['value'], isset( $field['html_options'] ) ? $field['html_options'] : array() );
						if ( isset( $this->errors[$field['name']] ) ) {
							$form .= '<p class="pap_error_msg">';
							$form .= implode( '<br/>', $this->errors[$field['name']] );
							$form .= '</p>';
						}
						$form .= '</td>';
						break;
					case 'checkbox':
						$form .= '<td>' . phpapp_Form::checkBox( $field['name'], $field['value'], isset( $field['html_options'] ) ? $field['html_options'] : array() );
						if ( isset( $this->errors[$field['name']] ) ) {
							$form .= '<p class="pap_error_msg">';
							$form .= implode( '<br/>', $this->errors[$field['name']] );
							$form .= '</p>';
						}
						$form .= '</td>';
						break;
				}
				if ( $field['type'] != 'hidden' ) {
					$form .= '</tr>';
				}
			}
			$form .= '</table>';
			$form .= '<p class="submit"><button type="submit" class="button button-primary">' . __( 'Save Changes', anp_instance()->domain ) . '</button> </p>';
			$form .= phpapp_Form::endForm();

			return $form;
		}

		public function before_save() {
		}

		public function after_save() {
		}

		public function import( $data = array() ) {
			foreach ( $data as $key => $val ) {
				if ( property_exists( $this, $key ) ) {
					$this->$key = $val;
				}
			}
		}

		public function export() {
			$data      = array();
			$ref_class = new ReflectionClass( get_called_class() );
			foreach ( $ref_class->getProperties() as $prop ) {
				if ( $prop->class == get_called_class() ) {
					$data[$prop->name] = $this->{$prop->name};
				}
			}

			return $data;
		}

		public function addition_validate() {
			return null;
		}

		public function validate() {
			$error    = true;
			$built_in = array(
				'required', 'numeric', 'email', 'compare', 'length'
			);
			foreach ( $this->rules() as $rule ) {
				$rule_name = $rule[0];
				$fields    = explode( ',', $rule[1] );
				if ( in_array( $rule_name, $built_in ) ) {
					foreach ( $fields as $field ) {
						switch ( $rule_name ) {
							case 'required':
								if ( empty( $this->$field ) ) {
									if ( ! isset( $this->errors[$field] ) ) {
										$this->errors[$field] = 'Field <strong>' . ucwords( str_replace( '_', ' ', $field ) ) . '</strong> is required!';
									}
								}
								break;
							case 'numeric':
								if ( ! isset( $this->errors[$field] ) ) {
									if ( ! filter_var( $this->$field, FILTER_VALIDATE_FLOAT ) ) {
										$this->errors[$field] = 'Field <strong>' . ucwords( str_replace( '_', ' ', $field ) ) . '</strong> must be a number!';
									}
								}
								break;
							case 'email':
								if ( ! isset( $this->errors[$field] ) ) {
									if ( ! filter_var( $this->$field, FILTER_VALIDATE_EMAIL ) ) {
										$this->errors[$field] = 'Field <strong>' . ucwords( str_replace( '_', ' ', $field ) ) . '</strong> not a valid email!';
									}
								}
								break;
							case 'compare':
								if ( ! isset( $this->errors[$field] ) ) {
									if ( isset( $rule['to'] ) ) {
										$compare = $rule['to'];
										if ( $this->$field != $compare ) {
											$this->errors[$field] = 'Field <strong>' . ucwords( str_replace( '_', ' ', $field ) ) . '</strong> not match!';
										}
									} elseif ( isset( $rule['not_in'] ) ) {
										$not_in = $rule['not_in'];
										if ( ! is_array( $not_in ) ) {
											$not_in = array();
										}
										if ( in_array( $this->$field, $not_in ) ) {
											$this->errors[$field] = 'Field <strong>' . ucwords( str_replace( '_', ' ', $field ) ) . '</strong> should not be ' . implode( ',', $not_in ) . '!';
										}
									}
								}
								break;
							case 'length':
								if ( ! isset( $this->errors[$field] ) ) {
									if ( isset( $rule['min'] ) ) {
										$min = $rule['min'];
										if ( strlen( $this->$field ) < $min ) {
											$this->errors[$field] = 'Field <strong>' . ucwords( str_replace( '_', ' ', $field ) ) . '</strong> minimum length is ' . $min . '!';
										}
									} elseif ( isset( $rule['max'] ) ) {
										$max = $rule['max'];
										if ( strlen( $this->$field ) > $max ) {
											$this->errors[$field] = 'Field <strong>' . ucwords( str_replace( '_', ' ', $field ) ) . '</strong> maximum length is ' . $max . '!';
										}
									}
								}
								break;
						}
					}
				}
			}

			if ( empty( $this->errors ) ) {
				$addition_validate = $this->addition_validate();
			}

			return empty( $this->errors ) ? true : false;
		}

		public function is_new_record() {
			$id = $this->id;
			if ( ! empty( $id ) ) {
				$model = $this->load( $id );
				if ( is_object( $model ) ) {
					return false;
				}
			} else {
				return true;
			}

			return true;
		}

		public function load( $id ) {
			global $wpdb;
			$sql = "SELECT * FROM " . $wpdb->prefix . $this->tbl_name() . ' WHERE id=%d';
			$row = $wpdb->get_row( $wpdb->prepare( $sql, $id ) );

			if ( ! empty( $row ) ) {
				return $this->fetch_model( $row );
			} else {
				return null;
			}

		}

		public function find_one( $query = null, $params = array() ) {
			global $wpdb;
			$sql = "SELECT * FROM " . $wpdb->prefix . $this->tbl_name();

			if ( ! empty( $query ) ) {
				$sql .= ' WHERE ' . $query;
			}
			$row = $wpdb->get_row( $wpdb->prepare( $sql, $params ) );

			if ( ! empty( $row ) ) {
				return $this->fetch_model( $row );
			} else {
				return null;
			}
		}

		public function find_all( $query = null, $params = array(), $order = '', $order_by = '', $limit = '' ) {
			global $wpdb;
			$sql = "SELECT * FROM " . $wpdb->prefix . $this->tbl_name();
			if ( ! empty( $query ) ) {
				$sql .= ' WHERE ' . $query;
			}

			if ( ! empty( $order_by ) ) {
				$sql .= 'ORDER BY ' . $order_by . trim( ' ' . $order );
			}

			if ( ! empty( $limit ) ) {
				$sql .= ' LIMIT ' . $limit;
			}

			$results = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
			$models  = array();
			if ( ! empty( $results ) && is_array( $results ) ) {
				$class = get_called_class();
				foreach ( $results as $row ) {
					$models[] = $this->fetch_model( $row );
				}
			}

			return $models;
		}


		protected function fetch_model( $data ) {
			$class = get_called_class();
			$model = new $class();
			$model->import( $data );

			return $model;
		}

		public function save() {
			global $wpdb;
			$this->before_save();
			$wpdb->insert( $wpdb->prefix . $this->tbl_name(), $this->export() );
			$this->after_save();
			$this->id = $wpdb->insert_id;

			return true;
		}

		public function update() {
			global $wpdb;
			$this->before_save();
			$wpdb->update( $wpdb->prefix . $this->tbl_name(), $this->export(), array(
				'id' => $this->id
			) );
			$this->after_save();

			return true;
		}

		public function delete() {
			global $wpdb;
			$wpdb->delete( $wpdb->prefix . $this->tbl_name(), array(
				'id' => $this->id
			) );
		}

		public static function get_instance() {
			$class = get_called_class();

			return new $class;
			/*if ( ! is_object( self::$instance ) ) {
				self::$instance = new $class;
			}

			return self::$instance;*/
		}

		public function generate_form_html() {
			$properties = $this->export();
			$form       = '<table class="form-table">';
			foreach ( $properties as $key => $property ) {
				$form .= '<tr>';
				$form .= '<th scope="row"><?php _e("' . ucwords( str_replace( '_', ' ', $key ) ) . '","domain") ?></th>';
				$form .= '<td><?php $form->textField($model,"' . $key . '") ?></td>';
				$form .= '</tr>';
			}
			$form .= '</table>';
			file_put_contents( anp_instance()->plugin_path . 'runtimes/' . get_called_class() . '.php', $form );
		}

		public function has_error( $attribute ) {
			if ( isset( $this->errors[$attribute] ) ) {
				return true;
			}

			return false;
		}

		public function set_error( $field, $error ) {
			$this->errors[$field] = $error;
		}

		public function get_error( $attribute ) {
			$error = '<p class="pap_error_msg">';
			$error .= $this->errors[$attribute];
			$error .= '</p>';

			return $error;
		}

		public function get_errors() {
			return $this->errors;
		}
	}
}