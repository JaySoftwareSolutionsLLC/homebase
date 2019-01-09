<?php 

function connect_to_local_db() {
	date_default_timezone_set('America/New_York');
	$serv = 'localhost';
	$user = 'root';
	$pass = 'Bc6219bAj';
	$db = 'jaysoftw_homebase';
	return new mysqli($serv, $user, $pass, $db);
}

function connect_to_db() {
	date_default_timezone_set('America/New_York');
	$serv = 'localhost';
	$user = 'jaysoftw_brett';
	$pass = 'Su944jAk127456';
	$db = 'jaysoftw_homebase';
	return new mysqli($serv, $user, $pass, $db);
}

function set_post_value($string) {
	return (isset($_POST[$string]) && ($_POST[$string]) != '') ? $_POST[$string] : null;
}

function post_is_set($string) {
	return (isset($_POST[$string]) && $_POST[$string] != '') ? true : false;
}

?>