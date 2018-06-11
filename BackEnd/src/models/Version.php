<?php

namespace HostMyDocs\Models;

/**
 * Model representing a Version of a Project
 */
class Version extends BaseModel
{
    /**
     * @var null|string SemVer compliant number of the current version
     */
    private $number = null;

    /**
     * @var Language[] available Languages for this Version
     */
    private $languages = [];

    /**
     * Build a JSON serializable array
     *
     * @return array an array containing the informations about this object for JSON serialization
     */
    public function jsonSerialize(): array
    {
        $data = [];

        if ($this->number !== null) {
            $data['number'] = $this->number;
        }

        if ($this->languages !== []) {
            foreach ($this->languages as $language) {
                $data['languages'][] = $language->jsonSerialize();
            }
        }

        return $data;
    }

    /**
     * Get the number of the project
     *
     * @return null|string the name of the project
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * Set the number of this Version if it is valid
     *
     * @param null|string $number the new value for the number
     * @param bool $allowEmpty whether the empty string ("") is allowed
     *
     * @return null|Version this Version if $number is valid, null otherwise
     */
    public function setNumber(?string $number, bool $allowEmpty = false): ?self
    {
        if ($number === null) {
            $this->logger->info('version cannot be null');
            return null;
        }

        if (strpos($number, '/') !== false) {
            $this->logger->info('version cannot contains slashes');
            return null;
        }

        if (strlen($number) === 0 && !$allowEmpty) {
            $this->logger->info('version cannot be empty');
            return null;
        }

        $this->number = $number;

        return $this;
    }

    /**
     * Get the array containing all the Language for this Version
     *
     * @return Language[] the Languages of this Version
     */
    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    /**
     * Return the first language added to this version or null if none where added
     *
     * @return null|Language the first Language of this project or null if there is no Language for this project
     */
    public function getFirstLanguage(): ?Language
    {
        if (count($this->languages) === 0) {
            return null;
        }
        return $this->languages[0];
    }

    /**
     * Replace the current Language array of this Version by the one given in parameter
     *
     * @param Language[] $languages the new array of Language of this project
     *
     * @return Version this version
     */
    public function setLanguages(array $languages): self
    {
        if (is_array($languages)) {
            $this->languages = $languages;
        }

        return $this;
    }

    /**
     * Add a new Language to this Version
     *
     * @param Language $language the Language to add to this Version
     *
     * @return Version this Version
     */
    public function addLanguage(Language $language): self
    {
        $this->languages[] = $language;

        return $this;
    }
}
