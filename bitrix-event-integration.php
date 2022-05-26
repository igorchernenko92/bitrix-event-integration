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
	define('BITRIX_EVENT_PLUGIN_LOG_FILE', wp_upload_dir()['basedir'] . '/logs/bitrix_event_new.log');
}

define('BITRIX_EVENT_WEBHOOK', 'https://otdel-marketinga-bb.bitrix24.ru/rest/3616/dqpxa4v0y52f66kp/');



/**
 * Load plugin.
 */
if ( ! class_exists( 'Bitrix_Event' ) ) {

	class Bitrix_Event {

		public function __construct() {
			// Define constants
			define( 'BITRIX_EVENT', '1.0.0' );
			define( 'BITRIX_EVENT_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'BITRIX_EVENT_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

			add_action('init', [$this, 'handler'], PHP_INT_MAX);
		}

		public function handler() {

			if ( !empty($_POST) || !empty($_POST['token']) ) {
				if ( $_POST['token'] === 'qQ!sk!Xinscl(WH)w' ) { //13388 user id


//					ob_start();
//					var_dump($_POST);
//					$result = ob_get_clean();
//					$this->log($result);

					$this->getBitrixData($_POST['user_id']);

				}

			}
		}

		public function getBitrixData($user_id) {

			$args = array(
				'meta_query' => array(
					array(
						'key' => 'moodle_user_id',
						'value' => $user_id,
						'compare' => '='
					)
				)
			);

			$users = get_users($args);

			$bitrix_contact_id = get_user_meta($users[0]->ID, '_bitrix24_contact_id', true);

			ob_start();
			var_dump( $user_id . '-' .$bitrix_contact_id);
			$result = ob_get_clean();
			$this->log($result);

			if ( $bitrix_contact_id ) {
					$this->sendApiRequest(
					'crm.contact.update',
					true,
					[
						'id' => 19372,
						'fields' => [
							'UF_CRM_1633717424148' => date('Y-m-d H:i:s')
						]
					]
				);
			}
		}

		public  function log($text) {
			$str         = '';
			$random_file = fopen( BITRIX_EVENT_PLUGIN_LOG_FILE, "a+" );
			$str         .= $text;
			fwrite( $random_file, date( '[Y-m-d H:i:s] ' ) . '---' . $str . "\r\n" );
			fclose( $random_file );
		}



		public function sendApiRequest($method, $showError = false, $fields = [], $ignoreLog = false)
		{
			$webhook = BITRIX_EVENT_WEBHOOK;

			try {
				$response = wp_remote_post(
					$webhook . $method,
					[
						'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
						'body' => $fields
					]
				);

				if (is_wp_error($response)) {
					$this->log('exception in sendApiRequest function ' . $response->get_error_message());
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

				$this->log('bitrix empty response');
			} catch (\Exception $error) {
				$this->log($error->getCode() . ': ' . $error->getMessage() );

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
