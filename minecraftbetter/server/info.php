<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require __DIR__ . '/php-minecraft-query/MinecraftPing.php';
require __DIR__ . '/php-minecraft-query/MinecraftPingException.php';
require __DIR__ . '/php-minecraft-query/MinecraftQuery.php';
require __DIR__ . '/php-minecraft-query/MinecraftQueryException.php';

use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;


$results = [];


$Ping = new MinecraftPing('minecraftbetter.com', 25565);
try {
    $data = $Ping->Query();
    $results["version"] = $data["version"]["name"];
    $results["description"] = $data["description"]["text"];
    $results["players_online"] = $data["players"]["online"];
    $results["players_max"] = $data["players"]["max"];
    $results["icon"] = "https://api.minecraftbetter.com/minecraftbetter/server/icon";
} catch (MinecraftPingException $e) {
    $results["errors"]["ping"] = $e->getMessage();
} finally {
    $Ping->Close();
}

try {
    $Query = new MinecraftQuery();
    $Query->Connect('minecraftbetter.com', 25565);

    $players = $Query->GetPlayers();
    if (!is_array($players)) $results["players"] = [];
    else foreach ($players as $player)
        $results["players"][] = ["name" => $player, "head" => "https://api.minecraftbetter.com/minecraftbetter/server/player?name=" . $player];
} catch (MinecraftQueryException $e) {
    $results["players"] = [];
    $results["errors"]["query"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode([
    "code" => 200,
    "date" => date("Y-m-d H:i:s", $filemtime ?? time()),
    "message" => "Success",
    "details" => "Minecraft server infos",
    "results" => $results
]);