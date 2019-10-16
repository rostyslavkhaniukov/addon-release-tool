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
}
