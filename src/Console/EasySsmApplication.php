<?php

declare(strict_types=1);

namespace EonX\EasySsm\Console;

use Symfony\Component\Console\Application;

final class EasySsmApplication extends Application
{
    /**
     * @var string
     */
    public const VERSION = '1.0.2';

    /**
     * @param \Symfony\Component\Console\Command\Command[] $commands
     */
    public function __construct(array $commands)
    {
        parent::__construct('easy-ssm', self::VERSION);

        $this->addCommands($commands);
    }
}
