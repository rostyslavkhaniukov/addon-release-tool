<?php
declare(strict_types=1);

namespace AirSlate\Releaser\Enums;

interface FileMode
{
    /** @var string */
    public const BLOB = '100644';

    /** @var string */
    public const EXECUTABLE = '100755';

    /** @var string */
    public const SUBDIRECTORY = '040000';

    /** @var string */
    public const SUBMODULE = '160000';

    /** @var string */
    public const SYMLINK = '120000';
}
