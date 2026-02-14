<?php

namespace Malico\LaravelNanoid;

use Hidehalo\Nanoid\Client;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
use InvalidArgumentException;

trait HasNanoids
{
    use HasUniqueStringIds;

    protected static array $nanoidFormatTokens = [];

    public function setUniqueIds()
    {
        foreach ($this->uniqueIds() as $column) {
            if (! empty($this->{$column})) {
                continue;
            }

            $this->{$column} = $this->newUniqueIdForColumn($column);
        }
    }

    /**
     * @return string
     */
    public function newUniqueId()
    {
        return $this->newUniqueIdForColumn($this->getKeyName());
    }

    protected function newUniqueIdForColumn(string $column): string
    {
        return $this->generateNanoid($column);
    }

    /**
     * @param  mixed  $value
     */
    protected function isValidUniqueId($value): bool
    {
        return true;
    }

    protected function newNanoId(string $column): string
    {
        return $this->generateNanoIdSegment(
            $this->nanoidClient(),
            $this->getNanoIdAlphabet($column),
            $this->getNanoidLength($column),
        );
    }

    protected function generateNanoid(string $column): string
    {
        $format = $this->getNanoIdFormat($column);
        $prefix = $this->getNanoIdPrefix($column);
        $length = $this->getNanoidLength($column);
        $alphabet = $this->getNanoIdAlphabet($column);
        $client = $this->nanoidClient();

        if (! $format) {
            return $prefix.$this->generateNanoIdSegment($client, $alphabet, $length);
        }

        if ($prefix !== '') {
            throw new InvalidArgumentException('Cannot use nanoidFormat and nanoidPrefix together.');
        }

        if ($length !== null) {
            throw new InvalidArgumentException('Cannot use nanoidFormat and nanoidLength together.');
        }

        return $this->renderNanoIdFormat($client, $alphabet, $format);
    }

    protected function renderNanoIdFormat(Client $client, ?string $alphabet, string $format): string
    {
        $parts = [];

        foreach ($this->getNanoIdFormatTokens($format) as $token) {
            if (is_string($token)) {
                $parts[] = $token;

                continue;
            }

            [$minLength, $maxLength] = $token;
            $length = $minLength === $maxLength
                ? $minLength
                : random_int($minLength, $maxLength);

            $parts[] = $this->generateNanoIdSegment($client, $alphabet, $length);
        }

        return implode('', $parts);
    }

    protected function generateNanoIdSegment(Client $client, ?string $alphabet, ?int $length): string
    {
        if ($alphabet !== null) {
            return $client->formattedId($alphabet, $length);
        }

        return $client->generateId($length, Client::MODE_DYNAMIC);
    }

    protected function getNanoIdPrefix(?string $column = null): string
    {
        $prefix = $this->getNanoIdOption('nanoidPrefix', $column);

        return is_string($prefix) ? $prefix : '';
    }

    /**
     * Get the nanoid length.
     */
    protected function getNanoidLength(?string $column = null): ?int
    {
        $nanoIdLength = $this->getNanoIdOption('nanoidLength', $column);

        if ($nanoIdLength === null) {
            return null;
        }

        if (is_array($nanoIdLength)) {
            if (! $this->isNanoIdLengthRange($nanoIdLength)) {
                throw new InvalidArgumentException('nanoidLength must be an integer or a [min, max] range.');
            }

            return random_int((int) $nanoIdLength[0], (int) $nanoIdLength[1]);
        }

        return (int) $nanoIdLength;
    }

    protected function getNanoIdAlphabet(?string $column = null): ?string
    {
        $alphabet = $this->getNanoIdOption('nanoidAlphabet', $column);

        return is_string($alphabet) ? $alphabet : null;
    }

    protected function getNanoIdFormat(?string $column = null): ?string
    {
        $format = $this->getNanoIdOption('nanoidFormat', $column);

        if ($format !== null && ! is_string($format)) {
            throw new InvalidArgumentException('nanoidFormat must be a string.');
        }

        return is_string($format) ? $format : null;
    }

    protected function getNanoIdOption(string $name, ?string $column = null)
    {
        $value = null;

        if (property_exists($this, $name)) {
            $value = $this->{$name};
        }

        if (method_exists($this, $name)) {
            $value = $this->{$name}();
        }

        return $this->getNanoIdOptionForColumn($value, $column);
    }

    protected function getNanoIdOptionForColumn($value, ?string $column)
    {
        if (! is_array($value) || $column === null) {
            return $value;
        }

        if (array_key_exists($column, $value)) {
            return $value[$column];
        }

        if (array_key_exists('*', $value)) {
            return $value['*'];
        }

        return $this->isNanoIdLengthRange($value) ? $value : null;
    }

    protected function getNanoIdFormatTokens(string $format): array
    {
        if (isset(self::$nanoidFormatTokens[$format])) {
            return self::$nanoidFormatTokens[$format];
        }

        $tokens = [];
        $offset = 0;

        while (preg_match('/\{(\d+)(?:-(\d+))?\}/', $format, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $matchText = $matches[0][0];
            $matchIndex = $matches[0][1];

            if ($matchIndex > $offset) {
                $tokens[] = substr($format, $offset, $matchIndex - $offset);
            }

            $minLength = (int) $matches[1][0];
            $maxLength = isset($matches[2][0]) ? (int) $matches[2][0] : $minLength;

            if ($minLength > $maxLength) {
                [$minLength, $maxLength] = [$maxLength, $minLength];
            }

            $tokens[] = [$minLength, $maxLength];
            $offset = $matchIndex + strlen($matchText);
        }

        if ($offset < strlen($format)) {
            $tokens[] = substr($format, $offset);
        }

        self::$nanoidFormatTokens[$format] = $tokens;

        return $tokens;
    }

    protected function nanoidClient(): Client
    {
        static $client = null;

        if ($client instanceof Client) {
            return $client;
        }

        $client = new Client;

        return $client;
    }

    protected function isNanoIdLengthRange(array $value): bool
    {
        return array_key_exists(0, $value)
            && array_key_exists(1, $value)
            && count($value) === 2
            && is_numeric($value[0])
            && is_numeric($value[1]);
    }
}
