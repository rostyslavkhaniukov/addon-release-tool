<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

class ComposerProcessor extends JsonProcessor
{
    public function checkLocked(string $checkedPackage): bool
    {
        $file = json_decode($this->fileBuffer, true);
        $packages = $file['packages'] ?? [];
        foreach ($packages as $package) {
            if ($package['name'] === $checkedPackage) {
                return true;
            }
        }

        return false;
    }
}
