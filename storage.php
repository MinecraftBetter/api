<?php
include $_SERVER['DOCUMENT_ROOT']."/config.php";
assert(isset($STORAGE_PATH) && isset($API_URL));

// ----- CONST ----- //

$url = $API_URL."/storage";
$idKey = "path";


// ----- PROG ----- //

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle): bool
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

$id = isset($_GET[$idKey]) ? trim($_GET[$idKey], "/\\") : "";
$filename = realpath($STORAGE_PATH . $id);
if(!str_contains($filename, $STORAGE_PATH)){
    header("Content-Type: application/json");
    echo json_encode(["code" => 400, "message" => "Illegal path"]);
    exit();
}


// Not found
if (!file_exists($filename)) {
    header("Content-Type: application/json");
    echo json_encode(["code" => 404, "message" => "Not found", "details" => "File not found at " . $filename]);
    exit();
}

// Folder
if (!is_file($filename)) {
    header("Content-Type: application/json");
    $data = ["code" => 200, "message" => "Folder content", "details" => $filename];

    $parent = trim(dirname($id), "/\\");
    if ($parent == ".") $data["result"]["parent"] = $url;
    else if ($parent != "") $data["result"]["parent"] = $url."?".$idKey."=" . $parent;

    $files = array_filter(scandir($filename), fn($f) => $f != '.' && $f != '..');
    $data["result"]["content"] = array_map(fn($f) => $url."?".$idKey."=" . $id . "/" . $f, $files);

    echo json_encode($data);
    exit();
}

// Send local file
header("Content-Type: application/octet-stream");
header("Content-Transfer-Encoding: Binary");
header("Content-Length:" . filesize($filename));
header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
readfile($filename);
exit();