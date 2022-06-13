<?php

namespace Divido\Chg;

use PHPUnit\Framework\TestCase;
require_once './constants.php';

class FileLoaderTest extends TestCase
{
    public function testGetFilesFromDirectory() {
        $fileloader = new FileLoader(DIRECTORY);
        $this->assertEquals([ 
            [ 'id' => 1, 'filename' => 'config.also_invalid.json', 'extension' => 'JSON'], 
            [ 'id' => 2, 'filename' => 'config.invalid.json', 'extension' => 'JSON'], 
            [ 'id' => 3, 'filename' => 'config.json', 'extension' => 'JSON'], 
            [ 'id' => 4, 'filename' => 'config.local.json', 'extension' => 'JSON']
        ], $fileloader->get_files_from_directory());
        $this->assertNotEquals([ 
            [ 'id' => 1, 'filename' => 'config.also_invalid.txt', 'extension' => 'JSON'], 
            [ 'id' => 2, 'filename' => 'config.invalid.txt', 'extension' => 'JSON'], 
            [ 'id' => 3, 'filename' => 'config.txt', 'extension' => 'JSON'], 
            [ 'id' => 4, 'filename' => 'config.local.txt', 'extension' => 'JSON']
        ], $fileloader->get_files_from_directory());
    }

    public function testGetFilesWithFiletype() {
        $fileloader = new FileLoader(DIRECTORY);
        $this->assertEquals([ 
            [ 'id' => 1, 'filename' => 'config.also_invalid.json', 'extension' => 'JSON'], 
            [ 'id' => 2, 'filename' => 'config.invalid.json', 'extension' => 'JSON'], 
            [ 'id' => 3, 'filename' => 'config.json', 'extension' => 'JSON'], 
            [ 'id' => 4, 'filename' => 'config.local.json', 'extension' => 'JSON']
        ], $fileloader->get_files_with_filetype([[ 'id' => 1, 'filename' => 'config.also_invalid.json', 'extension' => 'JSON'], 
        [ 'id' => 2, 'filename' => 'config.invalid.json', 'extension' => 'JSON'], 
        [ 'id' => 3, 'filename' => 'config.json', 'extension' => 'JSON'], 
        [ 'id' => 4, 'filename' => 'config.local.json', 'extension' => 'JSON'],
        [ 'id' => 1, 'filename' => 'config.also_invalid.txt', 'extension' => 'TXT'], 
        [ 'id' => 2, 'filename' => 'config.invalid.txt', 'extension' => 'TXT'], 
        [ 'id' => 3, 'filename' => 'config.txt', 'extension' => 'TXT'], 
        [ 'id' => 4, 'filename' => 'config.local.txt', 'extension' => 'TXT']], 'json'));
    
        $this->assertNotEquals([ 
            [ 'id' => 1, 'filename' => 'config.also_invalid.json', 'extension' => 'JSON'], 
            [ 'id' => 2, 'filename' => 'config.invalid.json', 'extension' => 'JSON'], 
            [ 'id' => 3, 'filename' => 'config.json', 'extension' => 'JSON'], 
            [ 'id' => 4, 'filename' => 'config.local.json', 'extension' => 'JSON']
        ], $fileloader->get_files_with_filetype([[ 'id' => 1, 'filename' => 'config.also_invalid.json', 'extension' => 'JSON'], 
        [ 'id' => 2, 'filename' => 'config.invalid.json', 'extension' => 'JSON'], 
        [ 'id' => 3, 'filename' => 'config.json', 'extension' => 'JSON'], 
        [ 'id' => 4, 'filename' => 'config.local.json', 'extension' => 'JSON'],
        [ 'id' => 1, 'filename' => 'config.also_invalid.txt', 'extension' => 'TXT'], 
        [ 'id' => 2, 'filename' => 'config.invalid.txt', 'extension' => 'TXT'], 
        [ 'id' => 3, 'filename' => 'config.txt', 'extension' => 'TXT'], 
        [ 'id' => 4, 'filename' => 'config.local.txt', 'extension' => 'TXT']], 'txt'));
    }
}
