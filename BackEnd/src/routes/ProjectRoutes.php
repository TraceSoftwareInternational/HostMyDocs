<?php
use Chumper\Zipper\Zipper;
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use Symfony\Component\Filesystem\Filesystem;
use HostMyDocs\Controllers\ProjectController;

$slim->get('/listProjects', function (Request $request, Response $response)
{
    $projects = [];
    try {
        $projects = ProjectController::listProjects($this->get('storageRoot'), $this->get('archiveRoot'));
    } catch (\Exception $e) {
    }

    $cacheProvider = $this->get('cache');
    return $cacheProvider->withEtag($response->withJson($projects), md5(json_encode($projects)));
});


$slim->post('/addProject', function (Request $request, Response $response)
{
    // increasing execution time
    ini_set('max_execution_time', 3600);

    $errorMessage = null;
    $name = null;
    $version = null;
    $language = null;

    $filesystem = new Filesystem();
    $requestParams = $request->getParsedBody();

    if (count($requestParams) === 0) {
        $errorMessage = 'no parameters found';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }


    error_log('Processing a new request');

    if (array_key_exists('name', $requestParams)) {
        $name = $requestParams['name'];
    }

    if (array_key_exists('version', $requestParams)) {
        $version = $requestParams['version'];
    }

    if (array_key_exists('language', $requestParams)) {
        $language = $requestParams['language'];
    }

    $files = $request->getUploadedFiles();
    $archive = null;


    error_log('Checking provided parameters');

    if ($name === null) {
        $errorMessage = 'name is empty';
    }

    if (strpos($name, '/') !== false) {
        $errorMessage = 'name cannot contains slashes';
    }

    if ($language === null) {
        $errorMessage = 'language is empty';
    }

    if (strpos($language, '/') !== false) {
        $errorMessage = 'language cannot contains slashes';
    }

    if ((array_key_exists('archive', $files))) {
        $archive = $files['archive'];
    } else {
        $errorMessage = 'no file provided';
        // this one needed to prevent next if to throw a null pointer exeption
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    if (pathinfo($archive->getClientFilename(), PATHINFO_EXTENSION) !== 'zip') {
        $errorMessage = 'archive is not a zip file';
    }

    if ($errorMessage !== null) {
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    error_log('Parameters OK');

    error_log("Name of the project : $name");
    error_log("Version of the project : $version");
    error_log("Language of the project : $language");


    error_log('Extracting the archive');

    $zipper = new Zipper();

    if (is_file($archive->file) === false) {
        $errorMessage = 'impossible to open archive file';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    error_log("Opening file : " . $archive->file);

    $zipFile = $zipper->make($archive->file);

    $rootCandidates = array_values(array_filter($zipFile->listFiles(), function ($path) {
        return preg_match('@^[^/]+/index\.html$@', $path);
    }));

    if (count($rootCandidates) > 1) {
        $errorMessage = "More than one index file found";
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    $splittedPath = explode('/', $rootCandidates[0]);
    $zipRoot = array_shift($splittedPath);

    $destination = [
        $this->get('storageRoot'),
        $name,
        $version,
        $language
    ];

    $destinationPath = implode('/', $destination);

    if (filter_var($destinationPath, FILTER_SANITIZE_URL) === false) {
        $errorMessage = 'extract path contains invalid characters';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    if (file_exists($destinationPath)) {
        $filesystem->remove($destinationPath);
    }

    if (mkdir($destinationPath, 0755, true) === false) {
        $errorMessage = 'failed to create folder';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    error_log('Extracting to ' . $destinationPath);

    $zipFile->folder($zipRoot)->extractTo($destinationPath);

    $zipper->close();

    error_log('Extracting OK');


    error_log('Backuping uploaded file');

    $fileName = [
        $name,
        $version,
        $language
    ];

    $destinationFolder =  $this->get('archiveRoot');

    if (file_exists($destinationFolder) === false) {
        if (mkdir($destinationFolder, 0755, true) === false) {
            $errorMessage = 'failed to create backup folder';
            $response = $response->write($errorMessage);
            return $response->withStatus(400);
        }
    }

    $destination = [
        $destinationFolder,
        implode('-', $fileName).'.zip'
    ];

    $destinationPath = implode('/', $destination);

    error_log('Trying to move upload file to ' . $destinationPath);

    try {
        $archive->moveTo($destinationPath);
    } catch (\Exception $e) {
        error_log('moveTo method failed.');
        error_log('Trying with rename()');
        if (rename($archive->file, $destinationPath) === false) {
            $errorMessage = 'failed twice to move uploaded file to backup folder';
            $response = $response->write($errorMessage);
            return $response->withStatus(400);
        }
    }

    error_log('Backup done.');

    error_log('Project added successfully');

    return $response->withStatus(200);
});

$slim->delete('/deleteProject', function (Request $request, Response $response)
{
    $errorMessage = null;
    $name = null;
    $version = null;
    $language = null;

    $filesystem = new Filesystem();
    $requestParams = $request->getParsedBody();

    if (count($requestParams) === 0) {
        $errorMessage = 'no parameters found';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    error_log('Processing a new request');

    if (array_key_exists('name', $requestParams)) {
        $name = $requestParams['name'];
    }

    if (array_key_exists('version', $requestParams)) {
        $version = $requestParams['version'];
    }

    if (array_key_exists('language', $requestParams)) {
        $language = $requestParams['language'];
    }

    error_log('Checking provided parameters');

    if ($name === null) {
        $errorMessage = 'name is empty';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    if (strpos($name, '/') !== false) {
        $errorMessage = 'name cannot contains slashes';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    if (strlen($name) === 0) {
        $errorMessage = 'name cannot be empty';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    if ($version === null) {
        $errorMessage = 'version is empty';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    if (strpos($version, '/') !== false) {
        $errorMessage = 'version cannot contains slashes';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    if ($language === null) {
        $errorMessage = 'language is empty';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    if (strpos($language, '/') !== false) {
        $errorMessage = 'language cannot contains slashes';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    if (strlen($language) !== 0 && strlen($version) === 0) {
        $errorMessage = 'language must be empty when version is empty';
        $response = $response->write($errorMessage);
        return $response->withStatus(400);
    }

    error_log('Parameters OK');

    error_log("Name of the project : $name");
    error_log("Version of the project : $version");
    error_log("Language of the project : $language");

    error_log('Deleting folder + backup');

    $projectDeleteError = ProjectController::deleteProject($name, $version, $language, $this->get('archiveRoot'), $this->get('storageRoot'));
    if ($projectDeleteError !== null) {
        $response = $response->write($projectDeleteError);
        return $response->withStatus(400);
    }

    error_log('Deleting done.');

    error_log('Project deleted successfully');

    return $response->withStatus(200);
});
