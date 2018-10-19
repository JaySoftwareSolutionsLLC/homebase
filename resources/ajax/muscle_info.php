<?php
//---INCLUDE RESOURCES--------------------------------------------------------------
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');
	include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/constants.php');

//---TEST FOR PROPER POST CONDITIONS------------------------------------------------
	if (!isset($_POST['muscle_id']) || $_POST['muscle_id']) {
		exit;
	}
	else {
		$muscle_id =							$_POST['muscle_id'];
	}

//---CONNECT TO DATABASE------------------------------------------------------------
	$conn = connect_to_db();

//---INITIALIZE GLOBAL VARIABLES ---------------------------------------------------


//---SELECT FROM DATABASE-----------------------------------------------------------


//---CLOSE DATABASE CONNECTION------------------------------------------------------
	$conn->close();
	
	return 'Conway Twitty';
?>