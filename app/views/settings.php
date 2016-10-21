<div class="wrap" style="position:relative;">
	<h2><?php _e( 'Settings', pct_instance()->domain ) ?>
	</h2>
	<?php $form = phpapp_Active_Form::generateForm( $model );
	$form->openForm( '#', 'POST' );
	?>
	<div class="metabox-holder">
		<div class="postbox">
			<h3 class="hndle" style="cursor:auto;">
				<span><?php _e( 'Translation Settings', pct_instance()->domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'Supported Languages', pct_instance()->domain ) ?></th>
						<td>
							<ul id="supl">
								<?php
								$tags = $model->supported_languages;
								$tags = explode( ',', $tags );
								$tags = array_filter( $tags );
								foreach ( $tags as $tag ): ?>
									<li><?php echo $tag ?></li>
								<?php endforeach; ?>
							</ul>
							<?php /*$form->textField( $model, "supported_languages", array(
								'class'       => 'regular-text',
								'id'          => 'support_lang',
								'placeholder' => __( "English, Spanish,.." )
							) ) */ ?>
							<p class="description"><?php _e( 'This is the languages your reader can translate', pct_instance()->domain ) ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Mode', pct_instance()->domain ) ?></th>
						<td>
							<?php $form->dropDownList( $model, "mode", array(
								'post'   => __( "Translate all comments and page content into the select language", pct_instance()->domain ),
								'all'    => __( "Translate all comments into the select language.", pct_instance()->domain ),
								'single' => __( "Translate comments on a comment by comment basis", pct_instance()->domain ),
								'off'    => __( "Disable", pct_instance()->domain ),
							) ) ?>
							<p class="description"><?php _e( 'This is the languages your reader can translate', pct_instance()->domain ) ?></p>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox">
			<h3 class="hndle" style="cursor:auto;">
				<span><?php _e( 'Languages Picker Look', pct_instance()->domain ) ?></span></h3>

			<div class="inside">
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'Position', pct_instance()->domain ) ?></th>
						<td>
							<?php $form->dropDownList( $model, "picker_position", array(
								'top-left'     => 'Top Left',
								'top-right'    => 'Top Right',
								'bottom-left'  => 'Bottom Left',
								'bottom-right' => 'Bottom Right'
							) ) ?>
							<p class="description"><?php _e( 'Where you want the languages picker placed?', pct_instance()->domain ) ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Display as', pct_instance()->domain ) ?></th>
						<td>
							<?php $form->dropDownList( $model, "picker_type", array(
								'dropdown' => 'Drop down',
								'link'     => 'Link'
							) ) ?>
							<p class="description"><?php _e( 'We can display it as links or drop down.', pct_instance()->domain ) ?></p>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox">
			<h3 class="hndle" style="cursor:auto;">
				<span><?php _e( 'Translate Engine', pct_instance()->domain ) ?></span></h3>

			<div class="inside">
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'Which is', pct_instance()->domain ) ?></th>
						<td>
							<?php $form->dropDownList( $model, "engine", apply_filters( 'pct_translate_engine', array() ), array(
								'id' => 'engine'
							) ) ?>
							<p class="description"><?php _e( 'This is where you can get the translate engine.', pct_instance()->domain ) ?></p>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<?php do_action( 'pct_settings_addition', $model, $form ) ?>
	<div class="metabox-holder">
		<div class="postbox">
			<h3 class="hndle" style="cursor:auto;">
				<span><?php _e( 'Custom CSS', pct_instance()->domain ) ?></span></h3>

			<div class="inside">
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'Custom Css', pct_instance()->domain ) ?></th>
						<td>
							<?php echo $form->textArea( $model, 'custom_css', array(
								'style' => 'width:100%',
								'rows'  => 10,
								'id'    => 'pct_editor'
							) ) ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<?php wp_nonce_field( pct_instance()->prefix . 'settings' ) ?>
	<p class="submit">
		<button type="submit" name="<?php echo pct_instance()->prefix . 'setting' ?>"
		        class="button button-primary"><?php _e( 'Save Changes', pct_instance()->domain ) ?></button>
	</p>
	<?php echo $form->endForm() ?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function ($) {
		var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('pct_editor'), {
			lineNumbers: true,
			mode: "css",
			extraKeys: {"Ctrl-Space": "autocomplete"}
		});
		var sources = <?php echo json_encode( array_values( pct_instance()->get_languages() ) ) ?>;
		$('.api-box').hide();
		function split(val) {
			return val.split(/,\s*/);
		}

		function extractLast(term) {
			return split(term).pop();
		}

		$("#supl").tagit({
			availableTags: sources,
			fieldName: 'supported_languages[]',
			'placeholderText': 'enter...',
			beforeTagAdded: function (event, ui) {
				// do something special
				if (sources.indexOf(ui.tag.find('span').first().text()) == -1) {
					return false;
				}
			}
		});
		$('#support_lang, #default_lang')
			.bind("keydown", function (event) {
				if (event.keyCode === $.ui.keyCode.TAB &&
					$(this).data("ui-autocomplete").menu.active) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				source: function (request, response) {
					// delegate back to autocomplete, but extract the last term
					response($.ui.autocomplete.filter(
						sources, extractLast(request.term)));
				},
				focus: function () {
					// prevent value inserted on focus
					return false;
				},
				select: function (event, ui) {
					var terms = split(this.value);
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push(ui.item.value);
					// add placeholder to get the comma-and-space at the end
					terms.push("");
					this.value = terms.join(", ");
					return false;
				}
			});

		$('#engine').change(function () {
			console.log($(this).val());
			if ($(this).val() == 'pct_Bing_Translate') {
				$('.api-box').hide();
				$('#bing-api').show();
			} else if ($(this).val() == 'pct_Google_Translate') {
				$('.api-box').hide();
				$('#google-api').show();
			} else if ($(this).val() == 'pct_Yandex_Translate') {
				$('.api-box').hide();
				$('#yandex-api').show();
			}
		}).change();
	})
</script>