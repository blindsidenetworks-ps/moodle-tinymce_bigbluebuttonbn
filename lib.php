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
        global $USER;

        if (!isloggedin() or isguestuser()) {
            // Must be a real user to manage any files.
            return;
        }
        if (!isset($options['maxfiles']) or $options['maxfiles'] == 0) {
            // No files allowed - easy, do not load anything.
            return;
        }

        // Add parameters for filemanager
        $params['bigbluebuttonbn'] = array('usercontext' => context_user::instance($USER->id)->id);
        foreach (array('itemid', 'context', 'areamaxbytes', 'maxbytes', 'subdirs', 'return_types') as $key) {
            if (isset($options[$key])) {
                if ($key === 'context' && is_object($options[$key])) {
                    // Just context id is enough
                    $params['bigbluebuttonbn'][$key] = $options[$key]->id;
                } else {
                    $params['bigbluebuttonbn'][$key] = $options[$key];
                }
            }
        }

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
