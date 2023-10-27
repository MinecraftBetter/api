<?php
/*
 * How to use this:
 * 1. Create a 0.json file in $cachePath, in this file, specify all the directories and files that needs to be deleted from the precedent server.
 * 2. Launch this program, it will generate the file list
 * 3. If an update is needed, replace the content of the old file by the files that needs to be deleted (like in 0.) then launch this program
 */

include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($API_URL) && isset($ADMINS));
header("Content-Type: application/json");

// ----- CONST ----- //

$folder = "gameassets"; // No trailing slash
$noOverride = ["config/", "./options.txt"]; // List of relative paths
$cache_life = 7 * 24 * 3600; // 7j (1h = 3600s)


// ----- PROG ----- //


$valid_users = array_keys($ADMINS);
$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];
$validated = in_array($user, $valid_users) && hash('sha256', $pass) == $ADMINS[$user];
if (!$validated) {
    header('WWW-Authenticate: Basic realm="Accès protégé"');
    http_response_code(401);
    die ("Not authorized");
}

$profile =  $_GET["profile"];
if(!isset($profile)){
    echo json_encode([
        "code" => 400,
        "date" => date("Y-m-d H:i:s", time()),
        "message" => "Error",
        "details" => "Profile name is required"
    ]);
    http_response_code(400);
    exit;
}

$path = $STORAGE_PATH . $folder . "/data/".$profile;

$cachePath = realpath($STORAGE_PATH . $folder . "/.cache/" . $profile);
if(!str_contains($cachePath, $STORAGE_PATH . $folder . "/.cache/")) {
    echo json_encode([
        "code" => 400,
        "date" => date("Y-m-d H:i:s", time()),
        "message" => "Error",
        "details" => "Security error"
    ]);
    http_response_code(400);
    exit;
}

$cachePath .= "/";
$cache = $cachePath . time() . ".json";

if (isset($_GET["update"])) {
    $result = [];
    print("<pre>");
    print ("cd \"" . $path . "\" && git pull");
    exec("cd \"" . $path . "\" && git pull", $result);
    foreach ($result as $line) {
        print($line . "\n");
    }
    print("</pre>");
}

if(!file_exists($path)) {
    echo json_encode([
        "message" => "error, the directory doesn't exists"
    ]);
    exit;
}
$o_dir = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
$o_iter = new RecursiveIteratorIterator($o_dir);

$results = [];
foreach ($o_iter as $o_name) {
    if (!$o_name->isFile()) continue;
    $fullPath = $o_name->getPathname();
    if (preg_match('/(^|\/)\.\w+/i', $fullPath)) continue; // Hidden dir

    $localFilePath = substr($fullPath, strlen($STORAGE_PATH));
    $filePath = substr($localFilePath, strlen($folder . "/data/".$profile."/"));

    $results[$filePath] = [
        "hash" => sha1_file($fullPath),
        "size" => $o_name->getSize(),
        "url" => $API_URL . "/storage?path=" . urlencode($localFilePath),
        "path" => $filePath,
        "override" => override($filePath)
    ];
}

file_put_contents($cache, json_encode($results));
$results["path"] = $cache;
echo json_encode($results);

function override($fPath): bool
{
    global $noOverride;
    foreach ($noOverride as $item)
        if (strpos($fPath, $item) !== false)
            return false;
    return true;
}