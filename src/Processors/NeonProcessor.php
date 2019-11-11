<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use Nette\Neon\Neon;

class NeonProcessor extends JsonProcessor
{
    public function bla(): bool
    {
        $config = Neon::decode($this->fileBuffer);

        $config['services'][] = [
            'tags' => ['phpstan.rules.rule'],
        ];

        var_dump(Neon::encode($config, 1));
        die;

        return true;
    }
}
