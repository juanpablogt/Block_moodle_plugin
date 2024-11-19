<?php
require_once('../../config.php');

// Habilitar la visualización de errores para facilitar la depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener parámetros
$courseid = required_param('courseid', PARAM_INT);
$selected_users = required_param_array('selected', PARAM_INT); // IDs de usuarios seleccionados
$message = required_param('message', PARAM_TEXT);

// Asegurar que el usuario esté autenticado y tenga permisos
require_login($courseid);
$context = context_course::instance($courseid);
require_capability('block/mis_companeros:sendmessage', $context);

// Establecer la URL de la página
$PAGE->set_url('/blocks/mis_companeros/sendmessage.php', ['courseid' => $courseid]);

// Inicializar contadores para envíos exitosos y fallidos
$successful_sends = 0;
$failed_sends = 0;

// Enviar mensajes a los usuarios seleccionados
foreach ($selected_users as $userid) {
    if (!$DB->record_exists('user', ['id' => $userid])) {
        debugging('User ID not found: ' . $userid); // Mensaje de depuración si no se encuentra el ID
        $failed_sends++;
        continue;
    }

    // Obtener el usuario
    $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

    $message_data = new \block_mis_companeros\message_provider();
    $message_data->component = 'block_mis_companeros';
    $message_data->name = 'notification';
    $message_data->userfrom = $USER->id; // Aquí solo asignas el ID
    $message_data->userto = $user->id; // También solo el ID
    $message_data->subject = get_string('newmessagefrom', 'block_mis_companeros', fullname($USER));
    $message_data->fullmessage = $message;
    $message_data->fullmessageformat = FORMAT_PLAIN;
    $message_data->fullmessagehtml = '';
    $message_data->smallmessage = '';
    $message_data->notification = 1; // Marca el mensaje como notificación
    

    // Mensaje de depuración sobre los datos del mensaje
    debugging('Message data: ' . print_r($message_data, true));

    try {
        // Enviar el mensaje
        message_send($message_data);
        $successful_sends++;
    } catch (Exception $e) {
        debugging('Error sending message to user ID: ' . $userid . ' - ' . $e->getMessage());
        $failed_sends++;
    }
}

// Redireccionar al curso con un mensaje de éxito o error
if ($successful_sends > 0) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]), 
             get_string('messagesent', 'block_mis_companeros', $successful_sends), 
             null, 
             \core\output\notification::NOTIFY_SUCCESS);
} else {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]), 
             get_string('messageerror', 'block_mis_companeros'), 
             null, 
             \core\output\notification::NOTIFY_ERROR);
}
