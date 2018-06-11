<?php

namespace HostMyDocs\Tests;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Slim\Http\UploadedFile;
use ZipArchive;

/**
 * from https://github.com/slimphp/Slim-Skeleton
 */

class BaseTestCase extends TestCase
{
    /**
     * @var array all paths to the temporary files that can be created during tests
     */
    private static $tmpFiles = [];

    public static function tearDownAfterClass()
    {
        foreach (self::$tmpFiles as $filename) {
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    /**
     * Process the Slim application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     * @param null|UploadedFile[] $files files uploaded in the request
     * @param null|string $credential HTTP Authentication credential (by example : "user:password")
     * @return Response
     */
    public function runApp($requestMethod, $requestUri, $requestData = null, $files = null, $credential = null)
    {
        putenv('SHOULD_SECURE=false');

        $baseEnvironment = [
            'REQUEST_METHOD' => $requestMethod,
            'REQUEST_URI' => $requestUri
        ];

        if ($credential !== null) {
            $baseEnvironment['HTTP_AUTHORIZATION'] =  'Basic '.base64_encode($credential);
        }

        // Create a mock environment for testing with
        $environment = Environment::mock($baseEnvironment);
        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);
        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }
        if (isset($files)) {
            $request = $request->withUploadedFiles($files);
        }
        // Set up a response object
        $response = new Response();
        // Use the application settings
        $settings = require __DIR__ . '/../src/settings.php';
        // Instantiate the application
        $slim = new App($settings);
        // add the dependencies in the container
        require __DIR__ . '/../src/dependencies.php';
        // Registering application's middlewares
        require __DIR__ . '/../src/middleware.php';
        // Register routes
        require __DIR__ . '/../src/routes.php';
        // Process the application
        $response = $slim->process($request, $response);
        // Return the response
        return $response;
    }

    public function createFile() : UploadedFile
    {
        $filename = sys_get_temp_dir().'/php'.str_replace(' ', '', microtime());
        $fh = fopen($filename, "w");
        fwrite($fh, '<html><body>');
        fwrite($fh, "<h1>I AM GROOT</h1>");
        fwrite($fh, '</body></html>');
        fclose($fh);
        self::$tmpFiles[] = $filename;
        return new UploadedFile($filename, 'test.html', 'text/html', filesize($filename));
    }

    public function createZipFile() : ?UploadedFile
    {
        $zip = new ZipArchive();
        $zipName = sys_get_temp_dir().'/php'.str_replace(' ', '', microtime());
        self::$tmpFiles[] = $zipName;

        $fileName = sys_get_temp_dir().'/php'.str_replace(' ', '', microtime());
        $fh = fopen($fileName, "w");
        fwrite($fh, '<html><body>');
        fwrite($fh, "<h1>I AM GROOT</h1>");
        fwrite($fh, '</body></html>');
        fclose($fh);
        self::$tmpFiles[] = $fileName;

        if ($zip->open($zipName, ZipArchive::CREATE) !== true) {
            return null;
        }

        $zip->addFile($fileName, 'folder/index.html');

        if ($zip->close() === false) {
            return null;
        }

        return new UploadedFile($zipName, 'mini-groot.zip', 'application/zip', filesize($zipName));
    }

    public function createInvalidZipFile() : ?UploadedFile
    {
        $zip = new ZipArchive();
        $zipName = sys_get_temp_dir().'/php'.str_replace(' ', '', microtime());
        self::$tmpFiles[] = $zipName;

        $fileName = sys_get_temp_dir().'/php'.str_replace(' ', '', microtime());
        $fh = fopen($fileName, "w");
        fwrite($fh, '<html><body>');
        fwrite($fh, "<h1>I AM GROOT</h1>");
        fwrite($fh, '</body></html>');
        fclose($fh);
        self::$tmpFiles[] = $fileName;

        if ($zip->open($zipName, ZipArchive::CREATE) !== true) {
            return null;
        }

        $zip->addFile($fileName, 'folder/index.html');
        $zip->addFile($fileName, 'folder2/index.html');
        $zip->addFile($fileName, $fileName);

        if ($zip->close() === false) {
            return null;
        }

        return new UploadedFile($zipName, 'mini-groot.zip', 'application/zip', filesize($zipName));
    }
}
