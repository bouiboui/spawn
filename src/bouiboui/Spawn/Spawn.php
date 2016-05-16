<?php

namespace bouiboui\Spawn;

use Symfony\Component\Process\ProcessBuilder;

class Spawn
{
    private $processes = [];

    // Listeners
    private $beforeStartListeners = [];
    private $processStartListeners = [];
    private $processEndListeners = [];
    private $finishListeners = [];

    /**
     * Adds processes when arguments are provided
     * Detects ranges in the arguments and spawns adequate number of processes
     * @param string $command
     * @param string $arguments
     */
    public function addProcessesFromArguments($command, $arguments)
    {
        // Detect range
        preg_match_all('/\{([^\}]+?)\}/', $arguments, $ranges);

        // Add processes from range
        if (count($ranges[$pregResults = 1]) > 0) {
            foreach ($ranges[$pregResults] as $range) {
                list($rStart, $rEnd) = explode('-', $range);
                $rNum = -1;
                while (++$rNum <= $rEnd - $rStart) {
                    $this->addProcess([$command, str_replace('{' . $range . '}', $rStart + $rNum, $arguments)]);
                }
            }
            return;
        }

        // No range detected; Add process as-is (light!)
        $this->addSingleProcess($command, $arguments);
    }

    /**
     * Convenience function to build and add a process to spawn
     * @param array $arguments
     */
    private function addProcess(array $arguments = [])
    {
        $process = ProcessBuilder::create(explode(' ', $arguments[0])); // $arguments[0] = command
        foreach (array_slice($arguments, 1) as $singleArgument) {
            $process->add($singleArgument);
        }
        $this->processes[] = $process;
    }

    /**
     * Adds a process without detecting ranges
     * @param string $command
     * @param string|null $arguments
     */
    public function addSingleProcess($command, $arguments = null)
    {
        $this->addProcess(null === $arguments ? [$command] : [$command, $arguments]);
    }

    /**
     * Adds a process for each file in a directory
     * @param string $command
     * @param string $directory
     */
    public function addProcessesFromDirectory($command, $directory)
    {
        if (file_exists($directory) && is_dir($directory)) {
            foreach (array_diff(scandir($directory), ['.', '..']) as $fileName) {
                $filePath = rtrim($directory, '/') . '/' . $fileName;
                if (is_file($filePath)) {
                    $this->addProcess([$command, $filePath]);
                }
            }
        }
    }

    /**
     * Returns the number of processes to be spawned
     * Useful to set the maximum value of the progress bar
     * @return int
     */
    public function getProcessesCount()
    {
        return count($this->processes);
    }

    /**
     * Loops through the processes and triggers listeners at each step of the lifecycle
     * 1. beforeStart, 2. processStart, 3. processEnd, 4. finish
     */
    public function runProcesses()
    {
        foreach ($this->beforeStartListeners as $listener) {
            $listener();
        }
        foreach ($this->processes as $processBuilder) {
            $process = $processBuilder->getProcess()->setTimeout(null);
            foreach ($this->processStartListeners as $listener) {
                $listener($process);
            }
            foreach ($this->processEndListeners as $listener) {
                $listener($process);
            }
        }
        foreach ($this->finishListeners as $listener) {
            $listener();
        }
    }

    /** @param callable $listener */
    public function addOnProcessStartListener(callable $listener)
    {
        $this->processStartListeners[] = $listener;
    }

    /** @param callable $listener */
    public function addOnProcessEndListener(callable $listener)
    {
        $this->processEndListeners[] = $listener;
    }

    /** @param callable $listener */
    public function addOnFinishListener(callable $listener)
    {
        $this->finishListeners[] = $listener;
    }

    /** @param callable $listener */
    public function addOnBeforeStartListener(callable $listener)
    {
        $this->beforeStartListeners[] = $listener;
    }

}
