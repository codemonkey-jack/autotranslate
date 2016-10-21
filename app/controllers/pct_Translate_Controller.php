<?php

/**
 * Author: Jack Kitterhing
 */
class pct_Translate_Controller {
	private $is_showed = false;
	private $is_page_showed = false;

	public function __construct() {
		//append translate dropdown
		add_filter( 'get_comment_text', array( &$this, 'append_dropdown_comment' ), 10, 3 );
		add_action( 'wp_footer', array( &$this, 'footer_scripts' ), 50 );
		add_action( 'wp_ajax_nopriv_' . pct_instance()->prefix . 'translate', array( &$this, 'do_translate' ) );
		add_action( 'wp_ajax_' . pct_instance()->prefix . 'translate', array( &$this, 'do_translate' ) );
		add_action( 'wp_ajax_nopriv_pct_reset', array( &$this, 'reset' ) );
		add_action( 'wp_ajax_pct_reset', array( &$this, 'reset' ) );
		add_filter( 'the_content', array( &$this, 'append_dropdown_content' ) );
	}

	function reset() {
		$type = isset( $_POST['t'] ) ? $_POST['t'] : 'comment';
		$id   = isset( $_POST['id'] ) ? $_POST['id'] : false;
		if ( $id !== false ) {
			if ( $type == 'comment' ) {
				$obj = get_comment( $id );
				if ( is_object( $obj ) ) {
					echo $obj->comment_content;
				}
			} else {
				$obj = get_post( $id );
				if ( is_object( $obj ) ) {
					echo $obj->post_content;
				}
			}
		}
		die;
	}

	function do_translate() {
		if ( isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], pct_instance()->prefix . 'translate' ) ) {
			//$result = $this->translate( $_POST['comment_id'], $_POST['lang'] );
			$type   = isset( $_POST['type'] ) ? $_POST['type'] : 'comment';
			$result = apply_filters( 'pct_do_translate', $_POST['comment_id'], $_POST['lang'], $type );
			if ( $result['status'] == false ) {
				echo json_encode( array(
					'status' => 0,
					'msg'    => __( $result['error_message'], pct_instance()->domain )
				) );
				exit;
			}

			echo json_encode( array(
				'status' => 1,
				'msg'    => stripslashes( $result['translated'] )
			) );
		}
		exit;
	}


	function footer_scripts() {
		$location = '';
		if ( pct_setting()->pre_translate == 1 ) {
			$lang = LocationLib::language_code( $_SERVER['REMOTE_ADDR'] );
			if ( ! empty( $lang ) ) {
				//check does this enable
				$langs_tmp = array_filter( explode( ',', pct_setting()->supported_languages ) );
				$langs_tmp = array_map( 'trim', $langs_tmp );
				$languages = pct_instance()->get_languages();
				$lang_name = $languages[ $lang ];
				if ( in_array( $lang_name, $langs_tmp ) ) {
					$location = $lang;
				}
			}
		}

		pct_instance()->render_view( 'picker/' . 'link' . '_script', array(
			'location' => $location
		) );

		pct_instance()->render_view( 'picker/' . 'dropdown' . '_script', array(
			'location' => $location
		) )
		?>
		<!--<script type="text/javascript">
			jQuery(function ($) {
				$('.pct_reset').hide();
				$('body').on('translate_done', function ($e, $container) {
					$container = $($container);
					$container.find('.pct_reset').show();
				})
				$('.pct_reset').click(function (e) {
					e.preventDefault();
					var that = $(this);
					var text_container = $(this).closest('div').find('.<?php /*echo pct_instance()->prefix */?>text').first();
					$.ajax({
						method: 'POST',
						url: '<?php /*echo admin_url( 'admin-ajax.php' ) */?>',
						data: {
							action: 'pct_reset',
							t: that.data('type'),
							id: that.data('id')
						},
						beforeSend: function () {
							text_container.css('opacity', '0.5');
						},
						success: function (data) {
							text_container.css('opacity', '1');
							text_container.html(data);
						}
					})
				})
			})
		</script>-->
		<?php
	}

	function append_dropdown_content( $content ) {
		if ( ! is_singular() ) {
			return $content;
		}

		if ( pct_setting()->mode != 'post' ) {
			$content = '<div class="pct_c_holder"><div data-id="' . get_the_ID() . '" data-type="content" class="' . pct_instance()->prefix . 'text' . '">' . $content . '</div></div>';

			return $content;
		}

		if ( strlen( trim( pct_setting()->custom_css ) ) ) {
			?>
			<style type="text/css">
				<?php echo pct_setting()->custom_css ?>
			</style>
			<?php
		} else {
			wp_enqueue_style( pct_instance()->prefix . 'style' );
		}

		$langs_tmp   = array_filter( explode( ',', pct_setting()->supported_languages ) );
		$langs_tmp   = array_map( 'trim', $langs_tmp );
		$lang_picker = '';
		if ( ! empty( $langs_tmp ) ) {
			$langs_tmp   = array_filter( explode( ',', pct_setting()->supported_languages ) );
			$langs_tmp   = array_map( 'trim', $langs_tmp );
			$lang_picker = '';
			if ( ! empty( $langs_tmp ) ) {
				$lang_picker = pct_instance()->render_view( 'picker/' . pct_setting()->picker_type . '_all', array(
					'langs_tmp' => $langs_tmp
				), true );
			}
			$content              = '<div class="pct_c_holder">' . $lang_picker . '<div data-id="' . get_the_ID() . '" data-type="content" class="' . pct_instance()->prefix . 'text' . '">' . $content . '</div></div>';
			$this->is_page_showed = true;
		}

		return $content;
	}

	function append_dropdown_comment( $comment_text, $comment, $args ) {
		if ( apply_filters( 'pct_translate_enable', false ) ) {
			if ( ! $this->is_page_showed ) {
				if ( strlen( trim( pct_setting()->custom_css ) ) ) {
					?>
					<style type="text/css">
						<?php echo pct_setting()->custom_css ?>
					</style>
					<?php
				} else {
					wp_enqueue_style( pct_instance()->prefix . 'style' );
				}

				$langs_tmp   = array_filter( explode( ',', pct_setting()->supported_languages ) );
				$langs_tmp   = array_map( 'trim', $langs_tmp );
				$lang_picker = '';
				if ( ! empty( $langs_tmp ) ) {
					if ( pct_setting()->mode == 'all' ) {
						if ( $this->is_showed == false ) {
							$lang_picker     = pct_instance()->render_view( 'picker/' . pct_setting()->picker_type . '_all', array(
								'langs_tmp' => $langs_tmp
							), true );
							$this->is_showed = true;
						}
					} elseif ( pct_setting()->mode == 'single' ) {
						$lang_picker = pct_instance()->render_view( 'picker/' . pct_setting()->picker_type, array(
							'langs_tmp' => $langs_tmp
						), true );
						//$lang_picker .= '&nbsp;<a class="pct_reset" data-type="comment" data-id="' . $comment->comment_ID . '" href="#">' . __( "Reset", pct_instance()->domain ) . '</a>';
					}

					if ( $this->is_page_showed == true ) {
						$lang_picker = '';
					}

					$comment_text = '<div class="pct_c_holder">' . $lang_picker . '<div data-id="' . $comment->comment_ID . '" class="' . pct_instance()->prefix . 'text' . '">' . $comment_text . '</div></div>';
				}
			} else {
				$comment_text = '<div class="pct_c_holder">' . '' . '<div data-id="' . $comment->comment_ID . '" class="' . pct_instance()->prefix . 'text' . '">' . $comment_text . '</div></div>';
			}
		}

		return $comment_text;
	}
}

new pct_Translate_Controller();