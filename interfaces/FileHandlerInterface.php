<?php

/**
* Interfaces to specify operations which can be performed on given files.
* Each class implementing the below interfaces need to make sure that they implement it.
* Otherwise it'll through error
* @package Divido\Chg
*/
namespace Divido\Chg;

interface iterate
{
    public function get_files_from_directory();
}

interface fetch
{
    public function get_files_with_filetype(array $files, string $ext);
}

interface validate
{
    public function get_valid_invalid_files(array $files);
}

interface process
{
    public function process_files(array $files);
}

//EOF