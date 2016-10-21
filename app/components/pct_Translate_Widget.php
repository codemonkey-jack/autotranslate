<?php

class pct_Translate_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname' => 'pct_Translate_Widget',
		);
		parent::__construct( 'pct_Translate_Widget', 'Translator Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		$look        = isset( $instance['look'] ) ? $instance['look'] : 'dropdown';
		$langs_tmp   = array_filter( explode( ',', pct_setting()->supported_languages ) );
		$langs_tmp   = array_map( 'trim', $langs_tmp );
		$lang_picker = '';

		if ( ! empty( $langs_tmp ) ) {
			$langs_tmp   = array_filter( explode( ',', pct_setting()->supported_languages ) );
			$langs_tmp   = array_map( 'trim', $langs_tmp );
			$lang_picker = '';
			if ( ! empty( $langs_tmp ) ) {
				$lang_picker = pct_instance()->render_view( 'picker/' . $look . '_all', array(
					'langs_tmp' => $langs_tmp,
					'no_pos'    => 1
				), true );
				echo $lang_picker;
			}
		}
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		$look  = ! empty( $instance['look'] ) ? $instance['look'] : 'drop_down';
		?>
		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'look' ) ); ?>"><?php _e( esc_attr( 'Display_as:' ) ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'look' ) ); ?>"
			        name="<?php echo esc_attr( $this->get_field_name( 'look' ) ); ?>">
				<option <?php selected( 'dropdown', $look ) ?>
					value="dropdown"><?php _e( "Drop Down", pct_instance()->domain ) ?></option>
				<option <?php selected( 'link', $look ) ?>
					value="link"><?php _e( "Link", pct_instance()->domain ) ?></option>

			</select>
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['look']  = ! empty( $new_instance['look'] ) ? $new_instance['look'] : 'dropdown';


		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'pct_Translate_Widget' );
} );