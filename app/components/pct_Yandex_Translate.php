<?php
include pct_instance()->plugin_path . 'app/components/yandex_translate/pct_Yandex_Translate_Model.php';

/**
 * Author: Jack Kitterhing
 */
class pct_Yandex_Translate {

	public function __construct() {
		add_action( 'pct_settings_addition', array( &$this, 'setting' ), 10, 2 );
		add_filter( 'pct_settings_saved', array( &$this, 'process' ), 10, 2 );
		add_filter( 'pct_translate_enable', array( &$this, 'is_on' ) );
		add_filter( 'pct_do_translate', array( &$this, 'translate' ), 10, 3 );
	}

	function translate( $object_id, $lang, $type ) {
		if ( $type == 'comment' ) {
			$comment = get_comment( $object_id );
			if ( is_null( $comment ) ) {
				return array(
					'status'        => false,
					'error_message' => __( 'Comment not found!', pct_instance()->domain )
				);
			}

			$text = $comment->comment_content;
		} elseif ( $type = 'content' ) {
			$post = get_post( $object_id );
			if ( is_null( $post ) ) {
				return array(
					'status'        => false,
					'error_message' => __( 'Post/Page not found!', pct_instance()->domain )
				);
			}
			$text = $post->post_content;
		}

		$model  = new pct_Yandex_Translate_Model();
		$result = $model->translate( nl2br( $text ), $lang );
		if ( $result['code'] == 200 ) {
			return array(
				'status'     => true,
				'translated' => reset( $result['text'] )

			);
		} else {
			return array(
				'status'        => false,
				'error_message' => $result['message']
			);
		}
	}

	function is_on() {
		$model = new pct_Yandex_Translate_Model();
		if ( ! empty( $model->yandex_api ) ) {
			return true;
		}

		return false;
	}

	function process( $return, $pmodel ) {
		$model = new pct_Yandex_Translate_Model();
		$model->import( $_POST['pct_Yandex_Translate_Model'] );
		if ( $model->validate() ) {
			$model->save();
			$return = true;
		} else {
			pct_instance()->global['yandex_model'] = $model;
			$return                                = false;
		}

		return $return;
	}

	function setting( $pmodel, $form ) {
		$model = null;

		if ( isset( pct_instance()->global['yandex_model'] ) ) {
			$model = pct_instance()->global['yandex_model'];
		}
		if ( ! $model instanceof pct_Yandex_Translate_Model ) {
			$model = new pct_Yandex_Translate_Model();
		}
		?>
		<div class="metabox-holder api-box" id="yandex-api">
			<div class="postbox">
				<h3 class="hndle" style="cursor:auto;"><span><?php _e( 'Yandex Api', pct_instance()->domain ) ?></span>
				</h3>

				<div class="inside">
					<table class="form-table">
						<tr>
							<th scope="row"><?php _e( 'Yandex API', pct_instance()->domain ) ?></th>
							<td>
								<?php $form->textField( $model, "yandex_api", array( 'class' => 'regular-text' ) ) ?>
								<p><?php _e( 'Yandex Api Key, required for translate job, you can obtain <a target="_blank" href="http://api.yandex.com/key/getkey.xml">here</a>', pct_instance()->domain ) ?></p>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<?php
	}
}

add_filter( 'pct_translate_engine', 'pct_register_yandex_engine' );
function pct_register_yandex_engine( $list ) {
	$list = array_merge( $list, array( 'pct_Yandex_Translate' => 'Yandex Translate' ) );

	return $list;
}