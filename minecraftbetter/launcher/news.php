<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($URL));
header("Content-Type: application/json");

$results = [];
$results[] = ["title" => "A title", "description" => "The body message", "date" => "2048-01-01 00:01:00"];
echo json_encode([
    "code" => 200,
    "date" => date("Y-m-d H:i:s", $filemtime ?? time()),
    "message" => "Success",
    "details" => "News for Minecraft Better",
    "results" => $results
    ]);