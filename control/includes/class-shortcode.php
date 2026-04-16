<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Control_Shortcode {

	public function __construct() {
		add_shortcode( 'control_system', array( $this, 'render_dashboard' ) );
		add_shortcode( 'control_policies', array( $this, 'render_policies' ) );
	}

	public function render_dashboard() {
		global $wpdb;
		ob_start();

		if ( ! Control_Auth::is_logged_in() ) {
			include CONTROL_PATH . 'templates/login.php';
			return ob_get_clean();
		}

		$view = isset( $_GET['control_view'] ) ? sanitize_text_field( $_GET['control_view'] ) : 'dashboard';
		$is_admin = Control_Auth::is_admin();

		include CONTROL_PATH . 'templates/header.php';

		$no_access_html = '
		<div style="text-align:center; padding:100px 30px; background:#fff; border-radius:20px; border:1px solid #e2e8f0; max-width:600px; margin: 40px auto; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
			<div style="width:100px; height:100px; background:#fef2f2; color:#ef4444; border-radius:50%; display:flex; align-items:center; justify-content:center; margin: 0 auto 30px;">
				<span class="dashicons dashicons-shield-lock" style="font-size:50px; width:50px; height:50px;"></span>
			</div>
			<h2 style="font-weight:800; color:#1e293b; margin-bottom:15px;">' . __( 'مرحباً بك في نظام كنترول', 'control' ) . '</h2>
			<p style="color:#64748b; font-size:1.1rem; line-height:1.6; margin-bottom:30px;">' . __( 'ليس لديك الصلاحيات الكافية للوصول إلى لوحة التحكم حالياً.', 'control' ) . '</p>
			<div style="padding:15px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1; color:#475569; font-size:0.9rem;">
				' . __( 'برجاء التواصل مع إدارة النظام أو الدعم الفني لطلب تفعيل صلاحيات الوصول الخاصة بحسابك.', 'control' ) . '
			</div>
		</div>';

		switch ( $view ) {
			case 'users':
				if ( ! Control_Auth::has_permission('users_view') ) {
					echo $no_access_html;
				} else {
					include CONTROL_PATH . 'templates/users.php';
				}
				break;
			case 'roles':
				if ( ! Control_Auth::has_permission('roles_manage') ) {
					echo $no_access_html;
				} else {
					include CONTROL_PATH . 'templates/roles.php';
				}
				break;
			case 'settings':
				if ( ! Control_Auth::has_permission('settings_manage') ) {
					echo $no_access_html;
				} else {
					include CONTROL_PATH . 'templates/settings.php';
				}
				break;
			default:
				if ( ! Control_Auth::has_permission('dashboard') ) {
					echo $no_access_html;
				} else {
					include CONTROL_PATH . 'templates/dashboard-home.php';
				}
				break;
		}

		include CONTROL_PATH . 'templates/footer.php';
		return ob_get_clean();
	}

	public function render_policies() {
		global $wpdb;
		$policies = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}control_policies ORDER BY id ASC" );

		if ( empty($policies) ) return '';

		ob_start();
		?>
		<div class="control-policies-display" style="direction: rtl; text-align: right; font-family: 'Rubik', sans-serif; line-height: 1.8; color: #334155; background: #fff; padding: 40px; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 900px; margin: 40px auto;">
			<?php foreach($policies as $policy): ?>
				<div class="policy-item" style="margin-bottom: 40px;">
					<h2 style="color:var(--control-primary); font-weight:800; border-bottom: 2px solid var(--control-bg); padding-bottom:10px; margin-bottom:20px;"><?php echo esc_html($policy->title); ?></h2>
					<div class="policy-content">
						<?php echo wp_kses_post( $policy->content ); ?>
					</div>
				</div>
			<?php endforeach; ?>
			<div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9; text-align: center; color: #94a3b8; font-size: 0.8rem;">
				<?php echo sprintf( __('تم تحديث كافة السياسات في: %s', 'control'), date_i18n( get_option('date_format') ) ); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

new Control_Shortcode();
