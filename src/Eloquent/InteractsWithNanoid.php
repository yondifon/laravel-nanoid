<?php

namespace Malico\LaravelNanoid\Eloquent;

use Hidehalo\Nanoid\Client;

trait InteractsWithNanoid
{
    /**
     * Get the nanoid length.
     */
    protected function getNanoidLength(): ?int
    {
        if (is_array($this->nanoidLength)) {
            return random_int($this->nanoidLength[0], $this->nanoidLength[1]);
        }

        return $this->nanoidLength;
    }

    /**
     * Generate a nanoid.
     */
    public function generateNanoid(): string
    {
        $client = new Client();

        return $this->nanoidPrefix . $client->generateId($this->getNanoidLength(), Client::MODE_DYNAMIC);
    }
}
