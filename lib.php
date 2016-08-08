<?php

/**
 * Atto bigbluebuttonbn library functions
 *
 * @package    tinymce_bigbluebuttonbn
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright  2016 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

const MOODLE_TINYMCE_BIGBLUEBUTTONBN_ROOT = '/lib/editor/tinymce/plugins/bigbluebuttonbn/';
const MOODLE_TINYMCE_BIGBLUEBUTTONBN_URL = '/lib/editor/tinymce/plugins/bigbluebuttonbn/bigbluebuttonbn.php';

require_once($CFG->dirroot.'/mod/bigbluebuttonbn/locallib.php');

class tinymce_bigbluebuttonbn extends editor_tinymce_plugin {
    /** @var array list of buttons defined by this plugin */
    protected $buttons = array('bigbluebuttonbn');

    /**
     * Adjusts TinyMCE init parameters for tinymce_bigbluebuttonbn
     *
     * Adds file area restrictions parameters and actual 'bigbluebuttonbn' button
     *
     * @param array $params TinyMCE init parameters array
     * @param context $context Context where editor is being shown
     * @param array $options Options for this editor
     */
    protected function update_init_params(array &$params, context $context, array $options = null) {

        if (!isloggedin() or isguestuser()) {
            // Must be a real user to manage any files.
            return;
        }

        // Add parameters for bigbluebuttonbn
        $params['bigbluebuttonbn'] = array('contextid' => $options['context']->id, 'sesskey' => sesskey() );

        // Position button in toolbar
        if ($row = $this->find_button($params, 'moodlemedia')) {
            // Add button after 'moodlemedia' button.
            $this->add_button_after($params, $row, 'bigbluebuttonbn', 'moodlemedia');
        } else if ($row = $this->find_button($params, 'image')) {
            // If 'moodlemedia' is not found add after 'image'.
            $this->add_button_after($params, $row, 'bigbluebuttonbn', 'image');
        } else {
            // OTherwise add button in the end of the last row.
            $this->add_button_after($params, $this->count_button_rows($params), 'bigbluebuttonbn');
        }

        // Add JS file, which uses default name.
        $this->add_js_plugin($params);
    }

    protected function get_sort_order() {
        return 310;
    }
}
