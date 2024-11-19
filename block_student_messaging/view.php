<?php

require_once($CFG->libdir.'/outputlib.php');

class block_student_messaging_view {
    function __construct($courseid) {
        $this->courseid = $courseid;
    }

    function render() {
        global $OUTPUT;

        $messages = get_messages_by_course($this->courseid);

        $table = new html_table();
        $table->head = array(get_string('subject', 'block_student_messaging'), get_string('from', 'block_student_messaging'), get_string('date', 'block_student_messaging'));
        $table->data = array();

        foreach ($messages as $message) {
            $row = array();
            $row[] = $message->subject;
            $row[] = $message->from;
            $row[] = date('d/m/Y H:i', $message->timecreated);
            $table->data[] = $row;
        }

        $OUTPUT->heading(get_string('messages', 'block_student_messaging'));
        $OUTPUT->table($table);

        // Render the message thread
        if (isset($_GET['messageid'])) {
            $messageid = $_GET['messageid'];
            $message = get_message($messageid);
            $OUTPUT->heading(get_string('message', 'block_student_messaging'));
            $OUTPUT->text($message->body);
        }
    }
}

$view = new block_student_messaging_view($this->page->course->id);
$view->render();

?>