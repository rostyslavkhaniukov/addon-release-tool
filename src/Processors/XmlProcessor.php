<?php

declare(strict_types=1);

namespace AirSlate\Releaser\Processors;

use SimpleXMLElement;

class XmlProcessor extends FileProcessor
{
    public function checkPSR12()
    {
        $lastFile = end($this->workingFiles);

        $file = new SimpleXMLElement($lastFile->getContent());

        foreach ($file->rule as $rule) {
            if (in_array('PSR12', (array)$rule['ref'])) {
                return true;
            }
        }

        return false;
    }
}
