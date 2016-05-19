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
     */
    public function addProcessesFromCommand($command, $find = null)
    {
        if (count($command) > 0) {
            $processesCount = count($this->processes);
            $this->addDirectories($command, $find);
            $this->addRanges($command);
            if ($processesCount === count($this->processes)) {
                $this->addProcess($command);
            }
        }
    }

    /**
     * Adds a process for each file in a directory
     * @param array $command
     * @param null $find
     */
    public function addDirectories($command, $find = null)
    {
        $baseCommand = $command;
        foreach ($command as $directoryIndex => $directory) {
            if (file_exists($directory) && is_dir($directory)) {
                foreach (glob(rtrim($directory, '/') . '/' . ($find ?: '*.*')) as $filePath) {
                    if (is_file($filePath)) {
                        $baseCommand[$directoryIndex] = $filePath;
                        $this->addProcess($baseCommand);
                    }
                }
            }
        }
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
     * Adds processes when arguments are provided
     * Detects ranges in the arguments and spawns adequate number of processes
     * @param array $command
     * @internal param string $arguments
     */
    public function addRanges($command)
    {
        $rangeProcesses = [implode('#', $command)];
        $this->recursiveParseRange($rangeProcesses);
        $rangeProcesses = array_keys(array_flip($rangeProcesses));
        if (count($rangeProcesses) > 1) {
            foreach ($rangeProcesses as $process) {
                $this->addProcess(explode('#', $process));
            }
        }
    }

    public function recursiveParseRange(&$res)
    {
        $pattern = '/\{([^\}]+?)\}/';
        foreach ($res as $resNum => $command) {
            preg_match($pattern, $command, $ranges);
            if (array_key_exists(1, $ranges) && count($ranges[1]) > 0) {
                list($r0, $r1) = explode('-', $ranges[1]);
                foreach (range($r0, $r1) as $n) {
                    $res[] = str_replace($ranges[0], $n, $command);
                }
                unset($res[$resNum]);
                $this->recursiveParseRange($res);
            }
        }
        return $res;
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
