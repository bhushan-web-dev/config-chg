<?php

namespace Divido\Chg;

use PHPUnit\Framework\TestCase;
require_once './constants.php';

class ConfiguratorTest extends TestCase
{
    public function testShowFilesWithGivenExtension() {
        $configurator = new Configurator(DIRECTORY);
        
        $this->assertEquals([0 => [ 'id' => 1, 'filename' => 'config.also_invalid.json', 
                            'extension' => 'JSON', 'status' => 'Invalid'], 
                            1 => [ 'id' => 2, 'filename' => 'config.invalid.json', 
                            'extension' => 'JSON', 'status' => 'Invalid'], 
                            2 => [ 'id' => 3, 'filename' => 'config.json', 
                            'extension' => 'JSON', 'status' => 'Valid'], 
                            3 => [ 'id' => 4, 'filename' => 'config.local.json', 
                            'extension' => 'JSON', 'status' => 'Valid']], $configurator->show_files_with_given_extension('json'));
        $this->assertEquals([], $configurator->show_files_with_given_extension('gif'));
    }

    public function testValidateAndProcess() {
        $configurator = new Configurator(DIRECTORY);
        
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
         ), $configurator->validate_and_process([0 => '1', 1 => '2']));
         
        $this->assertEquals((object) array(
              'environment' => 'production', 
              'database' => (object) array( 
                  'host' => 'mysql', 
                  'port' => 3306, 
                  'username' => 'divido', 
                  'password' => 'divido', 
                ), 'cache' => (object) array( 
                    'redis' => (object) array( 
                        'host' => 'redis', 
                        'port' => 6379, 
                ), 
            ), 
        ), $configurator->validate_and_process([0 => '2', 1 => '1']));

        $this->assertEquals((object) array( 
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
                    'port' => 6379, 
                ), 
            ), 
            ), $configurator->validate_and_process([0 => '1', 1 => '1']));

        $this->assertNull($configurator->validate_and_process([0 => '0', 1 => '0']));
    }

    public function testGetConfig() {
        $configurator = new Configurator(DIRECTORY);
        $this->assertEquals((object) array(
            'host' => '127.0.0.1',
            'port' => 3306,
            'username' => 'divido',
            'password' => 'divido',
         ), $configurator->get_config('database'));
        $this->assertEquals('development', $configurator->get_config('environment'));
        $this->assertNotEquals('production', $configurator->get_config('environment'));
    }
}
