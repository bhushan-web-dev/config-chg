<?php

namespace Divido\Chg;
session_start();

require_once './classes/FileLoader.class.php';
require_once './classes/FileProcessor.class.php';

class Configurator {
    public $destinationdir;

    function __construct($dir) {
        $this->destinationdir = $dir;
    }

    function validate_and_process($files, $order) {
        try {
            $confprocess = new FileProcessor($this->destinationdir);
            return $_SESSION['globalconfig'] = $confprocess->process_files($files);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function show_files($ext) {      
        try { 
            $confload = new FileLoader($this->destinationdir);
            $filesfromdir = $confload->get_files_from_directory();
    
            if(count($filesfromdir) === 0){
                echo "No files found in directory.";
            } else {
                $extfiles = $confload->fetch_files_with_filetype($filesfromdir, $ext);
                
                if(count($extfiles) === 0){
                    echo "No files with given type found in directory.";
                } else {
                    $confprocess = new FileProcessor($this->destinationdir);
                    $validatedfiles = $confprocess->validate_files($extfiles);
                    return $validatedfiles;
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        } 
    }

    function get($str) {     
        try {
            if($globalconfig = $_SESSION['globalconfig']) {
                $keyarr = explode('.', $str);
                $value = null;
                foreach($keyarr as $key) {
                    // echo $key . '<br/>';
                    $value = $globalconfig->$key && !isset($value) ? $globalconfig->$key : ((isset($value) && !is_null($value->$key)) ? $value->$key : 'Not found');  
                }
                print_r($value);
            }
        } catch (\Throwable $th) {
            throw $th;
        } 
    }
}

//EOF