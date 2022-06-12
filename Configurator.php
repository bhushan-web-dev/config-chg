<?php

namespace Divido\Chg;

use Divido\Chg\{FileLoader as FL, FileProcessor as FP};
use Throwable;

require_once './classes/FileLoader.class.php';
require_once './classes/FileProcessor.class.php';

/**
 * Class to control main functions for fetching and parsing configuration files
 * Class Configurator
 * @package Divido\Chg
 */
class Configurator
{
    //This variable holds the curent workign directory for config files. It can be modified from constants file.
    public $destinationdir;

    /**
     * Constructor
     * @param string $dir
     */
    function __construct($dir)
    {
        $this->destinationdir = $dir;
    }
 

    /**
     * This method shows the files with specific extension.
     * Sends it to file loading class and then cheks if the files are valid or invalid.
     * @param string $ext
     * @return array
     * @throws Throwable
     */
    function show_files_with_given_extension(string $ext): array
    {
        $fileswithtype = [];
        try {
            /*
            * First create object for loading files from directory and then call get method 
            */
            $confload = new FileLoader($this->destinationdir);
            $filesfromdir = $confload->get_files_from_directory();

            /*
            * Check if the array is empty. 
            */
            if (count($filesfromdir) === 0) {
                echo 'No files found in directory.';
            } else {
                /*
                * Get files now with specific extension.
                * Getting the files above and then filtering them for specific extension is written keeping in mind seperation of concerns.
                */
                $extfiles = $confload->get_files_with_filetype($filesfromdir, $ext);

                /*
                * Check if the array is empty. 
                */
                if (count($extfiles) === 0) {
                    echo 'No files with given type found in directory.';
                } else {
                    /*
                    * Once Loader class work is done above, send the files to Processor class to validate. 
                    */
                    $confprocess = new FileProcessor($this->destinationdir);
                    $fileswithtype = $confprocess->get_valid_invalid_files($extfiles);
                    $_SESSION['validfiles'] = array_filter($fileswithtype, function ($file) {
                                                return $file['status'] === 'Valid';
                                            });
                }
            }
        } catch (Throwable $th) {
            throw $th;
        }
        return $fileswithtype;
    }

    /**
     * This method gets the list of valid files to be parsed afrom session nd the order in which they need to be parsed..
     * Sends them for parsing and then stores the parsed results in session variable
     * @param array $order
     * @return object
     * @throws Throwable
     */
    function validate_and_process(array $order)
    {
        try {
            /*
            * First combine the order array with files array so that order of file merge is established.
            * Then filter the files where merge order is 0 as 0 order means skip for processing.
            */
            $files = array_filter(array_combine($order, array_column($_SESSION['validfiles'], 'filename')), function ($k) {
                return $k !== 0;
            }, ARRAY_FILTER_USE_KEY);

            /*
            * Then sorts the files array and if the count of files is still above 0 send it to file processor.
            */
            ksort($files);

            if (count($files) > 0) {
            
            /*
            * The config values returned from file processor is saved in SESSION for further use. 
            */
                $confprocess = new FileProcessor($this->destinationdir);
                $_SESSION['globalconfig'] = $confprocess->process_files($files);
                return $_SESSION['globalconfig'];
            }
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * This method gets the string for which the value from configuration files is to be retrieved.
     * It checks the values is present in the stored Session variable and returns that value
     * @param string $str
     */
    function get_config(string $str)
    {
        try {
            if ($globalconfig = $_SESSION['globalconfig']) {
                /**
                * Since it's dotted string first explode the string in array to get different parts.
                */
                $keyarr = explode('.', $str);
                $value = null;
                /**
                * Iterate through the keys array above and add each key to the end and verify if it exists, then repeat.
                */
                foreach ($keyarr as $key) {
                    $value = $globalconfig->$key && !isset($value) //This is for the first iteration
                                ? $globalconfig->$key //It gives you value at starting/root level 
                                    : ((isset($value) && !is_null($value->$key)) //This is for next all iterations
                                        ? $value->$key //It gives value at higher level until the end. Last iteration gives you the final value
                                            : 'Not found'); //Send message if not found
                }
                return $value;
            }
        } catch (Throwable $th) {
            throw $th;
        }
    }
}

//EOF