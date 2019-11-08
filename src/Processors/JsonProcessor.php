<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

class JsonProcessor extends FileProcessor
{
    /**
     * @param string $path
     * @param string $content
     * @return bool
     */
    public function isset(string $key): bool
    {
        $value = json_decode($this->fileBuffer, true);
        $parts = explode('.', $key);
        foreach ($parts as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $key
     * @return JsonProcessor
     * @throws \Exception
     */
    public function unsetKey(string $key): JsonProcessor
    {
        $value = json_decode($this->fileBuffer, true);
        $original = &$value;
        $parts = explode('.', $key);
        while (count($parts) > 1) {
            $part = array_shift($parts);

            if (isset($value[$part]) && is_array($value[$part])) {
                $value = &$value[$part];
            } else {
                throw new \Exception('Key not found');
            }
        }

        unset($value[array_shift($parts)]);

        $result = json_encode($original, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        $result = preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $result);
        $result .= "\n";

        $this->fileBuffer = $result;
        return $this;
    }

    public function notIsset(string $key): bool
    {
        return !$this->isset($key);
    }
}