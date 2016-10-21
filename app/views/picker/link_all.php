<?php
$pos = pct_setting()->picker_position;
if ( isset( $no_pos ) ) {
	$pos = '';
}
?>

<ul class="<?php echo pct_instance()->prefix . 'lang_picker_all ' . $pos . ' ' . 'lang_picker_link' ?> ">
	<?php foreach ( $langs_tmp as $lang ): ?>
		<?php
		$key = array_search( trim( $lang ), pct_instance()->get_languages() );
		if ( $key && ! empty( $key ) ) {
			?>
			<li><a href="#<?php esc_attr_e( $key ) ?>"><?php echo $lang ?></a></li>
		<?php } ?>
	<?php endforeach; ?>
</ul>