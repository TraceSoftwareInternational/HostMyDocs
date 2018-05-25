<?php
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

$slim->get('/', function (Request $request, Response $response) {
    return $response->write('What did you expect ?');
});

// declare function used to dynamicaly load routes
// they are first because since they are conditionnaly declared they have to be first to be recognized
// they are conditionnaly declared because PHPUnit require the file multiple time
//      and was going "Fatal error function is already defined"

// get every classes declared in the file
if(!function_exists("file_get_php_classes")) {
    function file_get_php_classes(string $filepath) {
        $php_code = file_get_contents($filepath);
        $classes = get_php_classes($php_code);
        return $classes;
    }
}

// get every classes declared in a valid php string
if(!function_exists("get_php_classes")) {
    function get_php_classes(string $php_code) {
        $classes = array();
        $tokens = token_get_all($php_code);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if (   $tokens[$i - 2][0] == T_CLASS
            && $tokens[$i - 1][0] == T_WHITESPACE
            && $tokens[$i][0] == T_STRING) {

                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }
        return $classes;
    }
}

// include BaseController to prevent dynamic import from failing because they are decalred before it
require_once __DIR__ . "/controllers/BaseController.php";

$classesNamespace = "HostMyDocs\\Controllers\\";

// name of the function used to get the name of the http word
$routeFunction = "useRoute";

// Includes all php files (recursive) in Routes folder
$dir = new RecursiveDirectoryIterator(__DIR__ . '/controllers');
$iter = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iter, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH); // an Iterator, not an array

foreach ($files as $file) {
    // this oe was already included
    if ($file[0] === __DIR__ . "/controllers/BaseController.php") {
        continue;
    }
    require_once($file[0]);

    // get the classes declared in the file
    foreach (file_get_php_classes($file[0]) as $className) {
        // the fully qualified name is neaded
        $fullClassName = $classesNamespace . $className;

        // if they have the function used to tell which route they take
        if (method_exists($fullClassName, $routeFunction)) {
            $route = $fullClassName::$routeFunction();
            $func = $route[0];
            $path = "/" . $route[1];
            $slim->$func($path, $fullClassName);
        }
    }
}
?>
