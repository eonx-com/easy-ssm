<?php

declare(strict_types=1);

namespace EonX\EasySsm\Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class DumpEnvCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('dump-env')
            ->setDescription('Dump env vars in a PHP file to improve loading time.')
            ->addOption(
                'filename',
                'f',
                InputOption::VALUE_OPTIONAL,
                'File name to generate',
                \sprintf('%s/.env.local.php', \getcwd())
            )
            ->addOption(
                'includes',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Env vars to include from $_SERVER',
                []
            )
            ->addOption(
                'excludes',
                'e',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Env vars to exclude from dump',
                []
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $includes = $input->getOption('includes');
        $excludes = $input->getOption('excludes');
        $vars = \var_export($this->loadEnv($includes, $excludes), true);

        $contents = <<<EOF
<?php

// This file was generated by running "easy-ssm dump-env"

return ${vars};

EOF;

        $filename = $input->getOption('filename');
        $this->filesystem->dumpFile($filename, $contents);

        $output->writeln(\sprintf('Successfully dumped env vars in <info>%s</info>', $filename));

        return 0;
    }

    /**
     * @param string[] $includes
     * @param string[] $excludes
     *
     * @return mixed[]
     */
    private function loadEnv(array $includes, array $excludes): array
    {
        $env = $_ENV;

        foreach ($includes as $include) {
            if (isset($_SERVER[$include])) {
                $env[$include] = $_SERVER[$include];
            }
        }

        foreach ($excludes as $exclude) {
            unset($env[$exclude]);
        }

        return $env;
    }
}
