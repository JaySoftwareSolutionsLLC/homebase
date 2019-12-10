<?php
    // File to be called via ajax to load a subset of notes in the form of cards

    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    // Connect to DB
    $conn = connect_to_db();
    echo return_habit_list_html($conn);
?>