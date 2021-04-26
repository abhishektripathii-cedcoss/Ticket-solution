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
$ts_template_settings = apply_filters( 'ts_template_settings_array', array() );
?>
<!--  template file for admin settings. -->
<div class="ts-section-wrap">
	<?php
		$ts_template_html = $ts_mwb_ts_obj->mwb_ts_plug_generate_html( $ts_template_settings );
		echo esc_html( $ts_template_html );
	?>
</div>
