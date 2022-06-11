<?php

namespace Divido\Chg;

interface iterate {
    public function get_files_from_directory();
}

interface fetch {
    public function fetch_files_with_filetype($files, $ext);
}

interface validate {
    public function validate_files($files);
}

interface process {
    public function process_files($files);
}

//EOF