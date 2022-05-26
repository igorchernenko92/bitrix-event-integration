<?php
/**
 * Defines the version and other meta-info about the plugin
 *
 * Setting the $plugin->version to 0 prevents the plugin from being installed.
 * See https://docs.moodle.org/dev/version.php for more info.
 *
 * @package    local_lastlogindate
 * @copyright  2021 Igor Chernenko <voodi.ua@gmail.com>
 */

function do_post($url, $params) {
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => $params
		)
	);
	$result = file_get_contents($url, false, stream_context_create($options));

}


function local_lastlogindate_before_footer() { //not send every request
	if ( isset($_SESSION['start']) ) { // check if it in not unset
		if( time() > $_SESSION['expire'] ) {
			unset($_SESSION['start']);
			unset($_SESSION['expire']);
		}
	} else {
		 global $USER;
		 $_SESSION['start'] = time();
		 $_SESSION['expire'] = $_SESSION['start'] + 86400; //one day
//		 do_post('https://wordpress-534553-1993492.cloudwaysapps.com/', 'user_id='.$USER->id . '&token=qQ!sk!Xinscl(WH)w');
		 do_post('https://kabacademy.com/', 'user_id='.$USER->id . '&token=qQ!sk!Xinscl(WH)w');
	 }
//	unset($_SESSION['start']);
//	unset($_SESSION['expire']);


}

