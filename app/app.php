<?php

use bouiboui\Spawn\Spawn;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

include_once dirname(__DIR__) . '/vendor/autoload.php';

$app = new Silly\Application();
$spawn = new Spawn();

$app->command('run commands* [--args=]* [--dir=]* [--outfile=]', function ($commands, $args, $dir, $outfile, OutputInterface $output) use ($spawn) {

    // Parse commands
    foreach ($commands as $cNum => $command) {
        if (array_key_exists($cNum, $args)) {
            $spawn->addProcessesFromArguments($command, $args[$cNum]);
        }
        if (array_key_exists($cNum, $dir)) {
            $spawn->addProcessesFromDirectory($command, $dir[$cNum]);
        }
        if (0 === count($args) + count($dir)) {
            $spawn->addSingleProcess($command);
        }
    }

    $progressBar = new ProgressBar($output, $spawn->getProcessesCount());
    $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% (%filename%) %elapsed:6s%/%estimated:-6s% %memory:6s%');

    /** @var ProcessHelper $processHelper */
    $processHelper = $this->getHelperSet()->get('process');

    // Before start
    $spawn->addOnBeforeStartListener(function () use ($output, $progressBar) {
        $output->writeln('Starting processes');
    });

    // Process start
    $spawn->addOnProcessStartListener(function (Process $p) use ($output, $progressBar, $processHelper) {
        $progressBar->setMessage($p->getCommandLine(), 'filename');
        $processHelper->run($output, $p);
    });

    // Process end
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

});

return $app;