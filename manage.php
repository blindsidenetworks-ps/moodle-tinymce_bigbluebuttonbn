<?php

/**
 * @package    tinymce_bigbluebuttonbn
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright  2016 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../../../config.php');
require_once('manage_form.php');
require_once($CFG->libdir.'/filestorage/file_storage.php');

$itemid = required_param('itemid', PARAM_INT);
$maxbytes = optional_param('maxbytes', 0, PARAM_INT);
$subdirs = optional_param('subdirs', 0, PARAM_INT);
$accepted_types = optional_param('accepted_types', '*', PARAM_RAW); // TODO not yet passed to this script
$return_types = optional_param('return_types', null, PARAM_INT);
$areamaxbytes = optional_param('areamaxbytes', FILE_AREA_MAX_BYTES_UNLIMITED, PARAM_INT);
$contextid = optional_param('context', SYSCONTEXTID, PARAM_INT);

$context = context::instance_by_id($contextid);
if ($context->contextlevel == CONTEXT_MODULE) {
    // Module context.
    $cm = $DB->get_record('course_modules', array('id' => $context->instanceid));
    require_login($cm->course, true, $cm);
} else if (($coursecontext = $context->get_course_context(false)) && $coursecontext->id != SITEID) {
    // Course context or block inside the course.
    require_login($coursecontext->instanceid);
    $PAGE->set_context($context);
} else {
    // Block that is not inside the course, user or system context.
    require_login();
    $PAGE->set_context($context);
}
if (isguestuser()) {
    // Guests can never manage files.
    print_error('noguest');
}

$title = get_string('manageareafiles', 'tinymce_bigbluebuttonbn');

$PAGE->set_url('/lib/editor/tinymce/plugins/bigbluebuttonbn/manage.php');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');

if ($return_types !== null) {
    $return_types = $return_types ^ 1; // links are allowed in textarea but never allowed in filemanager
}

$options = array(
    'subdirs' => $subdirs,
    'maxbytes' => $maxbytes,
    'maxfiles' => -1,
    'accepted_types' => $accepted_types,
    'areamaxbytes' => $areamaxbytes,
    'return_types' => $return_types,
    'context' => $context
);

$usercontext = context_user::instance($USER->id);
$fs = get_file_storage();
$files = $fs->get_directory_files($usercontext->id, 'user', 'draft', $itemid, '/', !empty($subdirs), false);
$filenames = array();
foreach ($files as $file) {
    $filenames[] = ltrim($file->get_filepath(), '/'). $file->get_filename();
}

$mform = new tinymce_bigbluebuttonbn_manage_form(null,
        array('options' => $options, 'draftitemid' => $itemid, 'files' => $filenames),
        'post', '', array('id' => 'tinymce_bigbluebuttonbn_manageform'));

if ($data = $mform->get_data()) {
    if (!empty($data->deletefile)) {
        foreach (array_keys($data->deletefile) as $filename) {
            $filepath = '/';
            if (!empty($subdirs) && strlen(dirname($filename))  ) {
                $filepath = '/'. dirname($filename). '/';
            }
            if ($file = $fs->get_file($usercontext->id, 'user', 'draft', $itemid,
                    $filepath, basename($filename))) {
                $file->delete();
            }
        }
        $filenames = array_diff($filenames, array_keys($data->deletefile));
        $mform = new tinymce_bigbluebuttonbn_manage_form(null,
                array('options' => $options, 'draftitemid' => $itemid, 'files' => $filenames),
                'post', '', array('id' => 'tinymce_bigbluebuttonbn_manageform'));
    }
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
