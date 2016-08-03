<?php

/**
 * TinyMCE text editor bigbluebuttonbn version file.
 *
 * @package    atto_bigbluebuttonbn
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright  2016 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2016071301;        // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2014051200;        // Requires this Moodle version.
$plugin->component = 'tinymce_bigbluebuttonbn';  // Full name of the plugin (used for diagnostics).
$plugin->maturity = MATURITY_ALPHA; // Maturity level.
$plugin->release = '1.0';
$plugin->dependencies = array( 'mod_bigbluebuttonbn' => 2016051900 );
