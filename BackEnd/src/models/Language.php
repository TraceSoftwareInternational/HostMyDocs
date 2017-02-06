<?php

namespace HostMyDocs\Models;

class Language implements \JsonSerializable
{
    /**
     * @var null|string Name of the programming language
     */
    private $name = null;

    /**
     * @var null|string path tho the index file of the documentation
     */
    private $indexFile = null;

    /**
     * @var null|string path to a downloadable zip of the current language and version of the documentation for the current project
     */
    private $archiveFile = null;

    /**
     * Language constructor.
     * @param string $name
     * @param string $indexFile
     * @param string $archiveFile
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
            $data['language'] = $this->name;
        }

        if ($this->indexFile !== null) {
            $data['index'] = $this->indexFile;
        }

        if ($this->archiveFile !== null) {
            $data['archive'] = $this->archiveFile;
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
     * @return Language
     */
    public function setName($name) : self
    {
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
}
