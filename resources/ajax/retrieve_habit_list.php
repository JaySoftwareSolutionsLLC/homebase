<?php
    // File to be called via ajax to load a subset of notes in the form of cards

    // Include resources
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

    // Connect to DB
    $conn = connect_to_db();
    $habits_list_html = "<ul class='habits-list'>";
    $q = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/homebase/resources/queries/retrieve_active_habit_performance.sql');
    $res = $conn->query($q);
    while ($row = $res->fetch_assoc()) {
        // $row_class_completions_remaining = '';
        $row_class_progess = '';
        $completions_remaining_in_window = $row['frequency_int'] - $row['completed'];
        // Classes for progress: - No-further-action-required-window | No-further-action-required-today | Excess-in-window | Excess-in-day | Deficit > opportunities remaining in window
        if (!empty($row['max_logs_per_day']) && ($row['logged_today'] - $row['max_logs_per_day']) == 0) {
            $row_class_progess = 'no-further-action-today';
        }
        if (($completions_remaining_in_window <= 0 && $row['frequency_type'] == 'at_least') || ($completions_remaining_in_window == 0 && $row['frequency_type'] == 'exactly') || ($completions_remaining_in_window >= 0 && $row['frequency_type'] == 'at_most')
        ) {
            $row_class_progess = 'no-further-action-this-window';
        }
        $habits_list_html .= "<li data-id='" . $row['id'] . "' class='$row_class_progess'><span class='unwrapable'>";
        for ($i = 0; $i < $row['logged_today']; $i++) {
            $habits_list_html .= "<i class='fas fa-check-square'></i>";
        }
        $habits_list_html .= $row['name'] . "</span>";
        $habits_list_html .= "<span class='unwrapable' style='font-size: 0.5rem;'>" . $row['completed'];
        $habits_list_html .= $row['started'] > 0 ? '(+' . $row['started'] . ")" : "";
        switch ($row['frequency_type']) {
            case 'at_least':
                $habits_list_html .= "&ge;";
                break;
            case 'exactly':
                $habits_list_html .= "=";
                break;
            case 'at_most':
                $habits_list_html .= "&le;";
                break;
            default:
                break;
        }
        $habits_list_html .= $row['frequency_int'] . ' This ' . $row['frequency_window'];
    }
    $habits_list_html .= "</ul>";
    echo $habits_list_html;


    /*
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
        $op .= "<li data-id='" . $row['id'] . "' class='$row_class_completions_remaining'><span class='unwrapable'>";
        for ($i = 0; $i < $row['logged_today']; $i++) {
            $op .= "<i class='fas fa-check-square'></i>";
        } 
        $op .= $row['name'] . "</span>" ; $op .="<span class='unwrapable' style='font-size: 0.5rem;'>" . $row['completed']; $op .=$row['started']> 0 ? '(+' . $row['started'] . ")" : "";
        $op .= '/' . $row['frequency_int'] . ' This ' . $row['frequency_window'];
    }
    $op .= "</ul>";
    echo $op;
    */
?>