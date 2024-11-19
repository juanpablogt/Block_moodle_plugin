<?php

class block_student_messaging extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_student_messaging');
        $this->version = 2023010100; // Version number
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Render the message form
        $mform = new message_form();
        $this->content->text .= $OUTPUT->render($mform);

        // Render the message list
        $messages = get_messages_by_course($this->page->course->id);
        $this->content->text .= $OUTPUT->render_from_template('block_student_messaging/messages', array('messages' => $messages));

        return $this->content;
    }

    function has_config() {
        return true;
    }

    function instance_config_save($data) {
        // Save block instance configuration
    }

    function applicable_formats() {
        return array('course'); // This block is applicable to courses
    }
}

?>