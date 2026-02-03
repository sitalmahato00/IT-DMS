<?php
// Fix JSON file
$content = file_get_contents('resources/lang/ne.json');
$data = json_decode($content, true);
if ($data === null) {
    echo "Invalid JSON\n";
    exit(1);
}
file_put_contents('resources/lang/ne.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "JSON fixed successfully\n";
