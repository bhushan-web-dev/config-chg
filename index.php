<?php

namespace Divido\Chg;

require_once './classes/FileLoader.class.php';
require_once './classes/FileProcessor.class.php';

$confload = new FileLoader('./fixtures', 'json');
// var_dump($con);
echo "<pre>";
$all_files = $confload->get_files_from_directory();
// print_r($all_files);
$config_files = $confload->fetch_files_with_filetype();
// print_r($config_files);
echo "</pre>";

$confprocess = new FileProcessor($confload->destinationdir, $config_files);
echo "<pre>";
$validity = $confprocess->validate_files();
// print_r($validity);
$valid_files = array_filter($validity, function($file) {
    if ($file['status'] === 'valid'){
        return $file['filename'];
    }
});
// print_r($valid_files);
$configuration = $confprocess->process_files($valid_files);
// print_r($configuration);
echo "</pre>";

//EOF