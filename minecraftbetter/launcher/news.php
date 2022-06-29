<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($URL));
header("Content-Type: application/json");

$results = json_decode(file_get_contents($STORAGE_PATH . "news.json"), true);
echo json_encode([
    "code" => 200,
    "date" => date("Y-m-d H:i:s", $filemtime ?? time()),
    "message" => "Success",
    "details" => "News for Minecraft Better",
    "results" => $results
]);