<?php
/**
 * Product import comparison log.
 *
 * @package Import_From_Hawthorne
 * @subpackage Import_From_Hawthorne/admin/templates/emails
 */

defined( 'ABSPATH' ) || exit;

global $wp_filesystem;
require_once ABSPATH . '/wp-admin/includes/file.php';
WP_Filesystem();

/**
 * This hook runs on the custom email headers.
 *
 * This hook helps in customizing email header text.
 *
 * @param string $email_heading Email heading.
 * @since 1.0.0
 */
do_action( 'woocommerce_email_header', $email_heading );
$filename = ( ! empty( $_COOKIE['hawthorne_import_log_filename'] ) ) ? $_COOKIE['hawthorne_import_log_filename'] : '';
$log_file = HAWTHORNE_LOG_DIR_PATH . $filename;
$log_arr  = $wp_filesystem->get_contents_array( $log_file );
?>
<p><?php esc_html_e( 'Congratulations !! The products are imported from Hawthorne.', 'import-from-hawthorne' ); ?></p>
<h3>
	<?php
	/* translators: 1: %s: current date time */
	echo sprintf( __( 'Hawthorne Import - %1$s, at %2$s', 'import-from-hawthorne' ), gmdate( 'F j, Y' ), gmdate( 'h:i A' ) );
	?>
</h3>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" bordercolor="#eee">
	<tbody>
		<!-- ITERATE THROUGH THE LOG DATA -->
		<?php
		if ( ! empty( $log_arr ) && is_array( $log_arr ) ) {
			foreach ( $log_arr as $log ) {
				$log = trim( $log );

				// Skip, if the log is empty.
				if ( empty( $log ) ) {
					continue;
				}
				?>
				<tr>
					<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $log ); ?></td>
				</tr>
				<?php
			}
		}
		?>
	</tbody>
</table>

<p><?php esc_html_e( 'If you\'re anyhow unable to read the content above, please find the attachment.', 'import-from-hawthorne' ); ?></p>
<p><?php esc_html_e( 'This is a system generated email. Please DO NOT respond to it.', 'import-from-hawthorne' ); ?></p>
<?php
/**
 * This hook runs on the custom email footers.
 *
 * This hook helps in customizing email footer text.
 *
 * @since 1.0.0
 */
do_action( 'woocommerce_email_footer' );
