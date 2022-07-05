<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($URL) && isset($GITHUB_TOKEN));

$opts = [
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: PHP',
            'Authorization: token '.$GITHUB_TOKEN
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
        "changelog" => $version["body"],
        "url" => $URL . "/minecraftbetter/launcher/download",
        "assets" => $assetsResults
    ]
];

header("Content-Type: application/json");
echo json_encode([
    "code" => 200,
    "date" => date("Y-m-d H:i:s", $filemtime ?? time()),
    "message" => "Success",
    "details" => "Information about the launcher",
    "results" => $results
]);