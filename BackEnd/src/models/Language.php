<?php

namespace HostMyDocs\Models;

use Psr\Http\Message\UploadedFileInterface;

class Language implements \JsonSerializable
{
    /**
     * @var null|string Name of the programming language
     */
    private $name = null;

    /**
     * @var null|string path to the index file of the documentation
     */
    private $indexFile = null;

    /**
     * @var null|UploadedFileInterface path to a downloadable zip of the current language and version of the documentation for the current project
     */
    private $archiveFile = null;

    /**
     * Language constructor.
     * @param string $name
     * @param string $indexFile
     * @param UploadedFileInterface $archiveFile
     */
    public function __construct($name, $indexFile, $archiveFile)
    {
        $this->name = $name;
        $this->indexFile = $indexFile;
        $this->archiveFile = $archiveFile;
    }

    /**
     * Build a JSON serializable array
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->indexFile !== null) {
            $data['indexPath'] = $this->indexFile;
        }

        if ($this->archiveFile !== null) {
            $data['archivePath'] = $this->archiveFile;
        }

        return $data;
    }

    /**
     * @return null|string
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return null|Language
     */
    public function setName($name) : ?self
    {
        if ($name === null) {
            error_log('language name cannot be null');
            return null;
        }

        if (strpos($name, '/') !== false) {
            error_log('language name cannot contains slashes');
            return null;
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getIndexFile() : ?string
    {
        return $this->indexFile;
    }

    /**
     * @param null|string $indexFile
     * @return Language
     */
    public function setIndexFile($indexFile) : self
    {
        $this->indexFile = $indexFile;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getArchiveFile(): ?UploadedFileInterface
    {
        return $this->archiveFile;
    }

    /**
     * @param null|UploadedFileInterface $archiveFile
     * @return Language
     */
    public function setArchiveFile($archiveFile): ?self
    {
        if (pathinfo($archiveFile->getClientFilename(), PATHINFO_EXTENSION) !== 'zip') {
            $errorMessage = 'archive is not a zip file';
            return null;
        }

        $this->archiveFile = $archiveFile;

        return $this;
    }
}
