<?php

namespace HostMyDocs\Models;

class Project extends BaseModel
{
    /**
     * @var null|string name of the project
     */
    private $name = null;

    /**
     * @var array|Version[] available versions of the project
     */
    private $versions = [];

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
    public function setName(?string $name) : ?self
    {
        if ($name === null) {
            $this->logger->info('project name cannot be null');
            return null;
        }

        if (strpos($name, '/') !== false) {
            $this->logger->info('project name cannot contains slashes');
            return null;
        }

        if (strlen($name) === 0) {
            $this->logger->info('project name cannot be empty');
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

    public function getFirstVersion(): ?Version
    {
        if (count($this->versions) === 0) {
            return null;
        }
        return $this->versions[0];
    }

    /**
     * @param Version[] $versions
     * @return Project
     */
    public function setVersions(array $versions) : self
    {
        $this->versions = $versions;


        return $this;
    }

    /**
     * @param Version $version
     */
    public function addVersion(Version $version)
    {
        $this->versions[] = $version;
    }
}
