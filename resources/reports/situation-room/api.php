<?php
    header('Content-type: application/json');

    // Inputs
    $date_of_birth = $_POST['dob'];
    $age = $_POST['age'];

    // Logic
    $response = new \stdClass;
    $response->date_of_birth = $date_of_birth;
    $response->age = $age;

    // Output
    echo json_encode($response);
?>