<?php
/**
* Plugin Name: Bitrix24 - Event - Integration
* Plugin URI: ''
* Description: Allows you to send data to database after bitrix events occurs
* Version: 1.0.0
* Author: igorchernenko92
* Author URI: https://github.com/igorchernenko92
* License:
* Text Domain: bitrix-event-integration
* Domain Path: /languages/
*/

if (!defined('ABSPATH')) {
    exit();
}

/*
* Require for `is_plugin_active` function.
*/
require_once ABSPATH . 'wp-admin/includes/plugin.php';


if (!defined('BITRIX_EVENT_PLUGIN_LOG_FILE')) {
	define('BITRIX_EVENT_PLUGIN_LOG_FILE', wp_upload_dir()['basedir'] . '/logs/bitrix_event' . md5(get_option('siteurl')) . '.log');
}

define('BITRIX_EVENT_WEBHOOK', 'https://otdel-marketinga-bb.bitrix24.ru/rest/1092/4sb8l1akg2dhl4l1/');



/**
 * Load plugin.
 */
if ( ! class_exists( 'Property_Builder' ) ) {

	class Bitrix_Event {

		public function __construct() {
			// Define constants
//			if ( ! defined( 'BUILDER_NAME' ) )
//				define( 'BUILDER_NAME', 'Builder' );
//
//			if ( ! defined( 'BUILDER_DOMAIN' ) )
//				define( 'BUILDER_DOMAIN', 'builder' );

			define( 'BITRIX_EVENT', '1.0.0' );
			define( 'BITRIX_EVENT_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'BITRIX_EVENT_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );


			add_action('init', [$this, 'handler'], PHP_INT_MAX);


//			// Include classes
//			include( BUILDER_PLUGIN_DIR . '/includes/class-builder-general.php' );
//
//			// Include functions
//			include( BUILDER_PLUGIN_DIR . '/functions/builder-properties.php' );
//
//			// Include wordpress pluggable
//			include( BUILDER_PLUGIN_DIR . '/functions/builder-notifications.php' );


		}

		public function handler() {

//			$entry = $this->sendApiRequest('crm.' . 'quote' . '.get', false, ['id' => 1174], true);

//			var_dump($entry);

//			var_dump($entry);
			if (!empty($_POST) || !empty($_POST['auth']) || !empty($_POST['auth']['application_token'])) {
				if ( $_POST['auth']['application_token'] === 'oyoah52w8odq8lrvqstg72e3javl9918' ) {



					$metaKey = '_wc_bitrix24_deal_id';
					$entryID = $_POST['data']['FIELDS']['ID'];

					switch ($_POST['event']) {
						case 'ONCRMDEALUPDATE':
							$metaKey = '_wc_bitrix24_deal_id';
							$entryType = 'deal';
							break;
						default:
							// Nothing
							break;
					}

					$entry = $this->sendApiRequest('crm.' . 'quote' . '.get', false, ['id' => $entryID], true);

					ob_start();
					var_dump($entry);
					$result = ob_get_clean();
					error_log('custom bitrix');
					error_log($result);

					$orders = get_posts(
						[
							'post_type' => 'shop_order',
							'post_status' => 'any',
							'numberposts' => 1,
							'meta_query' => [
								[
									'key' => $metaKey,
									'value' => $entryID
								]
							],
							'fields' => 'ids'
						]
					);

//            var_dump($orders);

				}

			}




		}

		public function sendApiRequest($method, $showError = false, $fields = [], $ignoreLog = false)
		{
			$webhook = BITRIX_EVENT_WEBHOOK;


			if (!$ignoreLog) {
				//log
			}

			try {
				$response = wp_remote_post(
					$webhook . $method,
					[
						'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
						'body' => $fields
					]
				);


//				var_dump($response);
				if (!$ignoreLog) {
				//log
				}

				if (is_wp_error($response)) {
					throw new \Exception(
						$response->get_error_message(),
						(int) $response->get_error_code()
					);
				}

				$body = $response['body'];

				if (!empty($body)) {
					$result = json_decode(str_replace('\'', '"', $body), true);

					if (!$ignoreLog) {
						//log
					}

					if (isset($result['result'])) {
						return (array) $result['result'];
					}

					if (!empty($result['error'])) {
						if ($showError) {
							throw new \Exception(
								isset($result['error_message'])
									? esc_html($result['error_message'])
									: esc_html($result['error_description']),
								(int) $result['error']
							);
						}
					}
				}

				//log
			} catch (\Exception $error) {
			//log

				if ($showError) {
					printf(
						'<div data-ui-component="wcbitrix24notice" class="error notice notice-error">'
						. '<p><strong>Error (%s)</strong>: %s</p></div>',
						esc_html($error->getCode()),
						esc_html($error->getMessage())
					);
				}
			}

			return [];
		}

	}



	$instance = new Bitrix_Event();
}
