<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($API_URL));
header("Content-Type: application/json");

// ----- CONST ----- //

$folder = "gameassets"; // No trailing slash


// ----- PROG ----- //

$path = $STORAGE_PATH . $folder;

$cachePath = $path . "/.cache/";
$caches = [];
$userCacheVersion = intval($_GET["from"]);


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