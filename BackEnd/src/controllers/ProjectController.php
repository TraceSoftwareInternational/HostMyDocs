<?php
namespace HostMyDocs\Controllers;

use Chumper\Zipper\Zipper;
use HostMyDocs\Models\Language;
use HostMyDocs\Models\Project;
use HostMyDocs\Models\Version;
use Psr\Log\LoggerInterface;
use Slim\Container;
use Slim\Http\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * class used to create, list and delete the projects
 * you must get it using the slim dependency injector
 *
 * @see https://www.slimframework.com/docs/concepts/di.html
 */
class ProjectController
{
    /**
     * @var Filesystem Object used to interact with the projects folder
     */
    private $filesystem;

    /**
     * @var string Path to the folder where project are stored
     */
    private $storageRoot;

    /**
     * @var string Path to the folder where archives are stored
     */
    private $archiveRoot;

    /**
     * @var LoggerInterface psr-3 compatible logger
     */
    private $logger;

    /**
     * Create a project controller
     *
     * You must not call this function by yourselves but get an instance from the slim container
     *
     * @param Container $container the slim container, it must contain the following keys
     * 		- string storageRoot Path to the folder where project are stored
     * 		- string archiveRoot Path to the folder where archives are stored
     * 		- Psr\Log\LoggerInterface logger psr-3 compatible logger
     *
     * @throws InvalidArgumentException when the container miss a key
     *
     * @see https://www.slimframework.com/docs/concepts/di.html
     */
    public function __construct(Container $container)
    {
        if (isset($container['storageRoot']) === false) {
            throw new \InvalidArgumentException("Container doesn't contain the key 'storageRoot'");
        }

        if (isset($container['archiveRoot']) === false) {
            throw new \InvalidArgumentException("Container doesn't contain the key 'archiveRoot'");
        }

        if (isset($container['logger']) === false) {
            throw new \InvalidArgumentException("Container doesn't contain the key 'logger'");
        }

        $this->filesystem = new Filesystem();
        $this->storageRoot = $container['storageRoot'];
        $this->archiveRoot = $container['archiveRoot'];
        $this->logger = $container['logger'];
    }

    /**
     * Retrieve all projects stored and asks for their versions
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
            $project = (new Project($this->logger))->setName($projectFolder->getFilename());

            $projectStructure[] = $projectFolder->getFilename();

            $this->listVersions($projectFolder, $project, $projectStructure);

            $projects[] = $project;

            $projectStructure = [];
        }

        return $projects;
    }

    /**
     * Retrieve all versions for a project stored and asks for their languages
     *
     * it has no return since it modify the project given in parameter
     *
     * @param SplFileInfo $projectFolder Folder where the versions folders are found
     * @param Project $currentProject Object containing the project being processed
     * @param array $projectStructure array containing the parts of the path to the project
     * 		e.g. ['DocumentationName']
     */
    private function listVersions(SplFileInfo $projectFolder, Project &$currentProject, array $projectStructure)
    {
        $versionLister  = new Finder();
        $versionLister
            ->ignoreDotFiles(false)
            ->depth('== 0')
            ->directories();

        $versionStructure = $projectStructure;

        /** @var SplFileInfo $versionFolder */
        foreach ($versionLister->in($projectFolder->getRealPath()) as $versionFolder) {
            $version = (new Version($this->logger))->setNumber($versionFolder->getFilename());

            $versionStructure[] = $versionFolder->getFilename();

            $this->listLanguages($versionFolder, $version, $versionStructure);

            $currentProject->addVersion($version);

            $versionStructure = $projectStructure;
        }
    }

    /**
      * Retrieve all languages for a version of a project stored
      *
      * it has no return since it modify the Version given in parameter
      *
      * @param SplFileInfo $versionFolder Folder where the languages folders are found
      * @param Version $currentVersion Object containing the version being processed
      * @param array $versionStructure array containing the parts of the path to the version
      * 		e.g. ['DocumentationName', '1.0.0']
      */
    private function listLanguages(SplFileInfo $versionFolder, Version &$currentVersion, array $versionStructure)
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

            $language = (new Language($this->logger))
                ->setName($languageFolder->getFilename())
                ->setIndexFile(implode('/', $indexPath))
                ->setArchiveFile(new UploadedFile($archivePath, null, 'application/zip'));

            $currentVersion->addLanguage($language);

            $languageStructure = $versionStructure;
        }
    }

    /**
     * take the archive from a project and extract it in the storage folder
     *
     * @param  Project $project the project to extract
     *
     * @return bool             whether the extration succeed
     */
    public function extract(Project $project): bool
    {
        $zipper = new Zipper();

        $version = $project->getFirstVersion();
        $language = $version->getFirstLanguage();
        $archive = $language->getArchiveFile();

        if ($version === null) {
            $this->logger->critical('An error occured while building the project (it has no version)');
            return false;
        }

        if ($language === null) {
            $this->logger->critical('An error occured while building the project (it has no language)');
            return false;
        }

        if (is_file($archive->file) === false) {
            $this->logger->warning('impossible to open archive file');
            return false;
        }

        $this->logger->info("Opening file : " . $archive->file);

        $zipFile = $zipper->make($archive->file);

        $rootCandidates = array_values(array_filter($zipFile->listFiles(), function ($path) {
            return preg_match('@^[^/]+/index\.html$@', $path);
        }));

        if (count($rootCandidates) > 1) {
            $this->logger->warning('More than one index file found');
            return false;
        }

        $splittedPath = explode('/', $rootCandidates[0]);
        $zipRoot = array_shift($splittedPath);

        $destinationPath = implode('/', [
            $this->storageRoot,
            $project->getName(),
            $version->getNumber(),
            $language->getName()
        ]);

        if (filter_var($destinationPath, FILTER_SANITIZE_URL) === false) {
            $this->logger->warning('extract path contains invalid characters');
            return false;
        }

        if (file_exists($destinationPath)) {
            $this->filesystem->remove($destinationPath);
        }

        if (mkdir($destinationPath, 0755, true) === false) {
            $this->logger->critical('failed to create folder');
            return false;
        }

        $this->logger->info('Extracting to ' . $destinationPath);

        $zipFile->folder($zipRoot)->extractTo($destinationPath);

        $zipper->close();

        return true;
    }

    /**
     * delete every files targetted by a Project
     *
     * @param  Project $project The project to delete
     *
     * @return bool             whether the deletion succeed
     */
    public function deleteProject(Project $project): bool
    {
        $version = $project->getFirstVersion();
        $language = $version->getFirstLanguage();

        if ($version === null) {
            $this->logger->critical('An error occured while building the project (it has no version)');
            return false;
        }

        if ($language === null) {
            $this->logger->critical('An error occured while building the project (it has no language)');
            return false;
        }

        $fileNameParts = array_filter(
            [
                $project->getName(),
                $version->getNumber(),
                $language->getName()
            ],
            function ($v) {
                return strlen($v) !== 0;
            }
        );

        $archiveDestinationGlob = $this->archiveRoot . DIRECTORY_SEPARATOR . implode('-', $fileNameParts) . '*.zip';
        $archiveToDelete = glob($archiveDestinationGlob);
        if (count($archiveToDelete) !== 0) {
            $this->filesystem->remove($archiveToDelete);
        } else {
            $this->logger->error('No backup found ' . $archiveDestinationGlob);
        }

        $storageDestinationPath = $this->storageRoot . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $fileNameParts);

        if (file_exists($storageDestinationPath) === true) {
            try {
                $this->filesystem->remove($storageDestinationPath);
            } catch (\Exception $e) {
                $this->logger->critical('deleting project failed.');
                return false;
            }
        } else {
            $this->logger->info('project does not exists.');
            return false;
        }

        return true;
    }

    /**
     * remove every empty subfolder of the given folder
     * @param  string $path path to the folder to clean
     * @return bool         whether the cleaning succeed
     */
    public function removeEmptySubFolders(string $path): bool
    {
        $empty=true;
        foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file) {
            $empty &= is_dir($file) && $this->removeEmptySubFolders($file);
        }
        return $empty && rmdir($path);
    }
}
