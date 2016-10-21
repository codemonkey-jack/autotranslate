<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$('.picker_single').change(function () {
			if ($(this).val().length > 0) {
				var that = $(this);
				var text_container = $(this).closest('div').find('.<?php echo pct_instance()->prefix ?>text').first();
				$.ajax({
					type: 'POST',
					url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
					data: {
						_nonce: '<?php echo wp_create_nonce( pct_instance()->prefix . 'translate' ) ?>',
						lang: $(this).val(),
						action: '<?php echo pct_instance()->prefix ?>translate',
						comment_id: text_container.data('id')
					},
					beforeSend: function () {
						text_container.css('opacity', '0.5');
					},
					success: function (data) {
						text_container.css('opacity', '1');
						data = jQuery.parseJSON(data);
						if (data.status == 1) {
							text_container.html(data.msg);
							$('body').trigger('translate_done', that.closest('.pct_c_holder'));
						} else {
							alert(data.msg);
						}
					}
				})
			}
		});
		<?php if(pct_setting()->mode == 'single'): ?>
		$('.pct_c_holder').closest('li').css('position', 'relative');
		<?php endif; ?>
		<?php if(! empty( $location )): ?>
		$('.picker_single').val('<?php echo $location ?>').change();
		<?php endif; ?>

		$('.picker_all').change(function () {
			if ($(this).val().length > 0) {
				var that = $(this);
				var text_container = $('.<?php echo pct_instance()->prefix ?>text');
				var value = $(this).val();
				text_container.each(function () {
					var that = $(this);
					var type = 'comment';
					if (that.data('type') != undefined) {
						type = that.data('type');
					}
					$.ajax({
						type: 'POST',
						url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
						data: {
							_nonce: '<?php echo wp_create_nonce( pct_instance()->prefix . 'translate' ) ?>',
							lang: value,
							action: '<?php echo pct_instance()->prefix ?>translate',
							comment_id: that.data('id'),
							type: type
						},
						beforeSend: function () {
							text_container.css('opacity', '0.5');
						},
						success: function (data) {
							that.css('opacity', '1');
							data = jQuery.parseJSON(data);
							if (data.status == 1) {
								that.html(data.msg);
								$('body').trigger('translate_done', that.closest('.pct_c_holder'));
							} else {
								alert(data.msg);
							}
						}
					})
				})
			}
		});
	})
</script>