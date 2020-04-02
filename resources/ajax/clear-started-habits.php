<?php

    // File to be called via ajax to delete all started habit logs

    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    // Connect to DB
    $conn = connect_to_db();
    $date = $_POST['date'] ?? $_GET['date'] ?? null;
    $habit_id = $_POST['habit_id'] ?? $_GET['habit_id'] ?? null;
    // Hesitant to implement the below line. Could lead to clearing ALL habits_logs ever if $date and habit_id were null
    // $status = $_POST['status'] ?? $GET['status'] ?? 'started'; // Clearing started is default behavior

    $qry = " DELETE FROM personal_wellness_habit_logs WHERE status = 'Started' ";
    if (!is_null($date)) {
        $qry .= " AND DATE(datetime) = DATE('$date') ";
    }
    if (!is_null($habit_id)) {
        $qry .= " AND habit_id = $habit_id ";
    }
    $response_object = new stdClass;
    $response_object->post = $_POST;
    $response_object->qry = $qry;
    if ($conn->query($qry)) {
        $response_object->type = 'DELETE';
        $response_object->success = true;
    } else {
        $response_object->type = 'DELETE';
        $response_object->success = false;
        $response_object->error = $conn->error;
    }
    echo json_encode($response_object);

?>