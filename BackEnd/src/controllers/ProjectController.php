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
    private $storageRoot = "";
    private $archiveRoot = "";

    public function __construct(string $storageRoot, string $archiveRoot)
    {
        $this->filesystem = new Filesystem();
        $this->storageRoot = $storageRoot;
        $this->archiveRoot = $archiveRoot;
    }

    /**
     * Retrieving all information to list all languages from all versions from all projects stored
     *
     * @param string $this->storageRoot
     * @param string $this->archiveRoot
     *
     * @return Project[] the list of projects
     */
    public function listProjects(): array
    {
        $projects = [];
        $projectLister  = new Finder();
        $projectLister
            ->ignoreDotFiles(false)
            ->depth('== 0')
            ->directories()
            ->in($this->storageRoot)
            ->sortByName();
        $projectStructure = [];

        foreach ($projectLister as $projectFolder) {
            $project = new Project($projectFolder->getFilename());

            $projectStructure[] = $projectFolder->getFilename();

            $this->listVersions($projectFolder, $project, $projectStructure);

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
    private function listVersions(SplFileInfo $projectFolder, Project $currentProject, array $projectStructure)
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
        $documentRoot = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->storageRoot);

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

            $archiveRoot = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->archiveRoot);

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


    public function extract(Project $project): bool
    {
        $zipper = new Zipper();

        $version = $project->getVersions()[0];
        $language = $version->getLanguages()[0];
        $archive = $language->getArchiveFile();

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
            $this->storageRoot,
            $project->getName(),
            $version->getNumber(),
            $language->getName()
        ];

        $destinationPath = implode('/', $destination);

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
     * @return bool
     */
    public function deleteProject(Project $project): bool
    {
        $version = $project->getVersions()[0];
        $language = $version->getLanguages()[0];

        $fileName = [
			$project->getName(),
			$version->getNumber(),
			$language->getName()
        ];

        $archiveFileName = preg_replace("/^$/", "*", $fileName);

        $archiveDestination = [
            $this->archiveRoot,
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
            $this->storageRoot,
            implode(DIRECTORY_SEPARATOR, $fileName)
        ];

        $storageDestinationPath = implode(DIRECTORY_SEPARATOR, $storageDestination);

        if (file_exists($storageDestinationPath) === true) {
            try {
                if (self::deleteDirectory($storageDestinationPath) === false) {
                    error_log('deleting project failed.');
                    return false;
                }
                if (self::deleteEmptyDirectories($this->storageRoot) === false) {
                    error_log('deleting empty folders failed. /listProject will fail until you delete them');
                    return false;
                }
            } catch (\Exception $e) {
                error_log('deleting project failed.');
                return false;
            }
        } else {
            error_log('project does not exists.');
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
    private function deleteDirectory(string $dir) : bool
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
    private function deleteEmptyDirectories(string $dir) : bool
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
