<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($URL));

header('Content-Type: application/json');
$data = [ "documentation" => $URL."/swagger/" ];
echo json_encode($data);