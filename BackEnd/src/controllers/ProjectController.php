<?php
namespace HostMyDocs\Controllers;

use Chumper\Zipper\Zipper;
use HostMyDocs\Models\Language;
use HostMyDocs\Models\Project;
use HostMyDocs\Models\Version;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ProjectController
{
    private $filesystem = null;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * Retrieving all information to list all languages from all versions from all projects stored
     *
     * @param string $storageRoot
     * @param string $archiveRoot
     *
     * @return Project[] the list of projects
     */
    public function listProjects($storageRoot, $archiveRoot)
    {
        $projects = [];
        $projectLister  = new Finder();
        $projectLister
            ->ignoreDotFiles(false)
            ->depth('== 0')
            ->directories()
            ->in($storageRoot)
            ->sortByName();
        $projectStructure = [];

        foreach ($projectLister as $projectFolder) {
            $project = new Project($projectFolder->getFilename());

            $projectStructure[] = $projectFolder->getFilename();

            self::listVersions($projectFolder, $project, $projectStructure, $storageRoot, $archiveRoot);

            $projects[] = $project;

            $projectStructure = [];
        }

        return $projects;
    }

    /**
     * @param SplFileInfo $projectFolder
     * @param Project $currentProject
     * @param array $projectStructure
     */
    private function listVersions(SplFileInfo $projectFolder, Project $currentProject, array $projectStructure, string $storageRoot, string $archiveRoot)
    {
        $versionLister  = new Finder();
        $versionLister
            ->ignoreDotFiles(false)
            ->depth('== 0')
            ->directories();

        $versionStructure = $projectStructure;

        /** @var SplFileInfo $versionFolder */
        foreach ($versionLister->in($projectFolder->getRealPath()) as $versionFolder) {
            $version = new Version($versionFolder->getFilename());

            $versionStructure[] = $versionFolder->getFilename();

            self::listLanguages($versionFolder, $version, $versionStructure, $storageRoot, $archiveRoot);

            $currentProject->addVersion($version);

            $versionStructure = $projectStructure;
        }
    }

    /**
     * @param SplFileInfo $versionFolder
     * @param Version $currentVersion
     * @param array $versionStructure
     */
    private function listLanguages(SplFileInfo $versionFolder, Version $currentVersion, array $versionStructure, string $storageRoot, string $archiveRoot)
    {
        $documentRoot = str_replace($_SERVER['DOCUMENT_ROOT'], '', $storageRoot);

        $languageLister = new Finder();
        $languageLister
            ->ignoreDotFiles(false)
            ->depth('== 0')
            ->directories();

        $languageStructure = $versionStructure;

        /** @var SplFileInfo $languageFolder */
        foreach ($languageLister->in($versionFolder->getRealPath()) as $languageFolder) {
            $languageStructure[] = $languageFolder->getFilename();

            $indexPath = [
                $documentRoot,
                implode('/', $languageStructure),
                'index.html'
            ];

            $archiveRoot = str_replace($_SERVER['DOCUMENT_ROOT'], '', $archiveRoot);

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


    public function extract($project): bool
    {
        $zipper = new Zipper();

        $name = $project->getName();
        $version = $project->getVersions()[0]->getNumber();
        $language = $project->getVersions()[0]->getLanguages()[0]->getName();
        $archive = $project->getVersions()[0]->getLanguages()[0]->getArchiveFile();

        if (is_file($archive->file) === false) {
            error_log('impossible to open archive file');
            return false;
        }

        error_log("Opening file : " . $archive->file);

        $zipFile = $zipper->make($archive->file);

        $rootCandidates = array_values(array_filter($zipFile->listFiles(), function ($path) {
            return preg_match('@^[^/]+/index\.html$@', $path);
        }));

        if (count($rootCandidates)>1) {
            error_log('More than one index file found');
            return false;
        }

        $splittedPath = explode('/', $rootCandidates[0]);
        $zipRoot = array_shift($splittedPath);

        $destination = [
            '/var/www/html/data/docs', // $this->get('storageRoot'),
            $name,
            $version,
            $language
        ];

        $destinationPath = implode('/', $destination);
        error_log($destinationPath);

        if (filter_var($destinationPath, FILTER_SANITIZE_URL) === false) {
            error_log('extract path contains invalid characters');
            return false;
        }

        if (file_exists($destinationPath)) {
            $this->filesystem->remove($destinationPath);
        }

        if (mkdir($destinationPath, 0755, true) === false) {
            error_log('failed to create folder');
            return false;
        }

        error_log('Extracting to ' . $destinationPath);

        $zipFile->folder($zipRoot)->extractTo($destinationPath);

        $zipper->close();

        return true;
    }



    /**
     * Delete doc for project and corresponding backup
     *
     * @return null|string null or the error's description if one append
     */
    public function deleteProject($name, $version, $language, $archiveFolder, $storageFolder)
    {
        $fileName = [
            $name,
            $version,
            $language
        ];

        $archiveFileName = preg_replace("/^$/", "*", $fileName);

        $archiveDestination = [
            $archiveFolder,
            implode('-', $archiveFileName).'.zip'
        ];

        $archiveDestinationPath = implode(DIRECTORY_SEPARATOR, $archiveDestination);

        $archiveToDelete = glob($archiveDestinationPath);
        if (count($archiveToDelete) !== 0) {
            foreach ($archiveToDelete as $f) {
                unlink($f);
            }
        } else {
            error_log('No backup found ' . $archiveDestinationPath);
        }

        $storageDestination = [
            $storageFolder,
            implode(DIRECTORY_SEPARATOR, $fileName)
        ];

        $storageDestinationPath = implode(DIRECTORY_SEPARATOR, $storageDestination);

        if (file_exists($storageDestinationPath) === true) {
            try {
                if (self::deleteDirectory($storageDestinationPath) === false) {
                    return 'deleting project failed.';
                }
                if (self::deleteEmptyDirectories($storageFolder) === false) {
                    return 'deleting empty folders failed. /listProject will fail until you delete them';
                }
            } catch (\Exception $e) {
                return 'deleting project failed.';
            }
        } else {
            return 'project does not exists.';
        }

        return null;
    }

    /**
     * delete the directory in argument $dir
     *
     * @param  [string] $dir path to dir to delete
     *
     * @return bool
     */
    private function deleteDirectory($dir) : bool
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        $dirContent = array_diff(scandir($dir), array('..', '.'));
        foreach ($dirContent as $item) {
            if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
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
    private function deleteEmptyDirectories($dir) : bool
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return true;
        }

        $dirContentBefore = array_diff(scandir($dir), array('..', '.'));
        foreach ($dirContentBefore as $item) {
            if (!self::deleteEmptyDirectories($dir . DIRECTORY_SEPARATOR . $item)) {
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
