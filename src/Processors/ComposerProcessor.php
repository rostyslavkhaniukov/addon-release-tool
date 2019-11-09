<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use Composer\Console\Application;
use Fluffy\GithubClient\Client;
use Symfony\Component\Console\Input\ArrayInput;

class ComposerProcessor extends JsonProcessor
{
    private $composerJsonContent;

    public function __construct(Client $client, string $owner, string $repository)
    {
        parent::__construct($client, $owner, $repository);

        $this->take('composer.json')->take('composer.lock');

        $this->composerJsonContent = json_decode($this->workingFiles['composer.json']->getContent(), true);
    }

    public function ensure(string $dependency, bool $isDev = false): self
    {
        $packagesKey = $isDev ? 'require-dev' : 'require';
        if (!array_key_exists($dependency, $this->composerJsonContent[$packagesKey])) {
            $this->composerJsonContent[$packagesKey][$dependency] = '*';
        }

        $newContent = json_encode($this->composerJsonContent, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) . "\n";
        $this->workingFiles['composer.json']->setContent($newContent);

        $this->update();

        return $this;
    }

    public function update(): self
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'composer');
        $sysDir = sys_get_temp_dir();

        $tempComposerJson = $tempFilePath . '.json';
        $tempComposerLock = $tempFilePath . '.lock';

        $content = json_encode($this->composerJsonContent, JSON_PRETTY_PRINT);
        file_put_contents($tempComposerJson, $content);

        @ini_set('memory_limit', '1536M');
        putenv("COMPOSER={$tempComposerJson}");
        putenv("COMPOSER_VENDOR_DIR=${sysDir}/test-vendor");


        $a = [];
        $a['github-oauth']['github.com'] = '38706f70ba9b6ae6ef3c917118145000f8c00bff';

        putenv("COMPOSER_AUTH=" . json_encode($a));
        $input = new ArrayInput(['command' => 'update', '--ignore-platform-reqs' => null]);

        $app = new Application();
        $app->setCatchExceptions(false);

        try {
            $app->run($input);
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
        }

        var_dump('ss');

        $this->workingFiles['composer.lock']->setContent(file_get_contents($tempComposerLock));
        return $this;
    }

    public function checkLocked(string $checkedPackage): bool
    {
        $packages = $this->composerJsonContent['packages'] ?? [];
        foreach ($packages as $package) {
            if ($package['name'] === $checkedPackage) {
                return true;
            }
        }

        return false;
    }
}
