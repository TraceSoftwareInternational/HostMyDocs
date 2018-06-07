<?php

namespace HostMyDocs\Models;

class Version extends BaseModel
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
     * Build a JSON serializable array
     *
     * @return array
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
     * @return null|string
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param null|string $number
     * @return null|Version
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
     * @return array
     */
    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function getFirstLanguage(): ?Language
    {
        if (count($this->languages) === 0) {
            return null;
        }
        return $this->languages[0];
    }

    /**
     * @param Language[] $languages
     * @return Version
     */
    public function setLanguages(array $languages): self
    {
        if (is_array($languages)) {
            $this->languages = $languages;
        }

        return $this;
    }

    public function addLanguage(Language $language)
    {
        $this->languages[] = $language;
    }
}
