<?php

namespace bouiboui\Spawn;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunCommand extends Command
{
    protected function configure()
    {
        $this->setName('run')
            ->addArgument('cmd', InputArgument::IS_ARRAY)
            ->addOption('find', null, InputOption::VALUE_REQUIRED)
            ->addOption('outfile', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $spawn = new Spawn();

        // Parse commands
        $spawn->addProcessesFromCommand($input->getArgument('cmd'), $input->getOption('find'));

        if ($spawn->getProcessesCount() > 0) {

            $progressBar = new ProgressBar($output, $spawn->getProcessesCount());
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% (%filename%) %elapsed:6s%/%estimated:-6s% %memory:6s%');

            /** @var ProcessHelper $processHelper */
            $processHelper = $this->getHelperSet()->get('process');

            // Before start
            $spawn->addOnBeforeStartListener(function () use ($output, $spawn) {
                $formattedLine = $this->getHelper('formatter')
                    ->formatSection('Spawn', sprintf('Starting %d process(es)', $spawn->getProcessesCount()));
                $output->writeln($formattedLine);
            });

            // Process start
            $spawn->addOnProcessStartListener(function (Process $p) use ($output, $progressBar, $processHelper) {
                $progressBar->setMessage($p->getCommandLine(), 'filename');
                $processHelper->run($output, $p);
            });

            // Process end
            $outfile = $input->getOption('outfile');
            $spawn->addOnProcessEndListener(function (Process $p) use ($outfile, $progressBar) {
                $progressBar->advance();
                if (null !== $outfile && file_exists($outfile) && is_writable($outfile)) {
                    file_put_contents($outfile, '$ ' . $p->getCommandLine() . PHP_EOL . $p->getOutput() . PHP_EOL . PHP_EOL, FILE_APPEND);
                }
            });

            // Everything done
            $spawn->addOnFinishListener(function () use ($progressBar) {
                $progressBar->finish();
            });

            // Start!
            $spawn->runProcesses();

        } else {

            // No process detected
            $formattedLine = $this->getHelper('formatter')->formatSection('Spawn', 'Found no process to start.');
            $output->writeln($formattedLine);
        }

    }

}
