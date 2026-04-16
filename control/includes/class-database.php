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
				array('id' => 'first_name', 'label' => 'الاسم الأول', 'enabled' => true, 'required' => true),
				array('id' => 'last_name', 'label' => 'اسم العائلة', 'enabled' => true, 'required' => true),
				array('id' => 'phone', 'label' => 'رقم الهاتف', 'enabled' => true, 'required' => true),
				array('id' => 'email', 'label' => 'البريد الإلكتروني', 'enabled' => true, 'required' => true),
				array('id' => 'password', 'label' => 'كلمة المرور', 'enabled' => true, 'required' => true),
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
			'auth_heading_text'      => 'مرحباً بك في نظام الإدارة',
			'auth_subtitle_text'     => 'نظام الإدارة المتكامل والأكثر تطوراً',
			'auth_layout_template'   => 'centered',
			'auth_title_visible'     => '1',
			'auth_subtitle_visible'  => '1',
			'policies_content'       => '<h2>الشروط والأحكام</h2><p>هنا تدرج سياسات النظام والشروط القانونية المنظمة للعمل.</p>',
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
					'title' => 'الشروط والأحكام العامة',
					'content' => $existing_policy
				));
			} else {
				$wpdb->insert($table_policies, array(
					'title' => 'سياسة الخصوصية',
					'content' => '<h2>سياسة الخصوصية</h2><p>نحن نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية.</p>'
				));
			}
		}

		// Seed Email Templates
		$templates = array(
			'welcome_email' => array(
				'subject' => 'مرحباً بك في منصة {system_name}',
				'content' => '<h1>أهلاً بك يا {user_name}!</h1><p>يسعدنا انضمامك إلى منصتنا الاحترافية. نحن هنا لنوفر لك أفضل الأدوات لإدارة مهامك بكفاءة.</p><h3>ماذا تقدم لك المنصة؟</h3><ul><li>إدارة شاملة للكوادر البشرية</li><li>نظام صلاحيات متطور</li><li>لوحة تحكم تفاعلية وتقارير مباشرة</li></ul><p>يمكنك البدء الآن بتسجيل الدخول واستكمال بيانات ملفك الشخصي.</p>'
			),
			'engagement_reminder' => array(
				'subject' => 'نفتقد وجودك في {system_name}',
				'content' => '<h1>أهلاً {user_name}،</h1><p>لاحظنا غيابك عن المنصة لفترة من الوقت. نود تذكيرك بأن هناك تحديثات وأدوات جديدة بانتظارك.</p><p>ندعوك لتسجيل الدخول الآن والاطلاع على آخر المستجدات في لوحة التحكم الخاصة بك.</p>'
			),
			'password_reset' => array(
				'subject' => 'طلب استعادة كلمة المرور - {system_name}',
				'content' => '<h1>أهلاً {user_name}،</h1><p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.</p><div style="background:#f1f5f9; padding:20px; border-radius:8px; margin:20px 0;">كلمة المرور المؤقتة الجديدة هي: <strong style="color:var(--control-primary); font-size:1.2rem;">{new_password}</strong></div><p>يرجى تسجيل الدخول وتغيير كلمة المرور فوراً من إعدادات ملفك الشخصي لضمان أمان حسابك.</p>'
			),
			'account_restriction' => array(
				'subject' => 'تنبيه: تم تقييد حسابك في {system_name}',
				'content' => '<h1>عذراً {user_name}،</h1><p>نود إبلاغك بأنه تم تقييد وصولك إلى المنصة مؤقتاً.</p><div style="background:#fff1f2; color:#9f1239; padding:20px; border-radius:8px; margin:20px 0;"><strong>السبب:</strong> {restriction_reason}<br><strong>ينتهي في:</strong> {expiry_date}</div><p>إذا كنت تعتقد أن هذا الإجراء تم بالخطأ، يرجى التواصل مع الدعم الفني أو مدير النظام.</p>'
			),
			'new_login_alert' => array(
				'subject' => 'تنبيه أمني: دخول جديد لحسابك في {system_name}',
				'content' => '<h1>تنبيه أمني</h1><p>أهلاً {user_name}، لقد تم رصد عملية دخول جديدة لحسابك الآن.</p><div style="background:#f8fafc; padding:20px; border-radius:8px; margin:20px 0;"><strong>الوقت:</strong> {login_time}<br><strong>الجهاز:</strong> {device_type}<br><strong>عنوان IP:</strong> {ip_address}</div><p>إذا لم تكن أنت من قام بهذه العملية، يرجى تغيير كلمة المرور فوراً والتواصل مع الإدارة.</p>'
			),
			'email_verification_otp' => array(
				'subject' => 'رمز التحقق الخاص بك - {system_name}',
				'content' => '<h1>تحقق من بريدك الإلكتروني</h1><p>أهلاً بك، يرجى استخدام الرمز التالي لإكمال عملية التسجيل في المنصة. هذا الرمز صالح لمدة 10 دقائق فقط.</p><div style="background:#f1f5f9; padding:30px; border-radius:12px; margin:20px 0; text-align:center;"><span style="font-size:32px; font-weight:800; color:var(--control-primary); letter-spacing:10px;">{otp_code}</span></div><p>إذا لم تكن أنت من بدأ هذا الطلب، يرجى تجاهل هذا البريد.</p>'
			),
			'password_reset_link' => array(
				'subject' => 'استعادة كلمة المرور - {system_name}',
				'content' => '<h1>أهلاً {user_name}،</h1><p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك. يمكنك القيام بذلك من خلال الضغط على الزر أدناه:</p><div style="text-align:center; margin:30px 0;"><a href="{reset_url}" style="background:var(--control-primary); color:#fff; padding:15px 30px; border-radius:8px; text-decoration:none; font-weight:bold; display:inline-block;">تعيين كلمة مرور جديدة</a></div><p>هذا الرابط صالح لمدة 24 ساعة فقط. إذا لم تطلب استعادة كلمة المرور، يرجى تجاهل هذا البريد.</p>'
			),
			'password_recovery_otp' => array(
				'subject' => 'رمز استعادة كلمة المرور - {system_name}',
				'content' => '<h1>استعادة كلمة المرور</h1><p>أهلاً بك، يرجى استخدام رمز التحقق التالي لاستكمال عملية استعادة كلمة المرور الخاصة بحسابك. هذا الرمز صالح لمدة 10 دقائق فقط.</p><div style="background:#f1f5f9; padding:30px; border-radius:12px; margin:20px 0; text-align:center;"><span style="font-size:32px; font-weight:800; color:var(--control-primary); letter-spacing:10px;">{otp_code}</span></div><p>إذا لم تطلب استعادة كلمة المرور، يرجى تجاهل هذا البريد وتأمين حسابك.</p>'
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
