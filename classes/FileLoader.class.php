<?php

namespace Divido\Chg;

require_once './interfaces/FileHandler.interface.php';

class FileLoader implements iterate, fetch {
    public $configfiles = [];
    public $allfiles = [];
    public $destinationdir = '';
    public $currentfiletype = '';
    
    function __construct($dir, $filetype) {
        $this->destinationdir = $dir;
        $this->currentfiletype = $filetype;
    }

    function get_files_from_directory() {
        try {
            $destinationdir = new \DirectoryIterator($this->destinationdir);
            foreach ($destinationdir as $file) {
                if ($file->isFile() && $fileext = $file->getExtension()) {
                    $this->allfiles[] = ['filename' => $file->getFilename(), 'extension' => strtoupper($fileext)];
                    // echo $fileinfo->getFilename() . '<br/>';
                    // echo $fileinfo->getExtension() . '<br/>';
                }
            }
            return $this->allfiles;
        } catch (\Throwable $th) {
            echo "Error: There was a problem in accessing contents of given directory.";
            throw $th;
        }
    }

    function fetch_files_with_filetype() {
        try {
            if($this->destinationdir !== '' && $this->destinationdir !== null && $this->allfiles) {
                $this->configfiles = array_map(function($item) {
                    if ($item['extension'] === strtoupper($this->currentfiletype)){
                        return $item['filename'];
                    }
                }, $this->allfiles);
                
                return $this->configfiles;
            }
        } catch (\Throwable $th) {
            echo "Error: There was a problem in fetching files.";
            throw $th;
        }
    }
}

//EOF