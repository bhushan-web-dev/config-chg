<?php

namespace Divido\Chg;

use PHPUnit\Framework\TestCase;
require_once './constants.php';

class FileProcessorTest extends TestCase
{
    
    public function testGetValidInvalidFiles() {
        $fileprocessor = new FileProcessor(DIRECTORY);
        $this->assertEquals([ 
            0 => [ 'id' => 1, 'filename' => 'config.also_invalid.json', 'extension' => 'JSON', 'status' => 'Invalid'], 
            1 => [ 'id' => 2, 'filename' => 'config.invalid.json', 'extension' => 'JSON', 'status' => 'Invalid'], 
            2 => [ 'id' => 3, 'filename' => 'config.json', 'extension' => 'JSON', 'status' => 'Valid'], 
            3 => [ 'id' => 4, 'filename' => 'config.local.json', 'extension' => 'JSON', 'status' => 'Valid']
            ]
            , $fileprocessor->get_valid_invalid_files([ 
                [ 'id' => 1, 'filename' => 'config.also_invalid.json', 'extension' => 'JSON'], 
                [ 'id' => 2, 'filename' => 'config.invalid.json', 'extension' => 'JSON'], 
                [ 'id' => 3, 'filename' => 'config.json', 'extension' => 'JSON'], 
                [ 'id' => 4, 'filename' => 'config.local.json', 'extension' => 'JSON']
            ]));
            $fileprocessor2 = new FileProcessor(DIRECTORY2);
            $this->assertEquals([ 
                0 => [ 'id' => 1, 'filename' => 'config.also_invalid.txt', 'extension' => 'TXT', 'status' => 'Invalid'], 
                1 => [ 'id' => 2, 'filename' => 'config.invalid.txt', 'extension' => 'TXT', 'status' => 'Invalid'], 
                2 => [ 'id' => 3, 'filename' => 'config.txt', 'extension' => 'TXT', 'status' => 'Valid'], 
                3 => [ 'id' => 4, 'filename' => 'config.local.txt', 'extension' => 'TXT', 'status' => 'Valid']
            ], $fileprocessor2->get_valid_invalid_files([ 
                [ 'id' => 1, 'filename' => 'config.also_invalid.txt', 'extension' => 'TXT'], 
                [ 'id' => 2, 'filename' => 'config.invalid.txt', 'extension' => 'TXT'], 
                [ 'id' => 3, 'filename' => 'config.txt', 'extension' => 'TXT'], 
                [ 'id' => 4, 'filename' => 'config.local.txt', 'extension' => 'TXT']
            ]));
    }
    
    public function testProcessFiles() {
        $fileprocessor = new FileProcessor(DIRECTORY);
        $this->assertEquals((object) array(
            'environment' => 'development',
            'database' => 
            (object) array(
                'host' => '127.0.0.1',
                'port' => 3306,
                'username' => 'divido',
                'password' => 'divido',
            ),
            'cache' => 
            (object) array(
                'redis' => 
                (object) array(
                    'host' => '127.0.0.1',
                    'port' => 6379,
                ),
            ),
        ), $fileprocessor->process_files([ 1 => 'config.json', 2 => 'config.local.json']));
        
        $this->assertEquals((object) array(
            'environment' => 'production',
            'database' => 
            (object) array(
                'host' => 'mysql',
                'port' => 3306,
                'username' => 'divido',
                'password' => 'divido',
            ),
            'cache' => 
            (object) array(
                'redis' => 
                (object) array(
                    'host' => 'redis',
                    'port' => 6379,
                ),
            ),
        ), $fileprocessor->process_files([ 1 => 'config.local.json', 2 => 'config.json']));
        
        $this->assertEquals((object) array(
            'environment' => 'development',
            'database' => 
            (object) array(
                'host' => '127.0.0.1',
                'port' => 3306,
                'username' => 'divido',
                'password' => 'divido',
            ),
            'cache' => 
            (object) array(
                'redis' => 
                (object) array(
                    'host' => '127.0.0.1',
                    'port' => 6379,
                ),
            ),
        ), $fileprocessor->process_files([ 1 => 'config.local.json']));
    }
    
    public function testGenerateConfigObject() {
        $fileprocessor = new FileProcessor(DIRECTORY);        
        $this->assertNotEquals((object) array( array( 
            'environment' => 'development', 
            'database' => (object) array( 
                'host' => '127.0.0.1', 
                'port' => 3306, 
                'username' => 'divido', 
                'password' => 'divido', ), 
                'cache' => (object) array( 
                    'redis' => (object) array( 
                        'host' => '127.0.0.1', 
                        'port' => 6379, 
                    ), 
                ), 
            ), 
            'cache' => (object) array( 
                'redis' => (object) array( 
                    'host' => '127.0.0.1', 
                    'port' => 6379, 
                ), 
            ), 
        ), 
        $fileprocessor->generate_config_object((object) array( 
            'environment' => 'development', 
            'database' => (object) array( 
                'host' => '127.0.0.1', 
                'port' => 3306, 
                'username' => 'divido', 
                'password' => 'divido', 
            ), 
            'cache' => (object) array( 
                'redis' => (object) array( 
                    'host' => '127.0.0.1', 
                    'port' => 6379, ), 
                ), 
            ), (object) array( )));
        }
}

//EOF
        