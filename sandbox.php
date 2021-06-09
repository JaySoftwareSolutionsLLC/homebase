<?php

include($_SERVER["DOCUMENT_ROOT"] . '/homebase/resources/resources.php');

$sd = return_date_from_str('2020-07-01');
$ed = return_date_from_str('2020-10-31');

$conn = connect_to_db();

echo "Ricks Pre Tax Income:" . return_ricks_pre_tax_income($conn, $sd, $ed, 7.8) . "<br/>";

?>