<?php

require_once($CFG->libdir.'/formslib.php');

class message_form extends moodleform {
    function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'subject', get_string('messagetitle', 'block_student_messaging'));
        $mform->setType('subject', PARAM_TEXT);

        $mform->addElement('textarea', 'body', get_string('messagebody', 'block_student_messaging'));
        $mform->setType('body', PARAM_TEXT);

        $mform->addElement('select', 'recipient', get_string('recipient', 'block_student_messaging'));
        $mform->addSelectOptions('recipient', get_recipient_list($this->page->course->id));

        $mform->addElement('submit', 'send', get_string('send', 'block_student_messaging'));

        $mform->closeHeaderBefore('send');
    }

    function validation($data, $files) {
        $errors = array();
        if (empty($data['subject'])) {
            $errors['subject'] = get_string('error:required', 'block_student_messaging');
        }
        if (empty($data['body'])) {
            $errors['body'] = get_string('error:required', 'block_student_messaging');
        }
        return $errors;
    }

    function submission($data, $files) {
        global $DB;

        $message = new stdClass();
        $message->courseid = $this->page->course->id;
        $message->userid = $USER->id;
        $message->subject = $data['subject'];
        $message->body = $data['body'];
        $message->timecreated = time();

        $DB->insert_record('block_student_messaging_messages', $message);

        $recipient = $DB->get_record('user', array('id' => $data['recipient']));
        $message->touserid = $recipient->id;

        // Send the message using Moodle's messaging API
        message_send($message);

        // Display a success message
        $this->_form->addElement('static', 'success', '', get_string('success', 'block_student_messaging'));
    }
}

?>