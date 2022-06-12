<?php

namespace Divido\Chg;

use stdClass;
use Throwable;

require_once './interfaces/FileHandler.interface.php';

/**
 * Class to perform specific processing on files
 * Class FileLoader
 * @package Divido\Chg
 */
class FileProcessor implements validate, process
{
    public $globalconfiguration;

    /**
     * @param string $dir
     */
    function __construct($dir)
    {
        $this->destinationdir = $dir;
    }

    /**
     * Function to determine if a file is valid or invalid and then list it acoordingly
     * @param array $configfiles
     * @return array
     */
    function get_valid_invalid_files(array $configfiles): array
    {
        $filevalidity = [];
        try {
            foreach ($configfiles as $configfile) {
                $content = file_get_contents($this->destinationdir . '/' . $configfile['filename']);
                if (!empty($content)) {
                    /* 
                    * Checks if the content can be decoded as JSON
                    */
                    @json_decode($content);
                    /* 
                    * If the JSON decode above did not fail, file is valid, invalid otherwise
                    */
                    $configfile['status'] = json_last_error() === 0 ? 'Valid' : 'Invalid';
                    $filevalidity[] = $configfile;
                }
            }
            return $filevalidity;
        } catch (Throwable $th) {
            echo 'Error: There was a problem in validating files.';
            throw $th;
        }
    }

    /**
     * Function to process given list of files and then generate merged/combined configuration out of them
     * @param array $filestoprocess
     * @return object
     * @throws Throwable
     */
    function process_files(array $filestoprocess): object
    {
        try {            
            /* 
            * $config stores latest merged config object but for first file an empty object is passed
            */
            $config = $this->globalconfiguration ?? new stdClass();
            foreach ($filestoprocess as $processfile) {          
                /* 
                * Get the JSON object of file in current iteration
                */
                $json = json_decode(file_get_contents($this->destinationdir . '/' . $processfile));          
                /* 
                * Pass the JSON object to generate config obect out of current file. 
                * $config either contains empty object (first iteration) or latest merged configuration for subsequent iterations.
                */
                $this->globalconfiguration = $this->generate_config_object($json, $config);
            }
            return $this->globalconfiguration;
        } catch (Throwable $th) {
            echo 'Error: There was a problem in processing files.';
            throw $th;
        }
    }

    /**
     * @param object $obj
     * @param object $config
     * @return object
     */
    function generate_config_object(object $obj, object $config): object
    {          
        /* 
        * Iterate through each object as key value pair
        */
        foreach ($obj as $key => $value) {                      
            /* 
            * If the current value is an object, we'll need to recursively iterate until a flat property is found
            */
            if (is_object($value)) {          
                /* 
                * For the first file being processed the key won't be there in config object we are preparing.
                * So for first iteration set empty object.
                * For subsequent iteration the key might already be present (from any previous file) or absent.
                * Set it accordingly to be passed to recursive call on next line.
                */
                $config->$key = $config->$key ?? new stdClass();          
                /* 
                * Recursive call to dig through all the values in current object until flat property is encountered.
                */
                $config->$key = $this->generate_config_object($value, $config->$key);
            } else {          
                /* 
                * If a flat property (non-object) is found in JSON, set it and move to next iteration.
                */
                $config->$key = $value;
            }
        }          
        /* 
        * $config at the end of each recursive call will get updated and once all the recursion is done,
        * it'll contain the final config object.
        */
        return $config;
    }
}

//EOF