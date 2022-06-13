<?php

namespace Divido\Chg;

require_once './interfaces/FileHandlerInterface.php';

/**
 * Class to get files based on specific criteria
 * Class FileLoader
 * @package Divido\Chg
 */
class FileLoader implements iterate, fetch
{
    public $destinationdir = '';


    /**
     * @param string $dir
     */
    function __construct($dir)
    {
        $this->destinationdir = $dir;
    }

    /**
     * Function to get files from directory specified in constructor
     * @return array
     */
    function get_files_from_directory(): array
    {
        try {
            $i = 1;
            /* 
            * Get the contents in given filesystem directory and iterate through them
            */
            $destinationdir = new \DirectoryIterator($this->destinationdir);
            foreach ($destinationdir as $file) {
                /* 
                * Check if it's a file and also get the extension
                */
                if ($file->isFile() && $fileext = $file->getExtension()) {
                    $allfiles[] = ['id' => $i, 'filename' => $file->getFilename(), 'extension' => strtoupper($fileext)];
                    $i++;
                }
            }
            return $allfiles;
        } catch (\Throwable $th) {
            echo 'Error: There was a problem in accessing contents of given directory.';
            throw $th;
        }
    }

    /**
     * Function to filter files with specific extension from array of files provided
     * @param array $files
     * @param string $ext
     */
    function get_files_with_filetype(array $files, string $ext)
    {
        try {
            if ($files && count($files) > 0) {
                $configfiles = array_filter($files, function ($item) use ($ext) {
                    return $item['extension'] === strtoupper($ext);
                });
                return $configfiles;
            }
        } catch (\Throwable $th) {
            echo 'Error: There was a problem in fetching files.';
            throw $th;
        }
    }
}

//EOF