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

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_lastlogindate';
$plugin->version = 2020073106;
$plugin->release = 'v0.12';
$plugin->requires = 2014051200;
$plugin->maturity = MATURITY_STABLE;
$plugin->cron = 0;
$plugin->dependencies = array();
