<?php

/**
 * Atto bigbluebuttonbn library functions
 *
 * @package    atto_bigbluebuttonbn
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright  2016 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/lib.php');

function bigbluebuttonbn_is_annotated( $content ) {
    return strpos($content, 'bigbluebuttonbn_annotation');
}