<?php

if (!isset($_GET["name"])) return;

$type = "head";
if (isset($_GET["type"]) && in_array($_GET["type"], ["head", "body"])) $type = $_GET["type"];

$result = null;
if ($type == "head")
    $result = json_decode(file_get_contents("https://minecraft-api.com/api/skins/".$_GET["name"]."/head/10.5/10/25/json"), true)["head"];
if ($type == "body")
    $result = json_decode(file_get_contents("https://minecraft-api.com/api/skins/".$_GET["name"]."/body/10.5/10/0/0/25/json"), true)["skin"];

header('Content-Type: image/png');
echo base64_decode($result);
