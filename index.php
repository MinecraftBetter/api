<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($API_URL));

header('Content-Type: application/json');
$data = [ "documentation" => $API_URL."/swagger/" ];
echo json_encode($data);