<?php

namespace HostMyDocs\Models;

/**
 * Model representing a Project
 */
class Project extends BaseModel
{
    /**
     * @var null|string name of the project
     */
    private $name = null;

    /**
     * @var Version[] available versions of the project
     */
    private $versions = [];

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
     * Get the name of the project
     *
     * @return null|string the name of the project
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of this Project if it is valid
     *
     * @param null|string $name the new value for the name
     *
     * @return null|Project this Project if $name is valid, null otherwise
     */
    public function setName(?string $name): ?self
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
     * Get the array containing all the Version of this project
     *
     * @return Version[] the Versions of this project
     */
    public function getVersions(): array
    {
        return $this->versions;
    }

    /**
     * Return the first version added to this project or null if none where added
     *
     * @return null|Version the first Version of this project or null if there is no Version for this project
     */
    public function getFirstVersion(): ?Version
    {
        if (count($this->versions) === 0) {
            return null;
        }
        return $this->versions[0];
    }

    /**
     * Replace the current Version array of this Project by the one given in parameter
     *
     * @param Version[] $versions the new array of Version of this project
     *
     * @return Project this project
     */
    public function setVersions(array $versions): self
    {
        $this->versions = $versions;

        return $this;
    }

    /**
     * Add a new Version to this Project
     *
     * @param Version $version the Version to add to this project
     *
     * @return Project this project
     */
    public function addVersion(Version $version): self
    {
        $this->versions[] = $version;

        return $this;
    }
}
