<?php

namespace Divido\Chg;

require_once './interfaces/FileHandler.interface.php';

class FileLoader implements iterate, fetch {
    public $destinationdir = '';
    
    function __construct($dir) {
        $this->destinationdir = $dir;
    }

    function get_files_from_directory() {
        try {
            $i = 1;
            $destinationdir = new \DirectoryIterator($this->destinationdir);
            foreach ($destinationdir as $file) {
                if ($file->isFile() && $fileext = $file->getExtension()) {
                    $allfiles[] = ['id' => $i, 'filename' => $file->getFilename(), 'extension' => strtoupper($fileext)];
                    $i++;
                }
            }
            return $allfiles;
        } catch (\Throwable $th) {
            echo "Error: There was a problem in accessing contents of given directory.";
            throw $th;
        }
    }

    function fetch_files_with_filetype($files, $ext) {
        try {
            if($files && count($files) > 0) {
                $this->configfiles = array_map(function($item)  use ($ext){
                    if ($item['extension'] === strtoupper($ext)){
                        return $item;
                    }
                }, $files);
                
                return $this->configfiles;
            }
        } catch (\Throwable $th) {
            echo "Error: There was a problem in fetching files.";
            throw $th;
        }
    }
}

//EOF