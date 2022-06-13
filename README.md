# Coding Challenge

This challenge code is written using PHP 7. It was run on XAMPP on local server. Main features of this challenge code are

1. It parses the list of files in 'features' directory. This directory setting can be changed in constants.php file at the root folder. Not in scope of this challenge but code can support provision for user selected directory from system.

2. It supports different extensions. e.g. 'txt' extension can be tested on fixtures_txt directory.

3. It provides interactive interface for parsing the files.

4. Copy the code over to your local server which can run PHP. This code comes with composer.json file. Please run 'composer update' in root folder first. Once done and server is up, you can access the app in your browser. Dockerizing the project was restricted on my machine so I could not do that part.

5. Each part of the code is documented to understand the code.

6. Unit tests are added. You just need to enter 'phpunit' in command prompt in root folder.

7. It follows PSR-4 for autoload. Overall code follows PSR-12 coding standard.


## Usage
1. After you load the app in your web browser, you will see option to specify extension.

3. Once you enter extension (e.g. json) on first screen and Submit. It gives you a list of files in the directory (fixtures) with given extension.

4. Before showing the list of files, each file is tested in the background for valid json and status is shown as 'Valid' or 'Invalid'. Invalid files don't have option to be processed and those files are not carried forward for any operation as asked in challenge.

5. Valid files have option to specify in which order you want to process them. 0 means you want to skip them.
Higher the order, higher the precedence in settings in merged final configuration.

6. For example if you provide order 2 to config.json and 1 to config.local.json, config.json values will overwrite any values of config.local.json in the final configuration.

7. Code merges the values from multiple files, meaning a setting present in only one file still show up in final config. Once the merging is done, you get to see final configuration on the next page on left side. 

8. On this page, you can provide the value you want to read from configuration (e.g. 'database.host').

9. Once this value is received, the code iterates through the conguration obtained above and shows you the desired value.

10. This value is shown on right side. You can again specify any other value to test.

11. Start Over link at bottom lets you go to the first page of the app.