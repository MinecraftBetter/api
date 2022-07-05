<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($API_URL) && isset($GITHUB_TOKEN));

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
