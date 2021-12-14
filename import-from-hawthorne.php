<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/vermadarsh/
 * @since             1.0.0
 * @package           Import_From_Hawthorne
 *
 * @wordpress-plugin
 * Plugin Name:       Import from Hawthorne
 * Plugin URI:        https://github.com/vermadarsh/import-from-hawthorne/
 * Description:       This plugin helps migrating e-commerce content from Hawthorne.
 * Version:           1.0.0
 * Author:            Adarsh Verma
 * Author URI:        https://github.com/vermadarsh/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       import-from-hawthorne
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'HAWTHORNE_PLUGIN_VERSION', '1.0.0' );

// Plugin path.
if ( ! defined( 'HAWTHORNE_PLUGIN_PATH' ) ) {
	define( 'HAWTHORNE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

// Plugin URL.
if ( ! defined( 'HAWTHORNE_PLUGIN_URL' ) ) {
	define( 'HAWTHORNE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Log file path.
if ( ! defined( 'HAWTHORNE_LOG_DIR_PATH' ) ) {
	$uploads_dir = wp_upload_dir();
	define( 'HAWTHORNE_LOG_DIR_PATH', $uploads_dir['basedir'] . '/hawthorne-import-log/' );
}

// Log file url.
if ( ! defined( 'HAWTHORNE_LOG_DIR_URL' ) ) {
	$uploads_dir = wp_upload_dir();
	define( 'HAWTHORNE_LOG_DIR_URL', $uploads_dir['baseurl'] . '/hawthorne-import-log/' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-import-from-hawthorne-activator.php
 */
function activate_import_from_hawthorne() {
	require_once HAWTHORNE_PLUGIN_PATH . 'includes/class-import-from-hawthorne-activator.php';
	Import_From_Hawthorne_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-import-from-hawthorne-deactivator.php
 */
function deactivate_import_from_hawthorne() {
	require_once HAWTHORNE_PLUGIN_PATH . 'includes/class-import-from-hawthorne-deactivator.php';
	Import_From_Hawthorne_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_import_from_hawthorne' );
register_deactivation_hook( __FILE__, 'deactivate_import_from_hawthorne' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_import_from_hawthorne() {
	// The core plugin class that is used to define internationalization, admin-specific hooks, and public-facing site hooks.
	require HAWTHORNE_PLUGIN_PATH . 'includes/class-import-from-hawthorne.php';
	$plugin = new Import_From_Hawthorne();
	$plugin->run();
}

/**
 * This initiates the plugin.
 * Checks for the required plugins to be installed and active.
 *
 * @since 1.0.0
 */
function hawthorne_plugins_loaded_callback() {
	$active_plugins = get_option( 'active_plugins' );
	$is_wc_active   = in_array( 'woocommerce/woocommerce.php', $active_plugins, true );

	if ( current_user_can( 'activate_plugins' ) && false === $is_wc_active ) {
		add_action( 'admin_notices', 'hawthorne_admin_notices_callback' );
	} else {
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'hawthorne_plugin_actions_callback' );
		run_import_from_hawthorne();
	}
}

add_action( 'plugins_loaded', 'hawthorne_plugins_loaded_callback' );

/**
 * Show admin notice for the required plugins not active or installed.
 *
 * @since 1.0.0
 */
function hawthorne_admin_notices_callback() {
	$this_plugin_data = get_plugin_data( __FILE__ );
	$this_plugin      = $this_plugin_data['Name'];
	$wc_plugin        = 'WooCommerce';
	?>
	<div class="error">
		<p>
			<?php
			/* translators: 1: %s: strong tag open, 2: %s: strong tag close, 3: %s: this plugin, 4: %s: woocommerce plugin, 5: anchor tag for woocommerce plugin, 6: anchor tag close */
			echo wp_kses_post( sprintf( __( '%1$s%3$s%2$s is ineffective as it requires %1$s%4$s%2$s to be installed and active. Click %5$shere%6$s to install or activate it.', 'import-from-hawthorne' ), '<strong>', '</strong>', esc_html( $this_plugin ), esc_html( $wc_plugin ), '<a target="_blank" href="' . admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) . '">', '</a>' ) );
			?>
		</p>
	</div>
	<?php
}

/**
 * This function adds custom plugin actions.
 *
 * @param array $links Links array.
 * @return array
 * @since 1.0.0
 */
function hawthorne_plugin_actions_callback( $links ) {
	$this_plugin_links = array(
		'<a title="' . __( 'Settings', 'import-from-hawthorne' ) . '" href="' . esc_url( admin_url( 'admin.php?page=hawthorne' ) ) . '">' . __( 'Settings', 'import-from-hawthorne' ) . '</a>',
		'<a title="' . __( 'Docs', 'import-from-hawthorne' ) . '" href="javascript:void(0);">' . __( 'Docs', 'import-from-hawthorne' ) . '</a>',
	);

	return array_merge( $this_plugin_links, $links );
}

/*add_action( 'admin_init', function() {
	$post_ids = get_posts(
		array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'post_status' => 'any',
		)
	);

	if ( empty( $post_ids ) || ! is_array( $post_ids ) ) {
		return;
	}

	foreach ( $post_ids as $post_id ) {
		wp_delete_post( $post_id, true );
	}
} );*/

/**
 * Check if the function exists.
 */
if ( ! function_exists( 'debug' ) ) {
	/**
	 * Debug function.
	 *
	 * @param string $params Parameter to print.
	 */
	function debug( $params ) {
		echo '<pre>';
		print_r( $params );
		echo '</pre>';
	}
}
