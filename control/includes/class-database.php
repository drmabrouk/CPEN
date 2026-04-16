<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Control_Database {

	public static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_staff        = $wpdb->prefix . 'control_staff';
		$table_settings     = $wpdb->prefix . 'control_settings';
		$table_roles        = $wpdb->prefix . 'control_roles';
		$table_activity_logs = $wpdb->prefix . 'control_activity_logs';
		$table_email_templates = $wpdb->prefix . 'control_email_templates';
		$table_policies     = $wpdb->prefix . 'control_policies';
		$table_otps         = $wpdb->prefix . 'control_otps';
		$table_reset_tokens = $wpdb->prefix . 'control_reset_tokens';

		$sql = "CREATE TABLE $table_staff (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			username varchar(100),
			phone varchar(50) NOT NULL,
			password varchar(255) NOT NULL,
			first_name varchar(100),
			last_name varchar(100),
			email varchar(255),
			role varchar(50) DEFAULT 'employee',
			is_restricted tinyint(1) DEFAULT 0,
			restriction_reason varchar(255),
			restriction_expiry datetime,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			last_activity datetime DEFAULT CURRENT_TIMESTAMP,
			raw_password varchar(255),

			/* Personal Info */
			profile_image varchar(255),
			gender varchar(20),

			/* Academic Info */
			degree varchar(255),
			specialization varchar(255),
			institution varchar(255),
			institution_country varchar(100),
			graduation_year varchar(10),

			/* Personal & Location Info */
			home_country varchar(100),
			state varchar(100),
			address text,

			/* Employment Info */
			employer_name varchar(255),
			employer_country varchar(100),
			work_phone varchar(50),
			work_email varchar(255),
			org_logo varchar(255),
			job_title varchar(255),

			PRIMARY KEY  (id),
			UNIQUE KEY phone (phone),
			UNIQUE KEY email (email)
		) $charset_collate;

		CREATE TABLE $table_settings (
			setting_key varchar(100) NOT NULL,
			setting_value text,
			PRIMARY KEY  (setting_key)
		) $charset_collate;

		CREATE TABLE $table_roles (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			role_key varchar(50) NOT NULL,
			role_name varchar(100) NOT NULL,
			permissions longtext,
			is_system tinyint(1) DEFAULT 0,
			PRIMARY KEY  (id),
			UNIQUE KEY role_key (role_key)
		) $charset_collate;

		CREATE TABLE $table_activity_logs (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id varchar(100) NOT NULL,
			action_type varchar(100) NOT NULL,
			description text,
			device_type varchar(50),
			device_info text,
			ip_address varchar(50),
			meta_data longtext,
			action_date datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;

		CREATE TABLE $table_email_templates (
			template_key varchar(100) NOT NULL,
			subject text NOT NULL,
			content longtext NOT NULL,
			last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (template_key)
		) $charset_collate;

		CREATE TABLE $table_policies (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			title varchar(255) NOT NULL,
			content longtext NOT NULL,
			last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;

		CREATE TABLE $table_otps (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			email varchar(255) NOT NULL,
			otp varchar(10) NOT NULL,
			expiry datetime NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			is_verified tinyint(1) DEFAULT 0,
			PRIMARY KEY  (id),
			KEY email (email)
		) $charset_collate;

		CREATE TABLE $table_reset_tokens (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id varchar(100) NOT NULL,
			token varchar(100) NOT NULL,
			expiry datetime NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			is_used tinyint(1) DEFAULT 0,
			PRIMARY KEY  (id),
			KEY token (token)
		) $charset_collate;";

		if ( file_exists( ABSPATH . 'wp-admin/includes/upgrade.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}

		// Seed initial data
		self::seed_data();
		Control_Auth::sync_roles();
	}

	private static function seed_data() {
		global $wpdb;
		$table_staff    = $wpdb->prefix . 'control_staff';
		$table_settings = $wpdb->prefix . 'control_settings';
		$table_roles    = $wpdb->prefix . 'control_roles';
		$table_email_templates = $wpdb->prefix . 'control_email_templates';
		$table_policies = $wpdb->prefix . 'control_policies';

		// Seed initial roles
		$initial_roles = array(
			array(
				'role_key'  => 'admin',
				'role_name' => 'System Administrator',
				'permissions' => json_encode(array('all' => true)),
				'is_system' => 1
			),
			array(
				'role_key'  => 'coach',
				'role_name' => 'Sports Coach',
				'permissions' => json_encode(array('dashboard' => true, 'users_view' => true)),
				'is_system' => 1
			),
			array(
				'role_key'  => 'therapist',
				'role_name' => 'Sports Therapist',
				'permissions' => json_encode(array('dashboard' => true, 'users_view' => true)),
				'is_system' => 1
			),
			array(
				'role_key'  => 'nutritionist',
				'role_name' => 'Sports Nutrition Specialist',
				'permissions' => json_encode(array('dashboard' => true, 'users_view' => true)),
				'is_system' => 1
			),
			array(
				'role_key'  => 'pe_teacher',
				'role_name' => 'PE Teacher',
				'permissions' => json_encode(array('dashboard' => true, 'users_view' => true)),
				'is_system' => 1
			),
			array(
				'role_key'  => 'researcher',
				'role_name' => 'Sports Researcher',
				'permissions' => json_encode(array('dashboard' => true, 'users_view' => true)),
				'is_system' => 1
			)
		);

		foreach ( $initial_roles as $role ) {
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table_roles WHERE role_key = %s", $role['role_key'] ) );
			if ( ! $exists ) {
				$wpdb->insert( $table_roles, $role );
			}
		}

		// Default settings
		$defaults = array(
			'fullscreen_password' => '123456789',
			'system_name'         => 'Control',
			'company_name'        => 'Control',
			'pwa_app_name'        => 'Control',
			'pwa_short_name'      => 'Control',
			'pwa_theme_color'     => '#000000',
			'pwa_bg_color'        => '#ffffff',
			'smtp_host'           => '',
			'smtp_port'           => '587',
			'smtp_user'           => '',
			'smtp_pass'           => '',
			'smtp_encryption'     => 'tls',
			'sender_name'         => 'Control System',
			'sender_email'        => get_option('admin_email'),
			'email_theme'         => 'modern',
			'auth_registration_enabled'      => '1',
			'auth_login_enabled'             => '1',
			'auth_registration_form_visible' => '1',
			'auth_login_form_visible'        => '1',
			'auth_registration_fields'       => json_encode(array(
				array('id' => 'first_name', 'label' => 'First Name', 'enabled' => true, 'required' => true),
				array('id' => 'last_name', 'label' => 'Last Name', 'enabled' => true, 'required' => true),
				array('id' => 'phone', 'label' => 'Phone Number', 'enabled' => true, 'required' => true),
				array('id' => 'email', 'label' => 'Email Address', 'enabled' => true, 'required' => true),
				array('id' => 'password', 'label' => 'Password', 'enabled' => true, 'required' => true),
			)),
			'auth_logo_visible'      => '1',
			'auth_bg_color'          => '#000000',
			'auth_bg_image'          => '',
			'auth_container_bg'      => '#000000',
			'auth_container_opacity' => '1.0',
			'auth_border_color'      => 'rgba(255,255,255,0.1)',
			'auth_border_radius'     => '20',
			'auth_container_shadow'  => '0 25px 50px -12px rgba(0, 0, 0, 0.5)',
			'auth_input_bg'          => 'transparent',
			'auth_input_border'      => 'rgba(255,255,255,0.2)',
			'auth_input_focus'       => '#D4AF37',
			'auth_heading_text'      => 'Welcome to the Management System',
			'auth_subtitle_text'     => 'The integrated and most advanced management system',
			'auth_layout_template'   => 'centered',
			'auth_title_visible'     => '1',
			'auth_subtitle_visible'  => '1',
			'policies_content'       => '<h2>Terms and Conditions</h2><p>System policies and legal terms governing work are listed here.</p>',
		);

		foreach ( $defaults as $key => $value ) {
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT setting_key FROM $table_settings WHERE setting_key = %s", $key ) );
			if ( ! $exists ) {
				$wpdb->insert( $table_settings, array(
					'setting_key'   => $key,
					'setting_value' => $value
				) );
			}
		}

		// Migrate/Seed Policies
		$policy_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_policies");
		if ($policy_count == 0) {
			$existing_policy = $wpdb->get_var("SELECT setting_value FROM $table_settings WHERE setting_key = 'policies_content'");
			if ($existing_policy) {
				$wpdb->insert($table_policies, array(
					'title' => 'General Terms and Conditions',
					'content' => $existing_policy
				));
			} else {
				$wpdb->insert($table_policies, array(
					'title' => 'Privacy Policy',
					'content' => '<h2>Privacy Policy</h2><p>We respect your privacy and are committed to protecting your personal data.</p>'
				));
			}
		}

		// Seed Email Templates
		$templates = array(
			'welcome_email' => array(
				'subject' => 'Welcome to {system_name}',
				'content' => '<h1>Welcome {user_name}!</h1><p>We are glad to have you join our professional platform. We are here to provide you with the best tools to manage your tasks efficiently.</p><h3>What does the platform offer?</h3><ul><li>Comprehensive human resource management</li><li>Advanced permissions system</li><li>Interactive dashboard and direct reports</li></ul><p>You can start now by logging in and completing your profile data.</p>'
			),
			'engagement_reminder' => array(
				'subject' => 'We miss you at {system_name}',
				'content' => '<h1>Hello {user_name},</h1><p>We noticed your absence from the platform for some time. We would like to remind you that there are updates and new tools waiting for you.</p><p>We invite you to log in now and see the latest developments in your dashboard.</p>'
			),
			'password_reset' => array(
				'subject' => 'Password Reset Request - {system_name}',
				'content' => '<h1>Hello {user_name},</h1><p>We received a request to reset your account password.</p><div style="background:#f1f5f9; padding:20px; border-radius:8px; margin:20px 0;">The new temporary password is: <strong style="color:var(--control-primary); font-size:1.2rem;">{new_password}</strong></div><p>Please log in and change your password immediately from your profile settings to ensure your account security.</p>'
			),
			'account_restriction' => array(
				'subject' => 'Alert: Your account has been restricted in {system_name}',
				'content' => '<h1>Sorry {user_name},</h1><p>We would like to inform you that your access to the platform has been temporarily restricted.</p><div style="background:#fff1f2; color:#9f1239; padding:20px; border-radius:8px; margin:20px 0;"><strong>Reason:</strong> {restriction_reason}<br><strong>Expires on:</strong> {expiry_date}</div><p>If you believe this action was taken in error, please contact technical support or the system administrator.</p>'
			),
			'new_login_alert' => array(
				'subject' => 'Security Alert: New login to your account in {system_name}',
				'content' => '<h1>Security Alert</h1><p>Hello {user_name}, a new login to your account has been detected now.</p><div style="background:#f8fafc; padding:20px; border-radius:8px; margin:20px 0;"><strong>Time:</strong> {login_time}<br><strong>Device:</strong> {device_type}<br><strong>IP Address:</strong> {ip_address}</div><p>If you did not perform this operation, please change your password immediately and contact management.</p>'
			),
			'email_verification_otp' => array(
				'subject' => 'Your Verification Code - {system_name}',
				'content' => '<h1>Verify Your Email</h1><p>Welcome, please use the following code to complete the registration process. This code is valid for 10 minutes only.</p><div style="background:#f1f5f9; padding:30px; border-radius:12px; margin:20px 0; text-align:center;"><span style="font-size:32px; font-weight:800; color:var(--control-primary); letter-spacing:10px;">{otp_code}</span></div><p>If you did not start this request, please ignore this email.</p>'
			),
			'password_reset_link' => array(
				'subject' => 'Password Recovery - {system_name}',
				'content' => '<h1>Hello {user_name},</h1><p>We received a request to reset your account password. You can do this by clicking the button below:</p><div style="text-align:center; margin:30px 0;"><a href="{reset_url}" style="background:var(--control-primary); color:#fff; padding:15px 30px; border-radius:8px; text-decoration:none; font-weight:bold; display:inline-block;">Set New Password</a></div><p>This link is valid for 24 hours only. If you did not request a password reset, please ignore this email.</p>'
			),
			'password_recovery_otp' => array(
				'subject' => 'Password Recovery Code - {system_name}',
				'content' => '<h1>Password Recovery</h1><p>Welcome, please use the following verification code to complete the password recovery process. This code is valid for 10 minutes only.</p><div style="background:#f1f5f9; padding:30px; border-radius:12px; margin:20px 0; text-align:center;"><span style="font-size:32px; font-weight:800; color:var(--control-primary); letter-spacing:10px;">{otp_code}</span></div><p>If you did not request a password recovery, please ignore this email and secure your account.</p>'
			)
		);

		foreach ( $templates as $key => $tpl ) {
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT template_key FROM $table_email_templates WHERE template_key = %s", $key ) );
			if ( ! $exists ) {
				$wpdb->insert( $table_email_templates, array(
					'template_key' => $key,
					'subject'      => $tpl['subject'],
					'content'      => $tpl['content']
				) );
			}
		}
	}
}
