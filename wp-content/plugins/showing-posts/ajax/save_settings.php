<?php

$path = '../../../../wp-load.php'; 
if (file_exists($path)) {
    include_once($path);
} else {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array('status' => 'error', 'message' => 'WordPress environment not found.'));
    exit;
}

check_ajax_referer('save_settings_nonce', 'nonce');

$numberposts = isset($_POST['numberposts']) ? intval($_POST['numberposts']) : 0;

if ($numberposts <= 0) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array('status' => 'error', 'message' => 'Invalid numberposts value.'));
    exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'showing_posts';

$updated_data = array(
    'posts_count' => $numberposts,
);

$where = array(
    'id' => 1, 
);

$data_format = array('%d'); 
$where_format = array('%d');

$result = $wpdb->update($table_name, $updated_data, $where, $data_format, $where_format);

if ($result === false) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array('status' => 'error', 'message' => 'Error updating row: ' . $wpdb->last_error));
    exit;
}


echo json_encode(array('status' => 'success', 'message' => 'Row updated successfully. Number of posts updated: ' . $numberposts));
exit;
?>