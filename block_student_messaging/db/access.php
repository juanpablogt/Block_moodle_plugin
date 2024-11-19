<?php

// Database schema management
function xmldb_block_student_messaging_install() {
    // Create the messages table
    $table = new xmldb_table('block_student_messaging_messages');
    $table->addField('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
    $table->addField('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
    $table->addField('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
    $table->addField('subject', XMLDB_TYPE_TEXT, null, null, null);
    $table->addField('body', XMLDB_TYPE_TEXT, null, null, null);
    $table->addField('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
    // ... other fields ...
    $dbman = $DB->get_manager();
    $dbman->create_table($table);
}

// Data retrieval functions
function get_messages_by_course($courseid) {
    global $DB;
    $sql = "SELECT * FROM {block_student_messaging_messages} WHERE courseid = :courseid";
    $params = array('courseid' => $courseid);
    return $DB->get_records_sql($sql, $params);
}

// Data storage functions
function insert_message($message) {
    global $DB;
    $DB->insert_record('block_student_messaging_messages', $message);
}

// Utility functions
function get_recipient_list($courseid) {
    global $DB;
    $sql = "SELECT u.id, u.username FROM {user} u JOIN {course_enrolments} ce ON u.id = ce.userid WHERE ce.courseid = :courseid";
    $params = array('courseid' => $courseid);
    return $DB->get_records_sql($sql, $params);
}

?>