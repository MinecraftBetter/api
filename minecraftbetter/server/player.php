<?php

include_once __DIR__ . "/php-minecraft-skin-render/3d.php";

if (!isset($_GET["name"])) return;
$hair = isset($_GET["disableHair"]) && $_GET["disableHair"] !== "false" ? "false" : "true";
$head = isset($_GET["body"]) && $_GET["body"] !== "false" ? "false" : "true";

$player = new render3DPlayer($_GET["name"], null, 10, 5, 10, 5,-2,-20,2, $hair, $head, 'base64', 25, 'true', 'true');
$png = $player->get3DRender();
header('Content-Type: image/png');
echo base64_decode($png);
