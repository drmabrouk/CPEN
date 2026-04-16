<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Control_Audit {

	public static function log( $action, $description = '', $data = null ) {
		global $wpdb;
		$user = Control_Auth::current_user();
		$user_id = $user ? $user->id : 'guest';

		$agent = $_SERVER['HTTP_USER_AGENT'];
		$device = self::parse_device($agent);

		$wpdb->insert( $wpdb->prefix . 'control_activity_logs', array(
			'user_id'     => $user_id,
			'action_type' => $action,
			'description' => $description,
			'device_type' => $device,
			'device_info' => $agent,
			'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
			'meta_data'   => $data ? json_encode($data) : null
		) );

		// Enforce 300 record limit
		$log_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}control_activity_logs");
		if ($log_count > 300) {
			$limit_to_delete = $log_count - 300;
			$wpdb->query("DELETE FROM {$wpdb->prefix}control_activity_logs ORDER BY action_date ASC LIMIT $limit_to_delete");
		}
	}

	public static function get_logs() {
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}control_activity_logs ORDER BY action_date DESC LIMIT 300" );
	}

	private static function parse_device($agent) {
		$os = 'Unknown OS';
		$browser = 'Unknown Browser';
		$type = wp_is_mobile() ? 'Mobile' : 'Desktop';

		// Simple OS Detection
		if (preg_match('/windows|win32/i', $agent)) $os = 'Windows';
		elseif (preg_match('/macintosh|mac os x/i', $agent)) $os = 'Mac OS';
		elseif (preg_match('/android/i', $agent)) $os = 'Android';
		elseif (preg_match('/iphone|ipad|ipod/i', $agent)) $os = 'iOS';
		elseif (preg_match('/linux/i', $agent)) $os = 'Linux';

		// Simple Browser Detection
		if (preg_match('/msie/i', $agent) && !preg_match('/opera/i', $agent)) $browser = 'IE';
		elseif (preg_match('/firefox/i', $agent)) $browser = 'Firefox';
		elseif (preg_match('/chrome/i', $agent)) $browser = 'Chrome';
		elseif (preg_match('/safari/i', $agent)) $browser = 'Safari';
		elseif (preg_match('/opera/i', $agent)) $browser = 'Opera';
		elseif (preg_match('/netscape/i', $agent)) $browser = 'Netscape';

		return "$type ($os - $browser)";
	}
}
