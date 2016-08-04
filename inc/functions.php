<?php
/**
 * A general functions file
 *
 * @package wp-podcatcher
 */

function wppc_no_fm() {
?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php esc_html_e( 'Fieldmanager should be here. Something has gone wrong. Contact joe@wpinonemonth.com', 'wp-podcatcher' ); ?>
			</p>
		</div>
<?php
}
