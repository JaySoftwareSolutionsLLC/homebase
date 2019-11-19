<?php

    // File to be called via ajax to load a subset of notes in the form of cards

    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    // Set variables
    $habit_id = $_POST['habit_id'] ?? $_GET['habit_id'] ?? null;
    $status = $_POST['status'] ?? $_GET['status'] ?? 'Completed';

    // Connect to DB
    $conn = connect_to_db();

    // If status is set to Completed then check to see if there is a started habit log. If there is, update that status to 'Completed' rather than inserting new row
    if ($status == 'Completed') {
        $qry = "SELECT COUNT(*) FROM personal_wellness_habit_logs WHERE habit_id = $habit_id";
        $res = $conn->query($qry);
        $row = mysqli_fetch_row($res);
        if ($row[0] > 0) {
            $qry_update = "UPDATE personal_wellness_habit_logs SET status = 'Completed' WHERE habit_id = $habit_id AND status = 'Started' LIMIT 1;";
            if ($conn->query($qry_update) === TRUE) {
                $response_object->type = 'UPDATE';
                $response_object->success = true;
            } else {
                $response_object->type = 'UPDATE';
                $response_object->success = false;
            }
        }
    }

    $qry = "INSERT INTO personal_wellness_habit_logs (habit_id, status) VALUES ($habit_id, '$status');";
    $response_object = new stdClass;
    // echo $qry;
    $response_object->qry = $qry;
    if ($conn->query($qry) === TRUE) {
        $response_object->type = 'INSERT';
        $response_object->success = true;
    } else {
        $response_object->type = 'INSERT';
        $response_object->success = false;
    }
    echo json_encode($response_object);

?>