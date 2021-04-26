<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Ticket_Solution
 * @subpackage Ticket_Solution/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $ts_mwb_ts_obj;
$ts_genaral_settings = apply_filters( 'ts_general_settings_array', array() );
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="mwb-ts-gen-section-form">
	<div class="ts-secion-wrap">
		<?php
		$ts_general_html = $ts_mwb_ts_obj->mwb_ts_plug_generate_html( $ts_genaral_settings );
		echo esc_html( $ts_general_html );
		?>
	</div>
</form>