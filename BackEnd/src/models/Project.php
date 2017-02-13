<?php

namespace HostMyDocs\Models;

use vierbergenlars\SemVer\version as Semver;

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
            return Semver::gt($a->getNumber(), $b->getNumber());
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
     * @return Project
     */
    public function setName($name) : Project
    {
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
    public function setVersions($versions) : Project
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
