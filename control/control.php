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
		$this->handle_language_switch();
		$this->includes();
		$this->init_hooks();
		$this->version_check();
	}

	private function handle_language_switch() {
		if ( isset( $_GET['control_lang'] ) ) {
			$lang = sanitize_text_field( $_GET['control_lang'] );
			if ( in_array( $lang, array( 'ar', 'en' ) ) ) {
				setcookie( 'control_lang', $lang, time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN );
				$_COOKIE['control_lang'] = $lang;
			}
		}

		add_filter( 'plugin_locale', array( $this, 'set_plugin_locale' ), 20, 2 );
		add_filter( 'locale', array( $this, 'set_wp_locale' ), 20 );
	}

	public function set_plugin_locale( $locale, $domain ) {
		if ( $domain === 'control' ) {
			$cookie_lang = isset( $_COOKIE['control_lang'] ) ? $_COOKIE['control_lang'] : '';
			if ( $cookie_lang === 'en' ) {
				return 'en_US';
			} elseif ( $cookie_lang === 'ar' ) {
				return 'ar';
			}
		}
		return $locale;
	}

	public function set_wp_locale( $locale ) {
		$is_control_page = ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'control' ) !== false ) || isset( $_GET['control_view'] ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && strpos( $_REQUEST['action'], 'control_' ) === 0 );

		if ( $is_control_page ) {
			$cookie_lang = isset( $_COOKIE['control_lang'] ) ? $_COOKIE['control_lang'] : '';
			if ( $cookie_lang === 'en' ) {
				return 'en_US';
			} elseif ( $cookie_lang === 'ar' ) {
				return 'ar';
			}
		}
		return $locale;
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
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( 'Control_Auth', 'init' ) );
		add_action( 'init', array( 'Control_Notifications', 'init' ) );
		add_action( 'init', array( 'Control_PWA', 'init' ) );
		add_action( 'init', array( $this, 'send_nocache_headers' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_head', array( $this, 'add_viewport_meta' ) );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'control', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
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
			'strings'  => array(
				'backup_generating' => __( 'جاري توليد النسخة الاحتياطية...', 'control' ),
				'backup_success'    => __( 'تم إنشاء النسخة الاحتياطية بنجاح.', 'control' ),
				'backup_failed'     => __( 'فشل إنشاء النسخة الاحتياطية: ', 'control' ),
				'restore_warning'   => __( 'تحذير هام: سيتم استبدال كافة البيانات الحالية. هل تريد الاستمرار؟', 'control' ),
				'restore_success'   => __( 'تمت استعادة النظام بنجاح. سيتم إعادة تحميل الصفحة.', 'control' ),
				'restore_error'     => __( 'خطأ في الاستعادة: ', 'control' ),
				'otp_sent_error'    => __( 'فشل إرسال الرمز: ', 'control' ),
				'unique_error'      => __( 'هذه القيمة مسجلة مسبقاً في النظام.', 'control' ),
				'field_required'    => __( 'هذا الحقل مطلوب', 'control' ),
				'phone_invalid'     => __( 'رقم الهاتف غير صالح', 'control' ),
				'pass_mismatch'     => __( 'كلمة المرور غير متطابقة', 'control' ),
				'logging_in'        => __( 'جاري التحقق...', 'control' ),
				'login_btn'         => __( 'تسجيل الدخول للنظام', 'control' ),
				'processing'        => __( 'جاري معالجة طلبك...', 'control' ),
				'reg_complete_btn'  => __( 'إتمام التسجيل', 'control' ),
				'updating'          => __( 'جاري التحديث...', 'control' ),
				'update_pass_btn'   => __( 'تحديث كلمة المرور', 'control' ),
				'sending'           => __( 'جاري الإرسال...', 'control' ),
				'send_otp_btn'      => __( 'إرسال رمز التحقق', 'control' ),
				'verifying'         => __( 'جاري التحقق...', 'control' ),
				'verify_otp_btn'    => __( 'تحقق من الرمز', 'control' ),
				'pass_short'        => __( 'كلمة المرور قصيرة جداً', 'control' ),
				'saving'            => __( 'جاري الحفظ...', 'control' ),
				'update_and_login'  => __( 'تحديث كلمة المرور والدخول', 'control' ),
				'settings_saved'    => __( 'تم حفظ الإعدادات بنجاح', 'control' ),
				'settings_error'    => __( 'خطأ أثناء الحفظ', 'control' ),
				'logout_sync'       => __( 'جاري تسجيل الخروج وتأمين الحساب...', 'control' ),
				'export_preparing'  => __( 'جاري التجهيز...', 'control' ),
				'delete_confirm'    => __( 'هل أنت متأكد من حذف هذا السجل؟', 'control' ),
				'log_details_title' => __( 'تفاصيل السجل', 'control' ),
				'close'             => __( 'إغلاق', 'control' ),
				'personal_info'     => __( 'المعلومات الشخصية', 'control' ),
				'academic_info'     => __( 'المؤهلات الأكاديمية', 'control' ),
				'professional_info' => __( 'المعلومات المهنية', 'control' ),
				'account_settings'  => __( 'إعدادات الحساب', 'control' ),
				'save_changes'      => __( 'حفظ التعديلات', 'control' ),
				'confirmation_word' => __( 'تأكيد', 'control' ),
				'confirm_prompt'    => __( 'يرجى كتابة كلمة "تأكيد" بشكل صحيح للمتابعة.', 'control' ),
				'policy_delete'     => __( 'هل أنت متأكد من حذف هذه السياسة؟ لا يمكن التراجع عن هذا الإجراء.', 'control' ),
				'policy_saved'      => __( 'تم حفظ السياسة بنجاح', 'control' ),
				'save_policy'       => __( 'حفظ السياسة', 'control' ),
				'select_user_first' => __( 'يرجى اختيار مستخدم واحد على الأقل من القائمة أولاً.', 'control' ),
				'email_count_one'   => __( 'إرسال بريد لمستخدم واحد', 'control' ),
				'email_count_many'  => __( 'إرسال بريد لـ %d مستخدم مختار', 'control' ),
				'preparing_preview' => __( 'جاري توليد المعاينة...', 'control' ),
				'confirm_send_mail' => __( 'هل أنت متأكد من رغبتك في إرسال هذا البريد الآن؟', 'control' ),
				'general_error'     => __( 'حدث خطأ', 'control' ),
				'otp_error'         => __( 'رمز التحقق غير صحيح أو انتهت صلاحيته.', 'control' ),
				'sync_default'      => __( 'جارٍ تحميل البيانات...', 'control' ),
				'sync_success'      => __( 'تم التحديث بنجاح', 'control' ),
				'hard_refresh'      => __( 'جاري مسح التخزين المؤقت وتحديث ملفات النظام...', 'control' ),
				'select_image'      => __( 'اختر صورة', 'control' ),
				'admin_label'       => __( 'المسؤول:', 'control' ),
				'action_label'      => __( 'العملية:', 'control' ),
				'desc_label'        => __( 'الوصف:', 'control' ),
				'device_label'      => __( 'الجهاز:', 'control' ),
				'browser_label'     => __( 'المتصفح:', 'control' ),
				'date_label'        => __( 'التاريخ:', 'control' ),
				'meta_label'        => __( 'البيانات الوصفية:', 'control' ),
				'select_profile_img'=> __( 'اختر صورة شخصية', 'control' ),
				'save_success'      => __( 'تم الحفظ بنجاح', 'control' ),
				'preparing'         => __( 'جاري التجهيز...', 'control' ),
				'export_pkg_btn'    => __( 'تصدير الحزمة الآن', 'control' ),
				'bulk_del_title'    => __( 'حذف كافة الحسابات', 'control' ),
				'bulk_del_desc'     => __( 'أنت على وشك حذف كافة الكوادر البشرية المسجلة. لن يتم حذف حسابك الحالي. هذا الإجراء نهائي ولا يمكن التراجع عنه.', 'control' ),
				'sys_reset_title'   => __( 'تصفير النظام بالكامل', 'control' ),
				'sys_reset_desc'    => __( 'سيتم مسح كافة الكوادر، سجلات النشاط، والبيانات المدخلة. سيتم الحفاظ على إعدادات النظام، الأدوار، والقوالب فقط لضمان بقاء الهيكل الأساسي.', 'control' ),
				'confirm_execute'   => __( 'نعم، تنفيذ الآن', 'control' ),
				'add_policy_title'  => __( 'إضافة سياسة جديدة', 'control' ),
				'edit_policy_title' => __( 'تحرير السياسة: ', 'control' ),
				'sending_mail'      => __( 'جاري الإرسال...', 'control' ),
				'mail_error'        => __( 'حدث خطأ أثناء الإرسال', 'control' ),
			)
		) );
	}

}

function Control() {
	return Control_System::get_instance();
}

// Kick off the plugin
Control();
