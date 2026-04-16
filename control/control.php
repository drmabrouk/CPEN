<?php
/**
 * Plugin Name: Control
 * Description: Professional system for administrative and user management.
 * Version: 2.3.0
 * Author: Control Team
 * Text Domain: control
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Official Email: info@control.online
 * Official Website: https://control.online
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'CONTROL_VERSION', '2.3.0' );
define( 'CONTROL_PATH', plugin_dir_path( __FILE__ ) );
define( 'CONTROL_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Class
 */
class Control_System {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
		$this->version_check();
	}

	private function version_check() {
		$installed_ver = get_option( 'control_system_version' );
		if ( $installed_ver !== CONTROL_VERSION ) {
			Control_Database::create_tables();
			update_option( 'control_system_version', CONTROL_VERSION );
		}
	}

	private function define_constants() {
		// Already defined above for now, but can move more here if needed.
	}

	private function includes() {
		// Module classes
		require_once CONTROL_PATH . 'includes/class-database.php';
		require_once CONTROL_PATH . 'includes/class-auth.php';
		require_once CONTROL_PATH . 'includes/class-users.php';
		require_once CONTROL_PATH . 'includes/class-notifications.php';
		require_once CONTROL_PATH . 'includes/class-audit.php';
		require_once CONTROL_PATH . 'includes/class-pwa.php';

		// Infrastructure
		require_once CONTROL_PATH . 'includes/class-shortcode.php';
		require_once CONTROL_PATH . 'includes/class-ajax.php';
	}

	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'Control_Database', 'create_tables' ) );
		add_action( 'init', array( 'Control_Auth', 'init' ) );
		add_action( 'init', array( 'Control_Notifications', 'init' ) );
		add_action( 'init', array( 'Control_PWA', 'init' ) );
		add_action( 'init', array( $this, 'send_nocache_headers' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_head', array( $this, 'add_viewport_meta' ) );
	}

	public function add_viewport_meta() {
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">';
	}

	public function send_nocache_headers() {
		$is_control_page = (isset( $_GET['page'] ) && strpos( $_GET['page'], 'control' ) !== false) || isset( $_GET['control_view'] );
		$is_control_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && strpos( $_REQUEST['action'], 'control_' ) === 0;

		if ( $is_control_page || $is_control_ajax ) {
			nocache_headers();

			// Aggressive Cache Prevention
			header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
			header( "Cache-Control: post-check=0, pre-check=0", false );
			header( "Pragma: no-cache" );
			header( "Expires: Wed, 11 Jan 1984 05:00:00 GMT" );
		}
	}

	public function enqueue_assets() {
		wp_enqueue_media();
		wp_enqueue_style( 'dashicons' );

		// Enqueue Rubik font from Google Fonts
		wp_enqueue_style( 'control-font-rubik', 'https://fonts.googleapis.com/css2?family=Rubik:wght@400;600;700;800&display=swap', array(), CONTROL_VERSION );

		wp_enqueue_style( 'control-rtl-style', CONTROL_URL . 'assets/css/style-rtl.css', array( 'control-font-rubik' ), CONTROL_VERSION );
		wp_enqueue_style( 'control-print-style', CONTROL_URL . 'assets/css/print.css', array(), CONTROL_VERSION, 'print' );

		// Enqueue html2pdf for bulk export
		wp_enqueue_script( 'html2pdf', 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js', array(), '0.10.1', true );

		wp_enqueue_script( 'control-scripts', CONTROL_URL . 'assets/js/scripts.js', array( 'jquery' ), CONTROL_VERSION, true );

		wp_localize_script( 'control-scripts', 'control_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'home_url' => home_url(),
			'logout_url' => wp_logout_url( home_url() ),
			'nonce'    => wp_create_nonce( 'control_nonce' ),
		) );
	}

}

function Control() {
	return Control_System::get_instance();
}

// Kick off the plugin
Control();
