<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Control_Auth {

	public static function init() {
		if ( ! session_id() && ! headers_sent() ) {
			session_start();
		}

		add_action( 'admin_init', array( __CLASS__, 'restrict_admin_access' ) );
		add_filter( 'show_admin_bar', array( __CLASS__, 'handle_admin_bar' ) );

		// Update activity for logged in users
		add_action( 'init', array( __CLASS__, 'update_last_activity' ) );
	}

	/**
	 * Get authoritative system roles from database.
	 */
	public static function get_roles() {
		global $wpdb;
		$results = $wpdb->get_results( "SELECT role_key, role_name FROM {$wpdb->prefix}control_roles ORDER BY id ASC", OBJECT_K );

		$roles = array();
		foreach ( $results as $key => $row ) {
			$roles[$key] = $row->role_name;
		}

		return $roles;
	}

	/**
	 * Update user's last activity timestamp.
	 */
	public static function update_last_activity() {
		if ( self::is_logged_in() ) {
			$user = self::current_user();
			// Only update if it's a control_staff user (not a wp_ prefix admin)
			if ( $user && is_numeric( $user->id ) ) {
				global $wpdb;
				$wpdb->update(
					$wpdb->prefix . 'control_staff',
					array( 'last_activity' => current_time( 'mysql' ) ),
					array( 'id' => $user->id )
				);

				// Also update the session data if needed to keep it fresh
				$_SESSION['control_last_activity'] = current_time( 'mysql' );
			}
		}
	}

	/**
	 * Sync system roles from database and remove non-explicit roles.
	 */
	public static function sync_roles() {
		global $wpdb;
		$roles = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}control_roles" );

		$keep_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ); // Keep core WP roles

		foreach ( $roles as $role ) {
			$role_key = $role->role_key;
			$keep_roles[] = $role_key;

			$caps = array( 'read' => true, 'upload_files' => true );
			if ( $role_key === 'admin' ) {
				$caps['manage_options'] = true;
			}

			if ( ! get_role( $role_key ) ) {
				add_role( $role_key, $role->role_name, $caps );
			}
		}

		// Only remove roles that are explicitly identified as "legacy" (e.g. from old brand)
		// For safety in this transition, we'll avoid the destructive removal loop
		// unless we have a specific list of branded keys to target.
	}

	/**
	 * Check if current user has a specific permission.
	 */
	public static function has_permission( $permission ) {
		if ( current_user_can( 'manage_options' ) ) {
			return true; // WP Super Admin has all permissions
		}

		$user = self::current_user();
		if ( ! $user ) return false;

		global $wpdb;
		$role = $wpdb->get_row( $wpdb->prepare( "SELECT permissions FROM {$wpdb->prefix}control_roles WHERE role_key = %s", $user->role ) );

		if ( ! $role ) return false;

		$perms = json_decode( $role->permissions, true );

		if ( isset($perms['all']) && $perms['all'] ) return true;

		return isset( $perms[$permission] ) && $perms[$permission];
	}

	/**
	 * Restrict WP Dashboard access.
	 */
	public static function restrict_admin_access() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// If not System Admin and not Site Admin (administrator), redirect away
		if ( is_admin() && ! current_user_can( 'manage_options' ) ) {
			wp_redirect( home_url() );
			exit;
		}
	}

	/**
	 * Hide admin bar for non-admins.
	 */
	public static function handle_admin_bar( $show ) {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		return false;
	}

	public static function login( $phone, $password ) {
		global $wpdb;
		$table = $wpdb->prefix . 'control_staff';

		$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE phone = %s", $phone ) );

		if ( $user && password_verify( $password, $user->password ) ) {
			if ( $user->is_restricted ) {
				// Check if restriction has expired
				if ( ! empty($user->restriction_expiry) && strtotime($user->restriction_expiry) < current_time('timestamp') ) {
					$wpdb->update( $table, array( 'is_restricted' => 0, 'restriction_reason' => null, 'restriction_expiry' => null ), array( 'id' => $user->id ) );
				} else {
					return new WP_Error( 'restricted', __( 'هذا الحساب مقيد حالياً. يرجى التواصل مع الإدارة.', 'control' ) );
				}
			}
			$wpdb->update( $table, array( 'last_activity' => current_time( 'mysql' ) ), array( 'id' => $user->id ) );
			return self::set_user_session( $user );
		}
		return false;
	}

	private static function set_user_session( $user ) {
		$_SESSION['control_user_id']   = $user->id;
		$_SESSION['control_phone']     = $user->phone;
		$_SESSION['control_user_role'] = $user->role;
		$_SESSION['control_user_first_name'] = $user->first_name;
		$_SESSION['control_user_last_name']  = $user->last_name;

		// Load language preference from user meta if available
		$wp_u = get_user_by( 'login', $user->username ) ?: get_user_by( 'email', $user->email );
		if ( $wp_u ) {
			$lang = get_user_meta( $wp_u->ID, 'control_lang', true );
			if ( $lang ) {
				$_SESSION['control_lang'] = $lang;
				setcookie( 'control_lang', $lang, time() + ( 86400 * 30 ), COOKIEPATH, COOKIE_DOMAIN );
			}
		}

		return true;
	}

	public static function register_user( $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'control_staff';

		// Check if phone or email already exists
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE phone = %s OR (email IS NOT NULL AND email != '' AND email = %s)", $data['phone'], $data['email'] ) );
		if ( $exists ) {
			return new WP_Error( 'duplicate', __( 'رقم الهاتف أو البريد الإلكتروني مسجل بالفعل.', 'control' ) );
		}

		$email = ! empty( $data['email'] ) ? sanitize_email( $data['email'] ) : null;
		$insert_data = array(
			'first_name' => $data['first_name'] ?? '',
			'last_name'  => $data['last_name'] ?? '',
			'phone'      => $data['phone'],
			'username'   => $data['username'] ?? $data['phone'],
			'email'      => $email,
			'password'   => password_hash( $data['password'], PASSWORD_DEFAULT ),
			'raw_password' => $data['password'],
			'role'       => $data['role'] ?? 'coach',
		);

		// Include extra profile fields if present in $data
		$extra_fields = array(
			'gender', 'degree', 'specialization', 'institution', 'graduation_year',
			'home_country', 'state', 'address', 'employer_name', 'employer_country',
			'work_phone', 'work_email', 'job_title'
		);

		foreach ($extra_fields as $field) {
			if (isset($data[$field])) {
				$insert_data[$field] = $data[$field];
			}
		}

		$inserted = $wpdb->insert( $table, $insert_data );

		if ( $inserted ) {
			$user_id = $wpdb->insert_id;

			// Sync with WordPress native user
			if ( ! empty( $insert_data['email'] ) ) {
				wp_insert_user( array(
					'user_login' => $insert_data['username'],
					'user_pass'  => $data['password'],
					'user_email' => $insert_data['email'],
					'first_name' => $insert_data['first_name'],
					'last_name'  => $insert_data['last_name'],
					'role'       => $insert_data['role']
				) );
			}

			$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $user_id ) );
			self::set_user_session( $user );
			return $user_id;
		}

		return new WP_Error( 'db_error', __( 'حدث خطأ أثناء حفظ البيانات.', 'control' ) );
	}

	public static function logout() {
		unset( $_SESSION['control_user_id'] );
		unset( $_SESSION['control_phone'] );
		unset( $_SESSION['control_user_role'] );
		unset( $_SESSION['control_user_first_name'] );
		unset( $_SESSION['control_user_last_name'] );
		unset( $_SESSION['control_lang'] );

		// Clear native WordPress session and cookies
		wp_logout();
		wp_clear_auth_cookie();

		if ( session_id() ) {
			session_destroy();
		}
	}

	public static function is_logged_in() {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		return isset( $_SESSION['control_user_id'] );
	}

	public static function current_user() {
		if ( current_user_can( 'manage_options' ) ) {
			$wp_user = wp_get_current_user();
			return (object) array(
				'id'   => 'wp_' . $wp_user->ID,
				'username' => $wp_user->user_login,
				'role' => 'admin',
				'name' => $wp_user->display_name
			);
		}

		if ( ! isset( $_SESSION['control_user_id'] ) ) return null;

		return (object) array(
			'id'   => $_SESSION['control_user_id'],
			'phone' => $_SESSION['control_phone'],
			'role' => $_SESSION['control_user_role'],
			'first_name' => $_SESSION['control_user_first_name'],
			'last_name'  => $_SESSION['control_user_last_name'],
			'name'       => $_SESSION['control_user_first_name'] . ' ' . $_SESSION['control_user_last_name']
		);
	}

	public static function is_admin() {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		$user = self::current_user();
		return $user && $user->role === 'admin';
	}

	public static function get_all_users() {
		global $wpdb;
		$table = $wpdb->prefix . 'control_staff';
		return $wpdb->get_results( "SELECT * FROM $table ORDER BY id DESC" );
	}

	/**
	 * Generate a secure password reset token.
	 */
	public static function generate_reset_token( $user_id ) {
		global $wpdb;
		$token = bin2hex( random_bytes( 32 ) );
		$expiry = date( 'Y-m-d H:i:s', strtotime( '+24 hours' ) );

		// Invalidate old tokens for this user
		$wpdb->update( "{$wpdb->prefix}control_reset_tokens", array( 'is_used' => 1 ), array( 'user_id' => $user_id, 'is_used' => 0 ) );

		$wpdb->insert( "{$wpdb->prefix}control_reset_tokens", array(
			'user_id' => $user_id,
			'token'   => $token,
			'expiry'  => $expiry
		) );

		return $token;
	}

	/**
	 * Verify a password reset token.
	 */
	public static function verify_reset_token( $token ) {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}control_reset_tokens WHERE token = %s AND is_used = 0 AND expiry > NOW()",
			$token
		) );

		return $row;
	}

	/**
	 * Centralized Permissions Registry
	 */
	public static function get_permissions_registry() {
		return array(
			'dashboard'     => array( 'label' => __( 'عرض لوحة التحكم', 'control' ), 'category' => __( 'النظام', 'control' ) ),
			'users_view'    => array( 'label' => __( 'عرض قائمة الكوادر', 'control' ), 'category' => __( 'الكوادر', 'control' ) ),
			'users_manage'  => array( 'label' => __( 'إضافة وتعديل الكوادر', 'control' ), 'category' => __( 'الكوادر', 'control' ) ),
			'users_delete'  => array( 'label' => __( 'حذف الكوادر', 'control' ), 'category' => __( 'الكوادر', 'control' ) ),
			'roles_manage'  => array( 'label' => __( 'إدارة الصلاحيات والأدوار', 'control' ), 'category' => __( 'النظام', 'control' ) ),
			'settings_manage' => array( 'label' => __( 'إدارة إعدادات النظام', 'control' ), 'category' => __( 'النظام', 'control' ) ),
			'audit_view'    => array( 'label' => __( 'عرض سجل النشاطات', 'control' ), 'category' => __( 'النظام', 'control' ) ),
			'backup_manage' => array( 'label' => __( 'إدارة النسخ الاحتياطي', 'control' ), 'category' => __( 'النظام', 'control' ) ),
			'emails_send'   => array( 'label' => __( 'إرسال البريد الإلكتروني', 'control' ), 'category' => __( 'النظام', 'control' ) ),
		);
	}
}
