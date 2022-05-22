<?php
namespace Event\Bitrix24\Integration\Includes;

class Helper
{
//    public static function log($message, $data = [], $type = 'info')
//    {
//        $settings = get_option(Bootstrap::OPTIONS_KEY);
//        $enableLogging = isset($settings['enabled_logging']) && (int) $settings['enabled_logging'] === 1;
//
//        if (!$enableLogging) {
//            return;
//        }
//
//        try {
//            Bootstrap::$logger->log('wcbx24', $message, (array) $data, $type);
//        } catch (\Exception $exception) {
//            // Nothing
//        }
//    }


	public static function log($text) {
		$str         = '';
		$random_file = fopen( "bitrix_integration.log", "a+" );
		$str         .= $text;
		fwrite( $random_file, date( '[Y-m-d H:i:s] ' ) . '---' . $str . "\r\n" );
		fclose( $random_file );
	}


}
