<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Services;

class ConstantsFetcher
{
    public static function fetch(string $schema)
    {
        $schema = json_decode($schema, true);
        return static::parse($schema, []);
    }

    public static function parse(array $schema, array $constants)
    {
        $local = $constants;
        foreach ($schema as $subschema) {
            if (is_array($subschema)) {
                $local = array_merge($local, static::parse($subschema, $constants));
                continue;
            }

            if (is_bool($subschema) || $subschema === null) {
                continue;
            }

            if ($subschema === strtoupper($subschema)) {
                $local[] = $subschema;
            }
        }

        return array_unique($local);
    }
}
