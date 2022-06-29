<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($URL));
header("Content-Type: application/json");

$opts = [
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: PHP'
        ]
    ]
];
$context = stream_context_create($opts);


$version = json_decode(file_get_contents("https://api.github.com/repos/MinecraftBetter/launcher/releases/latest", false, $context), true);
$assets = json_decode(file_get_contents($version["assets_url"], false, $context), true);
$assetsResults = [];
foreach ($assets as $asset) {
    $assetsResults[$asset["name"]] = [
        "size" => $asset["size"],
        "download_count" => $asset["download_count"],
        "url" => $asset["browser_download_url"]
    ];
}

$results = [
    "name" => "Launcher",
    "copyright" => "© " . date("Y") . " Minecraft Better",
    "latest_version" => [
        "version_number" => $version["tag_name"],
        "date" => $version["created_at"],
        "url" => $URL . "/minecraftbetter/launcher/download",
        "assets" => $assetsResults
    ]
];

echo json_encode([
    "code" => 200,
    "date" => date("Y-m-d H:i:s", $filemtime ?? time()),
    "message" => "Success",
    "details" => "Information about the launcher",
    "results" => $results
]);