<?php

namespace HostMyDocs\Controllers;

use HostMyDocs\Models\Language;
use HostMyDocs\Models\Project;
use HostMyDocs\Models\Version;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ListProjects extends BaseController
{
    /**
     * @var array|Project[] All project listed on the server
     */
    private $projects = [];

    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Response $response)
    {
        try {
            $this->listProjects();
        } catch (\Exception $e) {
        }

        return $response->withJson($this->projects);
    }

    /**
     * Retrieving all information to list all languages from all versions from all projects stored
     *
     * @return bool
     */
    private function listProjects()
    {
        $startPoint = $this->container->get('storageRoot');

        $projectLister  = new Finder();
        $projectLister->depth('== 0')->directories()->in($startPoint);

        $projectStructure = [];

        foreach ($projectLister as $projectFolder) {
            $project = new Project($projectFolder->getFilename());

            $projectStructure[] = $projectFolder->getFilename();

            $this->listVersions($projectFolder, $project, $projectStructure);

            $this->projects[] = $project;

            $projectStructure = [];
        }

        return true;
    }

    /**
     * @param SplFileInfo $projectFolder
     * @param Project $currentProject
     * @param array $projectStructure
     */
    private function listVersions(SplFileInfo $projectFolder, Project $currentProject, array $projectStructure)
    {
        $versionLister  = new Finder();
        $versionLister->depth('== 0')->directories();

        $versionStructure = $projectStructure;

        /** @var SplFileInfo $versionFolder */
        foreach ($versionLister->in($projectFolder->getRealPath()) as $versionFolder) {
            $version = new Version($versionFolder->getFilename());

            $versionStructure[] = $versionFolder->getFilename();

            $this->listLanguages($versionFolder, $version, $versionStructure);

            $currentProject->addVersion($version);

            $versionStructure = $projectStructure;
        }
    }

    /**
     * @param SplFileInfo $versionFolder
     * @param Version $currentVersion
     * @param array $versionStructure
     */
    private function listLanguages(SplFileInfo $versionFolder, Version $currentVersion, array $versionStructure)
    {
        $documentRoot = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->container->get('storageRoot'));

        $languageLister = new Finder();
        $languageLister->depth('== 0')->directories();

        $languageStructure = $versionStructure;

        /** @var SplFileInfo $languageFolder */
        foreach ($languageLister->in($versionFolder->getRealPath()) as $languageFolder) {
            $languageStructure[] = $languageFolder->getFilename();

            $indexPath = [
                $documentRoot,
                implode('/', $languageStructure),
                'index.html'
            ];

            $archiveRoot = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->container->get('archiveRoot'));

            $archivePath = $archiveRoot
                . DIRECTORY_SEPARATOR
                . implode('-', $languageStructure)
                . '.zip';

            $language = new Language(
                $languageFolder->getFilename(),
                implode('/', $indexPath),
                $archivePath
            );

            $currentVersion->addLanguage($language);

            $languageStructure = $versionStructure;
        }
    }
}
