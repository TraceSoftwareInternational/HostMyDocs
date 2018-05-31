<?php

namespace HostMyDocs\Models;

class Project implements \JsonSerializable
{
    /**
     * @var null|string name of the project
     */
    private $name = null;

    /**
     * @var array|Version[] available versions of the project
     */
    private $versions = [];

    public function __construct($name)
    {
        $this->name = $name;
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

        usort($this->versions, function (Version $a, Version $b) {
            return strnatcmp($a->getNumber(), $b->getNumber());
        });

        if ($this->versions !== []) {
            foreach ($this->versions as $version) {
                $data['versions'][] = $version->jsonSerialize();
            }
        }

        return $data;
    }

    /**
     * @return null|string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return null|Project
     */
    public function setName($name) : ?self
    {
        if ($name === null) {
            error_log('project name cannot be null');
            return null;
        }

        if (strpos($name, '/') !== false) {
            error_log('project name cannot contains slashes');
            return null;
        }

        if (strlen($name) === 0) {
            error_log('project name cannot be empty');
            return null;
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return array|Version[]
     */
    public function getVersions() : array
    {
        return $this->versions;
    }

    /**
     * @param Version[] $versions
     * @return Project
     */
    public function setVersions($versions) : self
    {
        $this->versions = $versions;


        return $this;
    }

    /**
     * @param Version $version
     */
    public function addVersion(Version $version) : void
    {
        $this->versions[] = $version;
    }
}
