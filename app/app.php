<?php

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

include_once dirname(__DIR__) . '/vendor/autoload.php';

$app = new Silly\Application();

$app->command('run name', function ($name, InputInterface $input, OutputInterface $output) {

    $formatter = $this->getHelperSet()->get('formatter');
    $question = $this->getHelperSet()->get('question');
    $process = $this->getHelperSet()->get('process');
    $debug = $this->getHelperSet()->get('debug_formatter');

//    if (!$question->ask($input, $output, new ConfirmationQuestion('Continue with this action?'.PHP_EOL, false))) {        return;    }

    $text = 'Running: ' . $name;
    $output->writeln($text);

    $p = ProcessBuilder::create(explode(' ', $name))->getProcess();
    $progress = new ProgressBar($output, 1);
    $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% (%filename%) %elapsed:6s%/%estimated:-6s% %memory:6s%');

    $progress->setMessage($name, 'filename');

    $progress->start();
    $process->run($output, $p);
    $progress->advance();

    $progress->finish();

});

return $app;