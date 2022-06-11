<?php
namespace Divido\Chg;
require_once './Configurator.php';

$configurator = new Configurator('./fixtures');
error_reporting(E_ERROR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Divido Config Challenge</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Divido Config Challenge">
</head>
<body>
    <header>
        <h1>Divido Config Challenge</h1>
    </header>
    <form action="" method="POST">
    <?php
    if (!$_POST){
    ?>
        Enter extension for config files: <input type="text" name="extension" required/><input type="submit" name="submit"/><br>
    <?php
    } else if ($_POST && $_POST['submit'] && $_POST['extension']){
        $fileswithtype = $configurator->show_files($_POST['extension']);
        $count = count(array_filter($fileswithtype, function($file) {
                if ($file['status'] === 'valid'){
                    return $file;
                }
            }));
    ?>
    <table border="0">
    <tr>
        <th>Id</th>
        <th>Status</th>
        <th>Filename</th>
        <th>Merge order</th>
    </tr>
    <?php
        foreach ($fileswithtype as $file) {
            if($file['status'] === 'valid') {
    ?>
    <tr>
        <td><?= $file['id']; ?></td>
        <td><?= $file['status']; ?></td>
        <td><input type="text" name="files[]" readonly width="200" value="<?= $file['filename']; ?>"/></td>
        <td>
        <select name="processorder[]" id="cars">
            <option value="0">N/A</option>
            <?php for ($c=1; $c <= $count; $c++) { ?>
            <option value="<?= $c;?>"><?= $c;?></option>
            <?php } ?>
        </select>
        </td>
    </tr>
    <?php
            } else {
    ?>
    <tr>
        <td><?= $file['id']; ?></td>
        <td><?= $file['status']; ?></td>
        <td><input type="text" readonly width="200" value="<?= $file['filename']; ?>"/></td>
        <td>N/A</td>
    </tr>
    <?php
            }
        }
    ?>
    </table>
    <input type="submit" name="submit"/>
    <?php
    } else if ($_POST && $_POST['submit'] && $_POST['processorder']) {
        if($globalconfig = $configurator->validate_and_process($_POST['files'], $_POST['processorder'])) {
            echo 'Parsing complete. You can now provide the dotted notation e.g. "database.host".<br>';
    ?>
    <input type="text" name="notation" required/><input type="submit" name="submit"/><br><br>
    Current configuration:<br>
    <textarea rows="10" cols="100" disabled="true" readonly><?= print_r($globalconfig); ?></textarea>
    <?php
        } else {
            echo 'Sorry, looks like there was an error. Please try again by refreshing page.';
        }
    } else if ($_POST && $_POST['submit'] && $_POST['notation']) {
    ?>
    <h3>Output</h3>
    <textarea rows="10" cols="100" disabled="true" readonly><?= print_r($configurator->get($_POST['notation'])); ?></textarea>
    <?php
    }
    ?>
    </form>
</body>
</html>
