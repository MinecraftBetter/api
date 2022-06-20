<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($URL));

// ----- CONST ----- //

$folder = "gameassets";
$noOverride = ["config/"]; // List of relative paths


// ----- PROG ----- //

$path = $STORAGE_PATH . $folder; // No trailing slash
$o_dir = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
$o_iter = new RecursiveIteratorIterator($o_dir);

$results = [];
foreach ($o_iter as $o_name) {
    if (!$o_name->isFile()) continue;
    $fullPath = $o_name->getPathname();
    if (preg_match('/(^|\/)\.\w+/i', $fullPath)) continue; // Hidden dir

    $localFilePath = substr($fullPath, strlen($STORAGE_PATH));
    $pathFolders = preg_split("/\//", $localFilePath);
    if (count($pathFolders) > 2) {
        $rootFolder = $pathFolders[1];
        $filePath = substr($localFilePath, strlen($folder . "/" . $rootFolder . "/"));
    }
    else {
        $rootFolder = ".";
        $filePath = substr($localFilePath, strlen($folder . "/"));
    }

    $results[$rootFolder][$filePath] = [
        "hash" => sha1_file($fullPath),
        "size" => $o_name->getSize(),
        "url" => $URL . "/storage?path=" . $localFilePath,
        "path" => $filePath,
        "override" => override($rootFolder."/".$filePath)
    ];
}

header("Content-Type: application/json");
echo json_encode(["code" => 200, "message" => "Success", "details" => "Custom assets for Minecraft Better", "results" => $results]);

function override($fPath): bool
{
    global $noOverride;
    foreach ($noOverride as $item)
        if (strpos($fPath, $item) !== false)
            return false;
    return true;
}