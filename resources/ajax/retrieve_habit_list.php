<?php
    // File to be called via ajax to load a subset of notes in the form of cards

    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    // Connect to DB
    $conn = connect_to_db();

    $op = "<ul class='habits-list'>";
    $q = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/homebase/resources/queries/retrieve_active_habit_performance.sql');
    $res = $conn->query($q);
    while($row = $res->fetch_assoc()) {
        $row_class_completions_remaining = '';
        $completions_remaining_in_window = $row['frequency_int'] - $row['completed'];
        //$unstarted_completions_remaining_in_window = $row['frequency_int'] - ($row['completed'] - $row['started']);
        //echo $row['name'] . ": $completions_remaining_in_window | " . $row['frequency_int'] . "|" . $row['completed'] . "<br />";
        if ($row['started'] > 0) {
        $row_class_completions_remaining = 'in-progress';
        }
        else if ($completions_remaining_in_window > 1) {
        $row_class_completions_remaining = 'many-remaining';
        }
        else if ($completions_remaining_in_window == 1) {
        $row_class_completions_remaining = 'one-remaining';
        }
        else if ($completions_remaining_in_window == 0) {
        $row_class_completions_remaining = 'none-remaining';
        }
        else {
        $row_class_completions_remaining = 'negative-remaining';
        }
        $op .= "<li data-id='" . $row['id'] . "' class='$row_class_completions_remaining'><span class='unwrapable'>" . $row['name'] . "</span>" ; $op .="<span class='unwrapable' style='font-size: 0.5rem;'>" . $row['completed']; $op .=$row['started']> 0 ? '(+' . $row['started'] . ")" : "";
        $op .= '/' . $row['frequency_int'] . ' This ' . $row['frequency_window'];
    }
    $op .= "</ul>";
    echo $op;
?>