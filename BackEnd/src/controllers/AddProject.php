<?php

namespace HostMyDocs\Controllers;

use Chumper\Zipper\Zipper;
use Slim\Container;
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use Slim\Http\UploadedFile;
use vierbergenlars\SemVer\version;

class AddProject extends BaseController
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
     * @var null|UploadedFile[] raw upload files from the request
     */
    private $files = null;

    /**
     * @var null|UploadedFile Zip file containing the project's documentation
     */
    private $archive = null;

    public function __construct(Container $container)
    {
        parent::__construct($container);
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
        $this->name     = $request->getParam('name');
        $this->version  = $request->getParam('version');
        $this->language = $request->getParam('language');
        $this->files    = $request->getUploadedFiles();

        if ($this->checkParams() === false) {
            if ($this->errorMessage !== null) {
                $response = $response->write($this->errorMessage);
            }
            return $response->withStatus(400);
        }

        if ($this->extract() === false) {
            if ($this->errorMessage !== null) {
                $response = $response->write($this->errorMessage);
            }
            return $response->withStatus(400);
        }

        if ($this->backup() === false) {
            if ($this->errorMessage !== null) {
                $response = $response->write($this->errorMessage);
            }
            return $response->withStatus(400);
        }

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

        try {
            new version($this->version);
        } catch (\Exception $e) {
            $this->errorMessage = 'version is invalid';
            return false;
        }

        if ($this->language === null) {
            $this->errorMessage = 'language is empty';
            return false;
        }

        if ((array_key_exists('archive', $this->files))) {
            $this->archive = $this->files['archive'];
        } else {
            $this->errorMessage = 'no file provided';
            return false;
        }

        if ($this->archive->getClientMediaType() !== 'application/zip') {
            $this->errorMessage = 'archive is not a zip file';
            return false;
        }

        return true;
    }

    /**
     * Extracting the provided archive into the filesystem given the following schema :
     *
     * STORAGE_ROOT  (environment variable)
     * |
     * |-- project1
     * |   |-- 1.0
     * |   |   |-- C
     * |   |   |-- C+
     * |   |   |-- C++
     * |   |-- 2.0
     * |       |-- Go
     * |-- project2
     * |   |-- 0.0.1-alpha
     * |   |   |-- CoffeeScript
     * |   |-- 1.0.0
     * |   |   |-- TypeScript
     *
     * @return bool
     */
    private function extract()
    {
        $zipper = new Zipper();
        $zipFile = $zipper->make($this->archive->file);

        $filesToExtract = $zipFile->listFiles();

        $splittedPath = explode('/', $filesToExtract[0]);
        $zipRoot = array_shift($splittedPath);

        $destination = [
            $this->container->get('storageRoot'),
            $this->name,
            $this->version,
            $this->language
        ];

        $destinationPath = implode('/', $destination);

        if ($destinationPath !== filter_var($destinationPath, FILTER_SANITIZE_URL)) {
            $this->errorMessage = 'request contains invalid characters';
            return false;
        }

        if (file_exists($destinationPath)) {
            $this->errorMessage =  'this project have been already uploaded';
            return false;
        }

        if (mkdir($destinationPath, 0755, true) === false) {
            $this->errorMessage = 'failed to create folder';
            return false;
        }

        $zipFile->folder($zipRoot)->extractTo($destinationPath);

        return true;
    }

    /**
     * Move client file to a backup folder
     *
     * @return bool
     */
    private function backup() : bool
    {
        $fileName = [
            $this->name,
            $this->version,
            $this->language
        ];

        $destinationFolder =  $this->container->get('archiveRoot');

        if (file_exists($destinationFolder) === false) {
            if (mkdir($destinationFolder, 0755, true) === false) {
                $this->errorMessage = 'failed to create backup folder';
                return false;
            }
        }

        $destination = [
            $destinationFolder,
            implode('-', $fileName).'.zip'
        ];

        $destinationPath = implode('/', $destination);

        try {
            $this->archive->moveTo($destinationPath);
        } catch (\Exception $e) {
            $this->errorMessage = 'failed to move uploaded file to backup folder';
            return false;
        }

        return true;
    }
}
