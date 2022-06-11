<?php

namespace Divido\Chg;

require_once './interfaces/FileHandler.interface.php';

class FileProcessor implements validate, process {
    public $globalconfiguration;
    
    function __construct($dir) {
        $this->destinationdir = $dir;
    }

    function validate_files($configfiles) {
        $filevalidity = [];
        try {
            foreach ($configfiles as $configfile) {
                $content = file_get_contents($this->destinationdir . '/' . $configfile['filename']);
                if (!empty($content)) {
                    @json_decode($content);
                    $configfile['status'] = json_last_error() === 0 ? 'valid' : 'invalid';
                    $filevalidity[] = $configfile;    
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
            $config = $this->globalconfiguration ?? new \stdClass();
            foreach($filestoprocess as $processfile) {
                $json = json_decode((file_get_contents($this->destinationdir . '/' . $processfile)));
                $this->globalconfiguration = $this->generate_config_object($json, $config);
            }
            return $this->globalconfiguration;
        } catch (\Throwable $th) {
            echo "Error: There was a problem in processing files.";
            throw $th;
        }
    }

    function generate_config_object($obj, $config) {
        foreach($obj as $key=>$value) {
            if(is_object($value)){
                $config->$key = $config->$key ?? new \stdClass();
                $config->$key = $this->generate_config_object($value, $config->$key);
            } else {
                $config->$key = $value;
            }
        }
        return $config;
    }
}

//EOF