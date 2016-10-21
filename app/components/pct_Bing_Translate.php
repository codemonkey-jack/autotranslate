<?php
include pct_instance()->plugin_path . 'app/components/bing_translate/core.php';
include pct_instance()->plugin_path . 'app/components/bing_translate/pct_Bing_Translate_Model.php';

/**
 * Author: Jack Kitterhing
 */
class pct_Bing_Translate {

	public function __construct() {
		add_action( 'pct_settings_addition', array( &$this, 'setting' ), 10, 2 );
		add_filter( 'pct_settings_saved', array( &$this, 'process' ), 10, 2 );
		add_filter( 'pct_translate_enable', array( &$this, 'is_on' ) );
		add_filter( 'pct_do_translate', array( &$this, 'translate' ), 10, 3 );
	}

	function translate( $object_id, $lang, $type = 'comment' ) {
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

		$model  = new pct_Bing_Translate_Model();
		$result = $model->translate( nl2br( $text ), $lang );

		if ( $result['status'] == false ) {
			return array(
				'status'        => false,
				'error_message' => $result['error']
			);
		} else {
			return array(
				'status'     => true,
				'translated' => $result['translated']
			);
		}
	}

	function is_on() {
		$model = new pct_Bing_Translate_Model();
		if ( ! empty( $model->client_id ) && ! empty( $model->client_secret ) ) {
			return true;
		}

		return false;
	}

	function process( $return, $pmodel ) {
		$model = new pct_Bing_Translate_Model();
		$model->import( $_POST['pct_Bing_Translate_Model'] );
		if ( $model->validate() ) {
			$model->save();
			$return = true;
		} else {
			pct_instance()->global['bing_model'] = $model;
			$return                              = false;
		}

		return $return;
	}

	function setting( $pmodel, $form ) {
		$model = null;

		if ( isset( pct_instance()->global['bing_model'] ) ) {
			$model = pct_instance()->global['bing_model'];
		}
		if ( ! $model instanceof pct_Bing_Translate_Model ) {
			$model = new pct_Bing_Translate_Model();
		}
		?>
		<div class="metabox-holder api-box" id="bing-api">
			<div class="postbox">
				<h3 class="hndle" style="cursor:auto;"><span><?php _e( 'Bing Api', pct_instance()->domain ) ?></span>
				</h3>

				<div class="inside">
					<table class="form-table">
						<tr>
							<th scope="row"><?php _e( 'Bing Client Id', pct_instance()->domain ) ?></th>
							<td>
								<?php $form->textField( $model, "client_id", array( 'class' => 'regular-text' ) ) ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Bing Client Secret Key', pct_instance()->domain ) ?></th>
							<td>
								<?php $form->textField( $model, "client_secret", array( 'class' => 'regular-text' ) ) ?>
								<p class="description"><?php _e( 'For getting Client Id and Client Secret Key, please check this <a href="http://blogs.msdn.com/b/translation/p/gettingstarted1.aspx">docs</a>', pct_instance()->domain ) ?></p>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<?php
	}
}

add_filter( 'pct_translate_engine', 'pct_register_bing_engine' );
function pct_register_bing_engine( $list ) {
	$list = array_merge( $list, array( 'pct_Bing_Translate' => 'Bing Translate' ) );

	return $list;
}