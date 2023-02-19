<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($API_URL));
header("Content-Type: application/json");

// ----- CONST ----- //

$folder = "gameassets"; // No trailing slash


// ----- PROG ----- //

$path = $STORAGE_PATH . $folder;

$userProfile =  $_GET["profile"];
$userCacheVersion = intval($_GET["from"]);

if(!isset($userProfile)){
    echo json_encode([
        "code" => 400,
        "date" => date("Y-m-d H:i:s", time()),
        "message" => "Error",
        "details" => "Profile name is required"
    ]);
    http_response_code(400);
    exit;
}

$cachePath = realpath($path . "/.cache/" . $userProfile);
if(!$cachePath){
    echo json_encode([
        "code" => 404,
        "date" => date("Y-m-d H:i:s", time()),
        "message" => "Error",
        "details" => "Profile hasn't been found"
    ]);
    http_response_code(404);
    exit;
}
if(!str_contains($cachePath, $path . "/.cache/")) {
    echo json_encode([
        "code" => 400,
        "date" => date("Y-m-d H:i:s", time()),
        "message" => "Error",
        "details" => "Security error"
    ]);
    http_response_code(400);
    exit;
}

$cachePath .= "/";
$caches = [];
if ($handle = opendir($cachePath)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry == "." || $entry == "..") continue;
        $fileName = intval(str_replace(".json", "", $entry));
        if ($fileName >= $userCacheVersion) $caches[$fileName] = json_decode(file_get_contents($cachePath . $entry));
    }
    closedir($handle);
}

$finalJson = json_encode([
    "code" => 200,
    "date" => date("Y-m-d H:i:s", time()),
    "message" => "Success",
    "details" => "Custom assets for Minecraft Better",
    "results" => $caches
]);
echo $finalJson;