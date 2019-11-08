<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class DependencyUpdaterProcessor extends JsonProcessor
{
    public function update(): DependencyUpdaterProcessor
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'composer');

        $sysDir = sys_get_temp_dir();

        $tempComposerJson = $tempFilePath . '.json';
        $tempComposerLock = $tempFilePath . '.lock';
        file_put_contents($tempComposerJson, $this->withFileBuffer);

        @ini_set('memory_limit', '1536M');
        putenv("COMPOSER={$tempComposerJson}");
        putenv("COMPOSER_VENDOR_DIR=${sysDir}/test-vendor");
        $input = new ArrayInput(['command' => 'update', '--ignore-platform-reqs' => null]);

        $app = new Application();
        $app->setCatchExceptions(false);

        try {
            $app->run($input);
        } catch (\Throwable $e) {
        }

        $this->fileBuffer = file_get_contents($tempComposerLock);
        return $this;
    }
}
