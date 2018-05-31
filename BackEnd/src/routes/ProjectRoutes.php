<?php
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use HostMyDocs\Controllers\ProjectController;
use HostMyDocs\Models\Language;
use HostMyDocs\Models\Project;
use HostMyDocs\Models\Version;

$slim->get('/listProjects', function (Request $request, Response $response) {
    $projects = [];
    try {
        $projects = $this->get('projectController')->listProjects($this->get('storageRoot'), $this->get('archiveRoot'));
    } catch (\Exception $e) {
    }

    $cacheProvider = $this->get('cache');
    return $cacheProvider->withEtag($response->withJson($projects), md5(json_encode($projects)));
});

$slim->post('/addProject', function (Request $request, Response $response) {
    // increasing execution time
    ini_set('max_execution_time', 3600);

    $name = null;
    $version = null;
    $language = null;

    $requestParams = $request->getParsedBody();

    if (count($requestParams) === 0) {
        $response = $response->write('No parameters found');
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

    $project = new Project(null);
    $projectVersion = new Version(null);
    $projectLanguage = new Language(null, null, null);

    $project = $project->setName($name);
    $projectVersion = $projectVersion->setNumber($version);
    $projectLanguage = $projectLanguage->setName($language);

    if ($project === null || $projectVersion === null || $projectLanguage === null) {
        $response = $response->write('Bad parameters');
        return $response->withStatus(400);
    }

    if ((array_key_exists('archive', $files))) {
        $archive = $files['archive'];
    } else {
        $response = $response->write('No file provided');
        return $response->withStatus(400);
    }

    if ($projectLanguage->setArchiveFile($archive) === null) {
        $response = $response->write('Invalid file parameter');
        return $response->withStatus(400);
    }

    $project->addVersion($projectVersion);
    $projectVersion->addLanguage($projectLanguage);

    error_log('Parameters OK');

    error_log("Name of the project : $name");
    error_log("Version of the project : $version");
    error_log("Language of the project : $language");

    error_log('Extracting the archive');

    if (!$this->get('projectController')->extract($project)) {
        $response = $response->write('Failed to extract the archive');
        return $response->withStatus(400);
    }

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
            $response = $response->write('Failed to create backup folder');
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
            $response = $response->write('Failed twice to move uploaded file to backup folder');
            return $response->withStatus(400);
        }
    }

    error_log('Backup done.');

    error_log('Project added successfully');

    return $response->withStatus(200);
});
//
// $slim->delete('/deleteProject', function (Request $request, Response $response) {
//     $errorMessage = null;
//     $name = null;
//     $version = null;
//     $language = null;
//
//     $requestParams = $request->getParsedBody();
//
//     if (count($requestParams) === 0) {
//         $errorMessage = 'no parameters found';
//         $response = $response->write($errorMessage);
//         return $response->withStatus(400);
//     }
//
//     error_log('Processing a new request');
//
//     if (array_key_exists('name', $requestParams)) {
//         $name = $requestParams['name'];
//     }
//
//     if (array_key_exists('version', $requestParams)) {
//         $version = $requestParams['version'];
//     }
//
//     if (array_key_exists('language', $requestParams)) {
//         $language = $requestParams['language'];
//     }
//
//     error_log('Checking provided parameters');
//
//     // $project->setName($name);
//     // $version->setNumber($version);
//     // $language->setName($language);
//
//     if (strlen($language) !== 0 && strlen($version) === 0) {
//         $errorMessage = 'language must be empty when version is empty';
//         $response = $response->write($errorMessage);
//         return $response->withStatus(400);
//     }
//
//     error_log('Parameters OK');
//
//     error_log("Name of the project : $name");
//     error_log("Version of the project : $version");
//     error_log("Language of the project : $language");
//
//     error_log('Deleting folder + backup');
//
//     $projectDeleteError = $this->get('projectController')->deleteProject($name, $version, $language, $this->get('archiveRoot'), $this->get('storageRoot'));
//     if ($projectDeleteError !== null) {
//         $response = $response->write($projectDeleteError);
//         return $response->withStatus(400);
//     }
//
//     error_log('Deleting done.');
//
//     error_log('Project deleted successfully');
//
//     return $response->withStatus(200);
// });
