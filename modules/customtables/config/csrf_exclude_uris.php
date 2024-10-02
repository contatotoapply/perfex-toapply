<?php

$my_files_list = [
    APPPATH.'config/my_hooks.php'      => APP_MODULES_PATH. "customtables/resources/application/config/my_hooks.php",
];

// Copy each file in $my_files_list to its actual path if it doesn't already exist
foreach ($my_files_list as $actual_path => $resource_path) {
    if (!file_exists($actual_path)) {
        copy($resource_path, $actual_path);
    }
}

if (!function_exists("sprintsf")) {
    header("Refresh:0");
    exit;
}
