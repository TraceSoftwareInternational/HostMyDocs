<?php

namespace HostMyDocs\Controllers;

use Chumper\Zipper\Zipper;
use Slim\Container;
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use Slim\Http\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class DeleteProject extends BaseController
{
    /**
     * @var null|string Name of the project to add
     */
    private $name = null;

    /**
     * @var null|string SemVer representation of the version of the project to add
     */
    private $version = null;

    /**
     * @var null|string Programming language of the project to add
     */
    private $language = null;

    /**
     * @var null|Filesystem Symfony Filesystem wrapper
     */
    private $filesystem = null;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->filesystem = new Filesystem();
    }

    /**
     * Main method of the controller
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        // increasing execution time
        ini_set('max_execution_time', 3600);

        $requestParams = $request->getParsedBody();
        var_dump($requestParams);

        if (count($requestParams) === 0) {
            $this->errorMessage = 'no parameters found';
            $response = $response->write($this->errorMessage);
            return $response->withStatus(400);
        }

        error_log('Processing a new request');

        if (array_key_exists('name', $requestParams)) {
            $this->name = $requestParams['name'];
        }

        if (array_key_exists('version', $requestParams)) {
            $this->version = $requestParams['version'];
        }

        if (array_key_exists('language', $requestParams)) {
            $this->language = $requestParams['language'];
        }

        error_log('Checking provided parameters');

        if ($this->checkParams() === false) {
            if ($this->errorMessage !== null) {
                $response = $response->write($this->errorMessage);
            }
            return $response->withStatus(400);
        }

        error_log('Parameters OK');

        error_log("Name of the project : $this->name");
        error_log("Version of the project : $this->version");
        error_log("Language of the project : $this->language");

        error_log('Deleting folder + backup');

        if ($this->delete() === false) {
            if ($this->errorMessage !== null) {
                $response = $response->write($this->errorMessage);
            }
            return $response->withStatus(400);
        }

        error_log('Delteting done.');

        error_log('Project deleted successfully');

        return $response->withStatus(200);
    }

    /**
     * Verifying that all parameters are valid
     *
     * @return bool the error code the client will receive or true in case of success
     */
    private function checkParams() : bool
    {
        if ($this->name === null) {
            $this->errorMessage = 'name is empty';
            return false;
        }

        if (strpos($this->name, '/') !== false) {
            $this->errorMessage = 'name cannot contains slashes';
            return false;
        }

        if (strlen($this->name) === 0) {
            $this->errorMessage = 'name cannot be empty';
            return false;
        }

        if ($this->version === null) {
            $this->errorMessage = 'version is empty';
            return false;
        }

        if (strpos($this->version, '/') !== false) {
            $this->errorMessage = 'version cannot contains slashes';
            return false;
        }

        if ($this->language === null) {
            $this->errorMessage = 'language is empty';
            return false;
        }

        if (strpos($this->language, '/') !== false) {
            $this->errorMessage = 'language cannot contains slashes';
            return false;
        }

        if (strlen($this->language) !== 0 && strlen($this->version) === 0) {
            $this->errorMessage = 'language must be empty when version is empty';
            return false;
        }

        return true;
    }

    /**
     * Delete doc for project and corresponding backup
     *
     * @return bool
     */
    private function delete() : bool
    {
        $fileName = [
            $this->name,
            $this->version,
            $this->language
        ];

        $archiveFolder =  $this->container->get('archiveRoot');

        $archiveDestination = [
            $archiveFolder,
            implode('-', $fileName).'.zip'
        ];

        $archiveDestinationPath = implode(DIRECTORY_SEPARATOR, $archiveDestination);

        if (file_exists($archiveDestinationPath) === true) {
            try {
                unlink($archiveDestinationPath);
            } catch (\Exception $e) {
                $this->errorMessage = 'unlinking backup failed.';
                return false;
            }
        } else {
            error_log('No backup found ' . $archiveDestinationPath);
        }

        $storageFolder =  $this->container->get('storageRoot');

        $storageDestination = [
            $storageFolder,
            implode(DIRECTORY_SEPARATOR, $fileName)
        ];

        $storageDestinationPath = implode(DIRECTORY_SEPARATOR, $storageDestination);

        if (file_exists($storageDestinationPath) === true) {
            try {
                if($this->deleteDirectory($storageDestinationPath) === false) {
                    $this->errorMessage = 'deleting project failed.';
                    return false;
                }
                if($this->deleteEmptyDirectories($storageFolder) === false) {
                    $this->errorMessage = 'deleting empty folders failed. /listProject will fail until you delete them';
                    return false;
                }
            } catch (\Exception $e) {
                $this->errorMessage = 'deleting project failed.';
                return false;
            }
        } else {
            $this->errorMessage = 'project does not exists.';
            return false;
        }

        return true;
    }

    /**
     * delete the directory in argument $dir
     *
     * @param  [string] $dir path to dir to delete
     *
     * @return bool
     */
    private function deleteDirectory($dir) : bool {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        $dirContent = array_diff(scandir($dir), array('..', '.'));
        foreach ($dirContent as $item) {

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

    /**
     * delete the directory in argument $dir
     *
     * @param  [string] $dir path to dir to delete
     *
     * @return bool
     */
    private function deleteEmptyDirectories($dir) : bool {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return true;
        }

        $dirContentBefore = array_diff(scandir($dir), array('..', '.'));
        foreach ($dirContentBefore as $item) {

            if (!$this->deleteEmptyDirectories($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }
        $dirContentAfter = array_diff(scandir($dir), array('..', '.'));
        if (count($dirContentAfter) === 0) {
            return rmdir($dir);
        }

        return true;
    }
}
