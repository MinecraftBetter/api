<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($API_URL) & isset($MINECRAFT_URL));


require __DIR__ . '/php-minecraft-query/MinecraftPing.php';
require __DIR__ . '/php-minecraft-query/MinecraftPingException.php';
require __DIR__ . '/php-minecraft-query/MinecraftQuery.php';
require __DIR__ . '/php-minecraft-query/MinecraftQueryException.php';

use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;


$results = [];


try {
    $Ping = new MinecraftPing($MINECRAFT_URL, 25565);
    $data = $Ping->Query();
    $results["version"] = $data["version"]["name"];
    $results["description"] = $data["description"]["text"];
    $results["players_online"] = $data["players"]["online"];
    $results["players_max"] = $data["players"]["max"];
    $results["icon"] = $API_URL . "/minecraftbetter/server/icon";
} catch (MinecraftPingException $e) {
    header('Content-Type: application/json');
    http_response_code(503);
    echo json_encode([
        "code" => 503,
        "date" => date("Y-m-d H:i:s", $filemtime ?? time()),
        "message" => "Error",
        "details" => $e->getMessage()
    ]);
    return;
} finally {
    if (isset($Ping)) $Ping->Close();
}

try {
    $Query = new MinecraftQuery();
    $Query->Connect('mika.justbetter.fr', 25565);

    $players = $Query->GetPlayers();
    if (!is_array($players)) $results["players"] = [];
    else {
        sort($players, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($players as $player)
            $results["players"][] = ["name" => $player, "head" => $API_URL . "/minecraftbetter/server/player?name=" . $player];
    }
} catch (MinecraftQueryException $e) {
    $results["players"] = [];
    $results["errors"]["query"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode([
    "code" => 200,
    "date" => date("Y-m-d H:i:s", $filemtime ?? time()),
    "message" => "Success",
    "details" => "Minecraft server info",
    "results" => $results
]);