<?php

namespace HostMyDocs\Models;

class Version implements \JsonSerializable
{
    /**
     * @var null|string SemVer compliant number of the current version
     */
    private $number = null;

    /**
     * @var Language[]
     */
    private $languages = [];

    /**
     * Version constructor.
     * @param null|string $number
     */
    public function __construct($number)
    {
        $this->number = $number;
    }

    /**
     * Build a JSON serializable array
     *
     * @return array
     */
    public function jsonSerialize() : array
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
     * @return null|string
     */
    public function getNumber() : ?string
    {
        return $this->number;
    }

    /**
     * @param null|string $number
     * @return null|Version
     */
    public function setNumber($number, $allowEmpty = false) : ?self
    {
        if ($number === null) {
            error_log('version cannot be null');
            return null;
        }

        if (strpos($number, '/') !== false) {
            error_log('version cannot contains slashes');
            return null;
        }

        if (strlen($number) === 0 && !$allowEmpty) {
            error_log('version cannot be empty');
            return null;
        }

        $this->number = $number;

        return $this;
    }

    /**
     * @return array
     */
    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    /**
     * @param Language[] $languages
     * @return Version
     */
    public function setLanguages($languages) : self
    {
        if (is_array($languages)) {
            $this->languages = $languages;
        }

        return $this;
    }

    public function addLanguage(Language $language) : void
    {
        $this->languages[] = $language;
    }
}
