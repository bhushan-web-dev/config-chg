<?php

/**
* This page acts as View part where all the UI is shown and POST request for each stage of parsing are handled.
*/
namespace Divido\Chg;

require_once './Configurator.php';
require_once './constants.php';

/**
* Set error reporting to avoid seeing notices for Prod env.
*/
error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start();
/**
* Instantiate the main Configurator class to perform fetch and processing operations.
* DIRECTORY constant is set in constants file and can be changed from there.
* If needed this behavior can be changed to get user selected directory.
*/
$configurator = new Configurator(DIRECTORY);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <title>Divido Config Challenge</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name='description' content='Divido Config Challenge'>
    <link rel='stylesheet' href='style.css'>
</head>
<body>
<header>
    <h1>Divido Config Challenge</h1>
</header>
<form action='' method='POST'>
    <?php
    /**
    * Show input to get file extension.
    */
    if (!$_POST) {// No Form submission has happened and it's the first page.
        ?>
        <div>Enter extension for config files: <input type='text' name='extension' required/><input type='submit'
                                                                                                    name='submit'/>
        </div>
        <?php
    } else if ($_POST && $_POST['submit'] && $_POST['extension']) { //User has provided the file type (extension)
        
        /**
        * Call configurator show files method to get files of specific type.
        */
        $fileswithtype = $configurator->show_files_with_given_extension($_POST['extension']);

        /**
        * If count of files is more than 0 show the list.
        */
        if (count($fileswithtype) > 0) {
            /**
            * This count is needed the create the merge order dropdown 
            * as it dynamically shows only the possible ordering based on number of valid files.
            */
            $count = count($_SESSION['validfiles']);
            ?>
            <div>
                <div class='row'>
                    <div class='col wid-5'><strong>Id</strong></div>
                    <div class='col wid-50'><strong>Filename</strong></div>
                    <div class='col wid-10'><strong>Status</strong></div>
                    <div class='col wid-10'><strong>Merge order</strong></div>
                </div>
                <?php
                foreach ($fileswithtype as $file) { 
                    /**
                    * Iterate thorugh all the files received above and show valid and invalid files both.
                    */
                ?>
                    <div class='row'>
                        <div class='col wid-5'><?= $file['id']; ?></div>
                        <div class='col wid-50'><?= $file['filename']; ?>'</div>
                        <div class='col wid-10'><?= $file['status']; ?></div>
                        <div class='col wid-10'>
                            <?php if($file['status'] === 'Valid') {
                            ?>
                            <select name='processorder[]' required>
                                <option value=''>Select</option>
                                <?php for ($c = 0; $c <= $count; $c++) { ?>
                                    <option value='<?= $c; ?>'><?= $c; ?></option>
                                <?php } ?>
                            </select>
                            <?php
                            } else {
                                echo 'Can\'t be merged';
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <br>
            <div><i>Note: <strong>Higher merge order files would overwrite configs from lower ones. Selecting 0 in Merge Order
                    means you don't want to include the file in processing.</strong></i></div>
            <br>
            <input type='submit' name='submit' value='Process'/>
            <?php
        }
    } else if ($_POST && $_POST['submit'] && $_POST['processorder']) {
        /**
        * User selected the merge order preference in previous stage. So call the validate and process method now.
        * Files to be processed are in session.
        */
        if (is_object($configurator->validate_and_process($_POST['processorder']))) {
            ?>
            <h2>Parsing complete!</h2>
            <h3>You can now read config values using dotted notation.</h3>
            <div class='wid-50'>
                <input type='text' name='notation' placeholder='e.g. "database.host"' required/><input type='submit'
                                                                                                       name='submit'/>
                <br>
                <h3>Current configuration::</h3>
                <textarea rows='10' cols='70' disabled='true'
                          readonly><?= var_export($_SESSION['globalconfig']); ?></textarea>
            </div>
            <?php
        } else {
            echo 'Sorry, looks like no files were processed or there was an error. Please try again by refreshing page.';
        }
    } else if ($_POST && $_POST['submit'] && $_POST['notation']) {
        /**
        * User performed the parsing in previous stage so they can now check for specific config values by providing dot notation.
        */
        ?>
        <h3>You can now read config values using dotted notation.</h3>
        <input type='text' name='notation' placeholder='e.g. "database.host"' required/><input type='submit'
                                                                                               name='submit'/>
        <br>
        <div class='wid-50 fl-left'>
            <h3>Current configuration::</h3>
            <textarea rows='10' cols='70' disabled='true'
                      readonly><?= var_export($_SESSION['globalconfig']); ?></textarea>
        </div>
        <div class='wid-50 il-block'>
            <h3>Output (<?= $_POST['notation']; ?>):</h3>
            <textarea rows='10' cols='70' disabled='true'
                      readonly><?= var_export($configurator->get_config($_POST['notation'])); ?></textarea>
        </div>

        <?php
    }
    ?>
</form>
<br>
<div><a href='./'>Start Over</a>
</div>
</body>
</html>
