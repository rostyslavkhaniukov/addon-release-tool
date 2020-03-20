<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use AirSlate\Releaser\DTO\WorkingFile;
use Closure;
use Exception;
use Illuminate\Support\Arr;

class JsonProcessor extends FileProcessor
{
    /**
     * @param WorkingFile $file
     * @param string $key
     * @return bool
     */
    public function isset(WorkingFile $file, string $key): bool
    {
        $value = json_decode($file->getContent(), true);
        return Arr::has($value, $key);
    }

    /**
     * @param WorkingFile $file
     * @param string $key
     * @return bool
     */
    public function notIsset(WorkingFile $file, string $key): bool
    {
        return !$this->isset($file, $key);
    }

    /**
     * @param string $key
     * @return JsonProcessor
     * @throws Exception
     */
    public function unsetKey(WorkingFile $file, string $key): JsonProcessor
    {
        $keys = array_keys($this->workingFiles);
        $fileKey = reset($keys);
        $file = $this->workingFiles[$fileKey];
        $value = json_decode($file->getContent(), true);
        $original = &$value;
        $parts = explode('.', $key);
        while (count($parts) > 1) {
            $part = array_shift($parts);

            if (isset($value[$part]) && is_array($value[$part])) {
                $value = &$value[$part];
            } else {
                throw new Exception('Key not found');
            }
        }

        unset($value[array_shift($parts)]);

        $result = json_encode($original, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $result);
        $result .= "\n";

        $this->workingFiles[$fileKey]->setContent($result);
        return $this;
    }

    public function map(Closure $callback)
    {
        array_walk($this->workingFiles, function (WorkingFile $file) use ($callback) {
            $content = json_decode($file->getContent(), true);

            $result = json_encode($callback($content), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $result = preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $result);
            $result .= "\n";

            $file->setContent($result);
        });

        return $this;
    }
}
