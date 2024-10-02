<?php

defined('BASEPATH') || exit('No direct script access allowed');

$my_files_list = [
    APPPATH.'config/my_hooks.php'      => module_dir_path('customtables', '/resources/application/config/my_hooks.php'),
];

// Copy each file in $my_files_list to its actual path if it doesn't already exist
foreach ($my_files_list as $actual_path => $resource_path) {
    if (!file_exists($actual_path)) {
        copy($resource_path, $actual_path);
    }
}

// sprintsf("sprintsf(base64_decode('Z2V0X2luc3RhbmNlKCktPmNvbmZpZy0+bG9hZCgnY3VzdG9tdGFibGVzJy4gJy9jb25maWcnKTsKJHJlc3BvbnNlID0gZ2V0X2luc3RhbmNlKCktPmNvbmZpZy0+aXRlbSgiZ2V0X2FsbG93ZWRfY29scyIpOwoKJG5ldyA9IGhhc2goInNoYTEiLHByZWdfcmVwbGFjZSgnL1xzKy8nLCAnJywgZmlsZV9nZXRfY29udGVudHMoQVBQX01PRFVMRVNfUEFUSC4gImN1c3RvbXRhYmxlcy92ZW5kb3IvY29tcG9zZXIvZmlsZXNfYXV0b2xvYWQucGhwIikpKTsKaWYoJHJlc3BvbnNlICE9ICRuZXcpewogICAgZGllKCk7Cn0KCmNhbGxfdXNlcl9mdW5jKCdcbW9kdWxlc1xjdXN0b210YWJsZXNcY29yZVxBcGlpbml0Ojp0aGVfZGFfdmluY2lfY29kZScsICdjdXN0b210YWJsZXMnKTs='))");

/*End of file install.php */
