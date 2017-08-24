<?hh
define('ROOT_DIR', getcwd().'/'); // Root folder for all files to access
define('OTCORE', true); // Will be used to prevent directly loading files

session_name('otSess');
session_start(); //gloval session_start();

require 'config.php';
require 'core/loader.php';
require 'vendor/autoload.php';

function main() : void {
    $loader = new core\loader();
    $loader->run();
}

main();
