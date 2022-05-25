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
	define('BITRIX_EVENT_PLUGIN_LOG_FILE', wp_upload_dir()['basedir'] . '/logs/bitrix_event11102021.log');
}

define('BITRIX_EVENT_WEBHOOK', 'https://otdel-marketinga-bb.bitrix24.ru/rest/1092/4sb8l1akg2dhl4l1/');



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

				}

			}
		}

		public function bitrixDataAdapter($entryType, $entry) {
			$data = [
				'entry_id' => intval($entry['ID']),
				'entry_type' => $entryType,
//				'entry_status' => $entry['STATUS_ID'],
				'entry_status' => $entry['STAGE_ID'],
				'data' => json_encode($entry) ?? '',
			];

			return $data;
		}

		//TODO: add post as argument
		public function getBitrixData() {
			$entryType = 'deal';
//					$metaKey = '_wc_bitrix24_deal_id';
			$entryID = $_POST['data']['FIELDS']['ID'];

//			$pieces = explode("DEAL_", $_POST['document_id'][2]);
//			$entryID = $pieces[1];

//			switch ($_POST['event']) {
//				case 'ONCRMDEALUPDATE':
//				case 'ONCRMDEALADD':
////							$metaKey = '_wc_bitrix24_deal_id';
//					$entryType = 'deal';
//					break;
//				case 'ONCRMQUOTEUPDATE':
//				case 'ONCRMQUOTEADD':
////							$metaKey = '_wc_bitrix24_quote_id';
//					$entryType = 'quote';
//					break;
//				default:
//					// Nothing
//					break;
//			}

			$entry = $this->sendApiRequest('crm.' . $entryType . '.get', false, ['id' => $entryID], true);


			if (empty($entry)) {
				$this->log('no entry in Bitrix24 by data' . $entryID);

				return false;
			}

			$adaptedData = $this->bitrixDataAdapter($entryType, $entry);

//			ob_start();
//			var_dump($this->bitrixDataAdapter($entryType, $entry));
//			$result = ob_get_clean();

//			$this->log($result);

			return $adaptedData;

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

		public  function log($text) {
			$str         = '';
			$random_file = fopen( BITRIX_EVENT_PLUGIN_LOG_FILE, "a+" );
			$str         .= $text;
			fwrite( $random_file, date( '[Y-m-d H:i:s] ' ) . '---' . $str . "\r\n" );
			fclose( $random_file );
		}

	}

	$instance = new Bitrix_Event();
}
