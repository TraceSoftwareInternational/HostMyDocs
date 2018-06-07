<?php
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use HostMyDocs\Controllers\ProjectController;
use HostMyDocs\Models\Language;
use HostMyDocs\Models\Project;
use HostMyDocs\Models\Version;
use Monolog\Logger;

if (!function_exists('createProjectFromParams')) {
    function createProjectFromParams(Request $request, Logger $logger, bool $allowEmpty = false): array
    {
        $requestParams = $request->getParsedBody();

        if (count($requestParams) === 0) {
            return ['errorMessage' => 'No parameters found'];
        }

        $logger->info('Processing a new request');

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

        $logger->info('Checking provided parameters');

        $project = new Project($logger);
        $projectVersion = new Version($logger);
        $projectLanguage = new Language($logger);

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
        $response = $response->write('An unexpected error append');
        return $response->withStatus(400);
    }

    $cacheProvider = $this->get('cache');
    return $cacheProvider->withEtag($response->withJson($projects), md5(json_encode($projects)));
});

$slim->post('/addProject', function (Request $request, Response $response): Response {
    $logger = $this->get('logger');
    $params = createProjectFromParams($request, $logger);
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

    $logger->info('Parameters OK');

    $logger->info("Name of the project : " . $project->getName());
    $logger->info("Version of the project : " . $projectVersion->getNumber());
    $logger->info("Language of the project : " . $projectLanguage->getName());

    $logger->info('Extracting the archive');

    if ($this->get('projectController')->extract($project) === false) {
        $response = $response->write('Failed to extract the archive');
        return $response->withStatus(400);
    }

    $logger->info('Extracting OK');

    $logger->info('Backuping uploaded file');

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

    $logger->info('Trying to move upload file to ' . $destinationPath);

    try {
        $archive->moveTo($destinationPath);
    } catch (\Exception $e) {
        $logger->warning('moveTo method failed.');
        $logger->info('Trying with rename()');
        if (rename($projectLanguage->getArchiveFile()->file, $destinationPath) === false) {
            $logger->critical('Failed twice to move uploaded file to backup folder');
            $response = $response->write('Failed twice to move uploaded file to backup folder');
            return $response->withStatus(400);
        }
    }

    $logger->info('Backup done.');

    $logger->info('Project added successfully');

    return $response->withStatus(200);
});

$slim->delete('/deleteProject', function (Request $request, Response $response): Response {
    $logger = $this->get('logger');
    $params = createProjectFromParams($request, $logger, true);

    if (isset($params['errorMessage'])) {
        $response = $response->write($params['errorMessage']);
        return $response->withStatus(400);
    }

    $project = $params['project'];

    $logger->info('Parameters OK');

    $logger->info("Name of the project : $name");
    $logger->info("Version of the project : $version");
    $logger->info("Language of the project : $language");

    $logger->info('Deleting folder + backup');

    if ($this->get('projectController')->deleteProject($project) === false) {
        $response = $response->write('Project deletion failed');
        return $response->withStatus(400);
    }

    $logger->info('Deleting done.');

    $logger->info('Removing resulting empty folders');
    $this->get('projectController')->removeEmptySubFolders($this->get('storageRoot'));
    $logger->info('Empty folders removed');

    $logger->info('Project deleted successfully');

    return $response->withStatus(200);
});
