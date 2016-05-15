<?php

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

include_once dirname(__DIR__) . '/vendor/autoload.php';

$app = new Silly\Application();

$app->command('run name* [--args=]* [--dir=]*', function ($name, $args, $dir, OutputInterface $output) {
    $processes = [];
    foreach ($name as $key => $process) {
        if (count($args) > 0 && array_key_exists($key, $args) && null !== ($processArguments = $args[$key])) {
            // Range
            if (preg_match_all('/\{([^\}]+?)\}/', $processArguments, $ranges)) {
                foreach ($ranges[1] as $range) {
                    list($rStart, $rEnd) = explode('-', $range);
                    $num = 0;
                    while ($num <= ($rEnd - $rStart)) {
                        $processes[] = $process . ' ' . escapeshellarg(str_replace('{' . $range . '}', $rStart + $num, $processArguments));
                        $num++;
                    }
                }
            } else {
                $processes[] = $process . ' ' . escapeshellarg($processArguments);
            }
        }
        if (count($dir) > 0 && array_key_exists($key, $dir) && null !== ($processDir = $dir[$key])) {
            if (file_exists($processDir) && is_dir($processDir)) {
                $files = array_diff(scandir($processDir), ['.', '..']);
                foreach ($files as $fileName) {
                    $filePath = $processDir.'/'.$fileName;
                    if (is_file($filePath)) {
                        $processes[] = $process . ' ' . escapeshellarg($filePath);
                    }
                }
            }
        }
    }
    
    if (count($processes) < 1) {
        $processes = $name;
    }

    $output->writeln('Starting processes');

    $progress = new ProgressBar($output, count($processes));
    $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% (%filename%) %elapsed:6s%/%estimated:-6s% %memory:6s%');
    $progress->setMessage($processes[0], 'filename');
    $progress->start();
    
    $processHelper = $this->getHelperSet()->get('process');
    foreach ($processes as $key => $n) {
        $progress->setMessage($n, 'filename');
        $cmd = explode(' ', $n);
        $p = ProcessBuilder::create($cmd)->getProcess();
        $processHelper->run($output, $p);
        $progress->advance();
    }
    $progress->finish();
});

return $app;