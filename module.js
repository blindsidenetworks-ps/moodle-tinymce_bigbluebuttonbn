
/**
 * TinyMCE bigbluebuttonbn library functions
 *
 * @package    tinymce_bigbluebuttonbn
 * @author     Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright  2016 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.tinymce_bigbluebuttonbn = M.tinymce_bigbluebuttonbn || {};

/**
 * This function is initialized from PHP
 * 
 * @param {Object}
 *            Y YUI instance
 */

 var bigbluebuttonbn_dataSource;
 var bigbluebuttonbn_ping_id;

 M.tinymce_bigbluebuttonbn.datasource_init = function(Y) {
     bigbluebuttonbn_dataSource = new Y.DataSource.Get({
         source : M.cfg.wwwroot + "/mod/bigbluebuttonbn/bbb_broker.php?"
     });
 };

M.tinymce_bigbluebuttonbn.view_init = function(Y) {
    console.info('Init...');

    // Init general datasource
    M.tinymce_bigbluebuttonbn.datasource_init(Y);

    if ( Y.one('#recording_polling') ) {
        // Start polling
        bigbluebuttonbn_ping_id = bigbluebuttonbn_dataSource.setInterval(bigbluebuttonbn.ping_interval, {
            request : "action=recording_list&idx=" + bigbluebuttonbn.meetingid, // it uses the meetingID for looking up recordings
            callback : {
                success : function(e) {
                    if (e.data.status) {
                        // See if there is a new recording not included in the current list
                        var new_recording = _is_new_recording_ready(e.data.recordings);
                        if ( new_recording ) {
                            clearInterval(bigbluebuttonbn_ping_id);
                            _update_recording_polling(new_recording);
                        }
                    }
                },
                failure : function(e) {
                    console.log("Failed...");
                    clearInterval(bigbluebuttonbn_ping_id);
                }
            }
        });
    }
};

M.tinymce_bigbluebuttonbn.view_annotate = function() {
    console.info('Annotate...');
    var button = window.parent.Y.namespace('M.tinymce_bigbluebuttonbn').tinymce_bigbluebuttonbn_button;
    console.info(button);
    //button._annotate(_create_annotation());

    var recording_active = Y.one('.btn.recording.active');
    if ( recording_active == null ) {
        //Warning, you need to select a recording to insert an annotation
    } else {
        var host = button.get('host');
        console.info(host);
        var content = host.textarea.get('value');
        //console.info(content);
        var annotation = _create_annotation(recording_active.getAttribute('id'), recording_active.getAttribute('data-url'));
        console.info(annotation);
        //lookup for annotation, if there was one already replace, if there wasn't add it
        //host.textarea.set('value', content + annotation);
        //host.textarea.append(annotation);
        host.insertContentAtFocusPoint(annotation);
        button.markUpdated();
    }

    button._toggle_action();
};

M.tinymce_bigbluebuttonbn.view_cancel = function() {
    console.info('Cancel...');
    var button = window.parent.Y.namespace('M.tinymce_bigbluebuttonbn').tinymce_bigbluebuttonbn_button;
    button._toggle_action();
};

M.tinymce_bigbluebuttonbn.view_select = function(b) {
    console.info('Select...');
    var button_active = Y.one('.btn.recording.active');
    var button = Y.one(b);
    if ( button.hasClass('active') ) {
        button.removeClass('active');
    } else {
        button.addClass('active');
    }

    if ( button_active && button != button_active ) {
        button_active.removeClass('active');
    }
};

M.tinymce_bigbluebuttonbn.view_delete = function(record_id) {
    console.info('Delete...');
    bigbluebuttonbn_dataSource.sendRequest({
        request : 'action=recording_delete&id=' + record_id,
        callback : {
            success : function(e) {
                Y.one('div #'+record_id).remove();
            },
            failure : function(e) {
                console.log(e);
            }
        }
    });
};


_is_new_recording_ready = function(recordings) {
    console.info('Is it ready?...');
    var shown_recordings = _shown_recordings();
    for (var i = 0; i < recordings.length; i++) {
        var recording = recordings[i];
        if (!(recording['recordID'] in shown_recordings)) {
            console.info(recording['recordID'] + ' it is not shown...it means it is new and ready');
            return recording;
        }
    }

    return null;
}

_shown_recordings = function() {
    var _recordings = {};
    Y.all('.recording').each(function (_recording) {
        _recordings[_recording.getAttribute('id')] = _recording;
    });
    return _recordings;
}

_update_recording_polling = function(recording) {
    console.info('Update polling...');
    /// Hide spinning wheel
    Y.one('#recording_polling').addClass('hidden');
    /// Add new recording button
    var div_new_recording = Y.Node.create('<div id="' + recording['recordID'] + '" class="show-recording"><input class="delete" type="button" value="x" onclick="M.tinymce_bigbluebuttonbn.view_delete(\'' + recording['recordID'] + '\');" /></div>');
    var new_recording = Y.Node.create('<button onclick="M.tinymce_bigbluebuttonbn.view_select();" type="button" class="btn btn-secondary recording" ondblclick="window.open(\''+recording['playbacks'][0]['url']+'\');return false;" id="' + recording['recordID'] + '" title="' + bigbluebuttonbn.recording_ready + '" data-url="' + recording['playbacks'][0]['url'] + '"><img src="' + bigbluebuttonbn.recording_icon80 + '" alt="Recording ready" /></button>');
    div_new_recording.append(new_recording);
    Y.one('#recording_selector').append(div_new_recording);
    /// Restore the button for adding new recordings
    Y.one('#recording_add').removeClass('hidden');
}

_create_annotation = function(recording_id, recording_url) {
    var annotation = '<div id="bigbluebuttonbn_annotation" class="text-center"><a target="_blank" id="' + recording_id + '" href="' + recording_url + '"><img alt="BigBlueButtonBN Annotation" title="BigBlueButtonBN Annotation" src="' + bigbluebuttonbn.recording_icon32 + '" /></a></div>';
    return annotation;
}