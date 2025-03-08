<?php

namespace Malico\LaravelNanoid;

use Hidehalo\Nanoid\Client;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;

trait HasNanoids
{
    use HasUniqueStringIds;

    /**
     * @return string
     */
    public function newUniqueId()
    {
        return $this->generateNanoid();
    }

    /**
     * @param  mixed  $value
     */
    protected function isValidUniqueId($value): bool
    {
        return true;
    }

    /**
     * Generate a nanoid.
     */
    protected function generateNanoid(): string
    {
        return $this->getNanoIdPrefix().$this->newNanoId();
    }

    protected function newNanoId(): string
    {
        $client = new Client;

        if ($alphabet = $this->getNanoIdAlphabet()) {
            return $client->formattedId($alphabet, $this->getNanoidLength());
        }

        return $client->generateId($this->getNanoidLength(), Client::MODE_DYNAMIC);
    }

    protected function getNanoIdPrefix(): string
    {
        if (property_exists($this, 'nanoidPrefix')) {
            return $this->nanoidPrefix;
        }

        if (method_exists($this, 'nanoidPrefix')) {
            return $this->nanoidPrefix();
        }

        return '';
    }

    /**
     * Get the nanoid length.
     */
    protected function getNanoidLength(): ?int
    {
        $nanoIdLength = null;

        if (property_exists($this, 'nanoidLength')) {
            $nanoIdLength = $this->nanoidLength;
        }

        if (method_exists($this, 'nanoidLength')) {
            $nanoIdLength = $this->nanoidLength();
        }

        if (is_array($nanoIdLength)) {
            return random_int($nanoIdLength[0], $nanoIdLength[1]);
        }

        return $nanoIdLength;
    }

    protected function getNanoIdAlphabet(): ?string
    {
        if (property_exists($this, 'nanoidAlphabet')) {
            return $this->nanoidAlphabet;
        }

        if (method_exists($this, 'nanoidAlphabet')) {
            return $this->nanoidAlphabet();
        }

        return null;
    }
}
