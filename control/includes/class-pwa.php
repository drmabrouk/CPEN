<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Control_PWA {

	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'add_pwa_meta' ) );
		add_action( 'init', array( __CLASS__, 'handle_pwa_requests' ) );
	}

	public static function add_pwa_meta() {
		echo '<link rel="manifest" href="' . home_url( '/?control_pwa=manifest' ) . '">' . PHP_EOL;
		echo '<meta name="mobile-web-app-capable" content="yes">' . PHP_EOL;
		echo '<meta name="apple-mobile-web-app-capable" content="yes">' . PHP_EOL;
		echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr( get_bloginfo('name') ) . '">' . PHP_EOL;

		global $wpdb;
		$theme_color = $wpdb->get_var( "SELECT setting_value FROM {$wpdb->prefix}control_settings WHERE setting_key = 'pwa_theme_color'" ) ?: '#000000';
		echo '<meta name="theme-color" content="' . esc_attr( $theme_color ) . '">' . PHP_EOL;
		echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . PHP_EOL;

		$icon = $wpdb->get_var( "SELECT setting_value FROM {$wpdb->prefix}control_settings WHERE setting_key = 'pwa_icon_url'" );
		if ( $icon ) {
			echo '<link rel="apple-touch-icon" href="' . esc_url( $icon ) . '">' . PHP_EOL;
		}

		echo '<script>
			if ("serviceWorker" in navigator) {
				window.addEventListener("load", function() {
					navigator.serviceWorker.register("' . home_url( '/?control_pwa=sw' ) . '").then(function(registration) {
						console.log("Control ServiceWorker registration successful");
					}, function(err) {
						console.log("Control ServiceWorker registration failed: ", err);
					});
				});
			}
		</script>' . PHP_EOL;
	}

	public static function handle_pwa_requests() {
		if ( ! isset( $_GET['control_pwa'] ) ) {
			return;
		}

		$request = $_GET['control_pwa'];

		if ( $request === 'manifest' ) {
			self::serve_manifest();
		} elseif ( $request === 'sw' ) {
			self::serve_service_worker();
		}
	}

	private static function serve_manifest() {
		global $wpdb;
		$settings = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}control_settings", OBJECT_K );

		$manifest = array(
			'name'             => $settings['pwa_app_name']->setting_value ?? 'Control',
			'short_name'        => $settings['pwa_short_name']->setting_value ?? 'Control',
			'start_url'         => home_url( '/' ),
			'display'           => 'standalone',
			'background_color'  => $settings['pwa_bg_color']->setting_value ?? '#ffffff',
			'theme_color'       => $settings['pwa_theme_color']->setting_value ?? '#000000',
			'orientation'       => 'any',
			'icons'             => array()
		);

		$icon_url = $settings['pwa_icon_url']->setting_value ?? '';
		if ( $icon_url ) {
			$manifest['icons'][] = array(
				'src'   => $icon_url,
				'sizes' => '512x512',
				'type'  => 'image/png',
				'purpose' => 'any maskable'
			);
			$manifest['icons'][] = array(
				'src'   => $icon_url,
				'sizes' => '192x192',
				'type'  => 'image/png',
				'purpose' => 'any maskable'
			);
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $manifest );
		exit;
	}

	private static function serve_service_worker() {
		header( 'Content-Type: application/javascript' );
		?>
		const CACHE_NAME = 'control-cache-v3';
		const urlsToCache = [
			'/',
			'<?php echo CONTROL_URL . 'assets/css/style-rtl.css'; ?>',
			'<?php echo CONTROL_URL . 'assets/js/scripts.js'; ?>',
			'https://fonts.googleapis.com/css2?family=Rubik:wght@400;600;700;800&display=swap'
		];

		self.addEventListener('install', (event) => {
			event.waitUntil(
				caches.open(CACHE_NAME).then((cache) => cache.addAll(urlsToCache))
			);
			self.skipWaiting();
		});

		self.addEventListener('activate', (event) => {
			event.waitUntil(
				caches.keys().then((cacheNames) => {
					return Promise.all(
						cacheNames.map((cacheName) => {
							if (cacheName !== CACHE_NAME) {
								return caches.delete(cacheName);
							}
						})
					);
				})
			);
		});

		// Stale-while-revalidate strategy
		self.addEventListener('fetch', (event) => {
			if (event.request.method !== 'GET') return;

			event.respondWith(
				caches.open(CACHE_NAME).then((cache) => {
					return cache.match(event.request).then((cachedResponse) => {
						const fetchedResponse = fetch(event.request).then((networkResponse) => {
							cache.put(event.request, networkResponse.clone());
							return networkResponse;
						});

						return cachedResponse || fetchedResponse;
					});
				})
			);
		});
		<?php
		exit;
	}
}
