<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$opts = [
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: PHP'
        ]
    ]
];
$context = stream_context_create($opts);

$isWin = isset($_GET["os"]) && $_GET["os"] == "windows";

$version = json_decode(file_get_contents("https://api.github.com/repos/MinecraftBetter/launcher/releases/latest", false, $context), true);
$assets = json_decode(file_get_contents($version["assets_url"], false, $context), true);
foreach ($assets as $asset){
    if (!endsWith($asset["name"], $isWin ? ".exe": ".jar")) continue;
    header("Location: ".$asset["browser_download_url"]);
    exit();
}

http_response_code(404);
echo "No asset has been found";

function endsWith($haystack, $needle): bool
{
    $length = strlen($needle);
    return $length <= 0 || substr($haystack, -$length) === $needle;
}
