<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($API_URL));

header("Location: https://swagger-api.justbetter.fr/");
