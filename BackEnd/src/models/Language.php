<?php

namespace HostMyDocs\Models;

use Psr\Http\Message\UploadedFileInterface;

/**
 * Model representing a programming language of a project
 */
class Language extends BaseModel
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
     * @var null|UploadedFileInterface a downloadable zip of the current language and version of the documentation for the current project
     * 		using the psr-7 interface
     */
    private $archiveFile = null;

    /**
     * Build a JSON serializable array
     *
     * @return array an array containing the informations about this object for JSON serialization
     */
    public function jsonSerialize(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->indexFile !== null) {
            $data['indexPath'] = $this->indexFile;
        }

        if ($this->archiveFile !== null) {
            $data['archivePath'] = $this->archiveFile->file;
        }

        return $data;
    }

    /**
     * Get the name of the language
     *
     * @return null|string the name of the language
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of this Language if it is valid
     *
     * @param null|string $name the new value for the name
     * @param bool $allowEmpty whether the empty string ("") is allowed
     *
     * @return null|Language this Language if $name is valid, null otherwise
     */
    public function setName(?string $name, bool $allowEmpty = false): ?self
    {
        if ($name === null) {
            $this->logger->info('language name cannot be null');
            return null;
        }

        if (strpos($name, '/') !== false) {
            $this->logger->info('language name cannot contains slashes');
            return null;
        }

        if (strlen($name) === 0 && !$allowEmpty) {
            $this->logger->info('language name cannot be empty');
            return null;
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Get the path to the index.html file of the documentation
     *
     * @return null|string
     */
    public function getIndexFile(): ?string
    {
        return $this->indexFile;
    }

    /**
     * Set the path to the index.html file of the documentation
     *
     * @param null|string $indexFile the path to the index file
     *
     * @return Language this language
     */
    public function setIndexFile(?string $indexFile): self
    {
        $this->indexFile = $indexFile;
        return $this;
    }

    /**
     * Get the archive file of this documentation
     *
     * @return null|UploadedFileInterface the archive
     */
    public function getArchiveFile(): ?UploadedFileInterface
    {
        return $this->archiveFile;
    }

    /**
     * Set the archive file of this documentation using the psr-7 documentation
     *
     * @param UploadedFileInterface $archiveFile the archive file
     *
     * @return Language this Language if $archiveFile is valid, null otherwise
     */
    public function setArchiveFile(UploadedFileInterface $archiveFile): ?self
    {
        $this->archiveFile = $archiveFile;

        return $this;
    }
}
