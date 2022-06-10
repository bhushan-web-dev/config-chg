<?php

namespace Divido\Chg;

require_once './interfaces/FileHandler.interface.php';

class FileProcessor implements validate, process {
    public $configfiles = [];
    public $filestoprocess = [];
    public $globalconfiguration;
    
    function __construct($dir, $files) {
        $this->configfiles = $files;
        $this->destinationdir = $dir;
    }

    function validate_files() {
        $filevalidity = [];
        try {
            foreach ($this->configfiles as $configfile) {
                $content = file_get_contents($this->destinationdir . '/' . $configfile);
                if (!empty($content)) {
                    @json_decode($content);
                    $filevalidity[] = ['filename' => $configfile, 'status' => json_last_error() === 0 ? 'valid' : 'invalid'];    
                }
            }
            return $filevalidity;
        } catch (\Throwable $th) {
            echo "Error: There was a problem in validating files.";
            throw $th;
        }
    }

    function process_files($filestoprocess) {
        try {
            $this->filestoprocess = $filestoprocess;
            $config = $this->globalconfiguration ?? new \stdClass();
            foreach($this->filestoprocess as $processfile) {
                // var_dump(json_decode((file_get_contents($this->destinationdir . '/' . $processfile['filename']))));
                $json = json_decode((file_get_contents($this->destinationdir . '/' . $processfile['filename'])));
                // print_r($json);
                $this->globalconfiguration = $this->generate_config_object($json, $config);
                // print_r($json->database->host);
                // var_export((file_get_contents($this->destinationdir . '/' . $processfile['filename'])));
                // $trimmedlinesarr = file($this->destinationdir . '/' . $processfile['filename']);
                // print_r($trimmedlinesarr);
                // $this->generate_config_array($trimmedlinesarr);
            }
            print_r($this->globalconfiguration);
        } catch (\Throwable $th) {
            echo "Error: There was a problem in processing files.";
            throw $th;
        }
    }

    function generate_config_array($arr) {
        foreach ($arr as $line) {
            $temparr = explode(":", $line);
            if($temparr[0] !== '{'){
                // $config[] = 
            }
        }
    }

    function generate_config_object($obj, $config) {
        foreach($obj as $key=>$value) {
            if(is_object($value)){
                // $tempconfig = $config->$key;
                $config->$key = $config->$key ?? new \stdClass();
                // print_r($config);
                $config->$key = $this->generate_config_object($value, $config->$key);
            } else {
                $config->$key = $value;
            }
        }
        return $config;
    }
}

//EOF