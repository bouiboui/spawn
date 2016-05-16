<?php

namespace bouiboui\Spawn;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class SpawnApplication extends Application
{
    public function __construct($version = null)
    {
        parent::__construct('Spawn', $version);
    }

    /**
     * Gets the name of the command based on input.
     *
     * @param InputInterface $input The input interface
     *
     * @return string The command name
     */
    protected function getCommandName(InputInterface $input)
    {
        // This should return the name of your command.
        return 'run';
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new RunCommand();

        return $defaultCommands;
    }

    /**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}