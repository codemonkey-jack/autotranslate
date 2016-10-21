<?php
/**
 * Author: Jack Kitterhing
 */
$lang_picker = '<div><select class="' . pct_instance()->prefix . 'lang_picker picker_single '.pct_setting()->picker_position.'">';
$lang_picker .= '<option value="">' . __( 'Translate to', pct_instance()->domain ) . '</option>';
foreach ( $langs_tmp as $lang ) {
	$key = array_search( trim( $lang ), pct_instance()->get_languages() );
	if ( $key && ! empty( $key ) ) {
		$lang_picker .= '<option value="' . esc_attr( $key ) . '">' . __( esc_html( trim( $lang ) ), pct_instance()->domain ) . '</option>';
	}
}
$lang_picker .= '</select>';
echo $lang_picker;