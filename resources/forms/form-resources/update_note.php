<?php
    //---INCLUDE RESOURCES--------------------------------------------------------------
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
    include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

    //---RETRIEVE POST VARIABLES--------------------------------------------------------
    $column_name = $_POST['column-name'];
    $value = $_POST['value'];
    if ( is_null( $value ) ) {
        $value = 'null';
    }
    $row_id = $_POST['id'];

    //---CONNECT TO DATABASE------------------------------------------------------------
    $conn = connect_to_db();

    //---UPDATE-------------------------------------------------------------------------
    $qry = "  UPDATE `personal_notes`
            SET $column_name = $value
            WHERE id = $row_id; ";
    //echo $qry;
    if ( $conn->query($qry) === TRUE) {
    	$feedback_str .= "<li>$column_name successfully updated to $value at id $row_id.</li>";
	} else {
    	$feedback_str .= "<li>$column_name update failed! <br/> Error with query: $qry </br> $conn->error </br></li>";
    }
    echo $feedback_str;
?>