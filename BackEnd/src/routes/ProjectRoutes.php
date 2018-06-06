<?php
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use HostMyDocs\Controllers\ProjectController;
use HostMyDocs\Models\Language;
use HostMyDocs\Models\Project;
use HostMyDocs\Models\Version;

if (!function_exists('createProjectFromParams')) {
    function createProjectFromParams(Request $request, $allowEmpty = false): array
    {
        $requestParams = $request->getParsedBody();

        if (count($requestParams) === 0) {
            return ['errorMessage' => 'No parameters found'];
        }

        error_log('Processing a new request');

        $name = null;
        if (array_key_exists('name', $requestParams)) {
            $name = $requestParams['name'];
        }

        $version = null;
        if (array_key_exists('version', $requestParams)) {
            $version = $requestParams['version'];
        }

        $language = null;
        if (array_key_exists('language', $requestParams)) {
            $language = $requestParams['language'];
        }

        error_log('Checking provided parameters');

        $project = new Project();
        $projectVersion = new Version();
        $projectLanguage = new Language();

        if ($project->setName($name) === null) {
            return ['errorMessage' => 'Given name is not valid'];
        }
        if ($projectVersion->setNumber($version, $allowEmpty) === null) {
            return ['errorMessage' => 'Given version is not valid'];
        }
        if ($projectLanguage->setName($language, $allowEmpty) === null) {
            return ['errorMessage' => 'Given language is not valid'];
        }
        if ($allowEmpty) {
            if (strlen($language) !== 0 && strlen($version) === 0) {
                return ['errorMessage' => 'language must be empty when version is empty'];
            }
        }

        $projectVersion->addLanguage($projectLanguage);
        $project->addVersion($projectVersion);

        return [
            'project' => $project,
            'projectVersion' => $projectVersion,
            'projectLanguage' => $projectLanguage
        ];
    };
}

$slim->get('/listProjects', function (Request $request, Response $response): Response {
    $projects = [];
    try {
        $projects = $this->get('projectController')->listProjects();
    } catch (\Exception $e) {
        error_log($e);
        $response = $response->write('An unexpected error append');
        return $response->withStatus(400);
    }

    $cacheProvider = $this->get('cache');
    return $cacheProvider->withEtag($response->withJson($projects), md5(json_encode($projects)));
});

$slim->post('/addProject', function (Request $request, Response $response): Response {
    $params = createProjectFromParams($request);
    if (isset($params['errorMessage'])) {
        $response = $response->write($params['errorMessage']);
        return $response->withStatus(400);
    }

    list('project' => $project, 'projectVersion' => $projectVersion, 'projectLanguage' => $projectLanguage) = $params;

    $files = $request->getUploadedFiles();
    $archive = null;
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


    error_log('Parameters OK');

    error_log("Name of the project : " . $project->getName());
    error_log("Version of the project : " . $projectVersion->getNumber());
    error_log("Language of the project : " . $projectLanguage->getName());

    error_log('Extracting the archive');

    if ($this->get('projectController')->extract($project) === false) {
        $response = $response->write('Failed to extract the archive');
        return $response->withStatus(400);
    }

    error_log('Extracting OK');

    error_log('Backuping uploaded file');

    $destinationFolder =  $this->get('archiveRoot');
    if (file_exists($destinationFolder) === false) {
        if (mkdir($destinationFolder, 0755, true) === false) {
            $response = $response->write('Failed to create backup folder');
            return $response->withStatus(400);
        }
    }

    $destinationPath =
    $destinationFolder
    . DIRECTORY_SEPARATOR
    . implode('-', [
        $project->getName(),
        $projectVersion->getNumber(),
        $projectLanguage->getName()
    ])
    . '.zip';

    error_log('Trying to move upload file to ' . $destinationPath);

    try {
        $archive->moveTo($destinationPath);
    } catch (\Exception $e) {
        error_log('moveTo method failed.');
        error_log('Trying with rename()');
        if (rename($projectLanguage->getArchiveFile()->file, $destinationPath) === false) {
            $response = $response->write('Failed twice to move uploaded file to backup folder');
            return $response->withStatus(400);
        }
    }

    error_log('Backup done.');

    error_log('Project added successfully');

    return $response->withStatus(200);
});

$slim->delete('/deleteProject', function (Request $request, Response $response): Response {
    $params = createProjectFromParams($request, true);

    if (isset($params['errorMessage'])) {
        $response = $response->write($params['errorMessage']);
        return $response->withStatus(400);
    }

    $project = $params['project'];


    error_log('Parameters OK');

    error_log("Name of the project : $name");
    error_log("Version of the project : $version");
    error_log("Language of the project : $language");

    error_log('Deleting folder + backup');

    if ($this->get('projectController')->deleteProject($project) === false) {
        $response = $response->write('Project deletion failed');
        return $response->withStatus(400);
    }

    error_log('Deleting done.');

    error_log('Removing resulting empty folders');
    $this->get('projectController')->removeEmptySubFolders($this->get('storageRoot'));
    error_log('Empty folders removed');

    error_log('Project deleted successfully');

    return $response->withStatus(200);
});
