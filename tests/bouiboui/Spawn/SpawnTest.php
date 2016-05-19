<?php

namespace bouiboui\Spawn;


class SpawnTest extends \PHPUnit_Framework_TestCase
{
    public function testAddProcessFromDirectory()
    {
        $spawn = new Spawn();
        $spawn->addProcessesFromDirectory('php tests/test.php', dirname(dirname(__DIR__)). '/data');
        static::assertEquals(3, $spawn->getProcessesCount());
    }

    public function testAddProcessWithSingleRange()
    {
        $spawn = new Spawn();
        $spawn->addProcessesFromCommand('php tests/test.php', 'arg{0-0}');
        static::assertEquals(1, $spawn->getProcessesCount());
    }

    public function testAddProcessWithMultipleRange()
    {
        $spawn = new Spawn();
        $spawn->addProcessesFromCommand('php tests/test.php', 'arg{1-10}');
        static::assertEquals(10, $spawn->getProcessesCount());
    }

    public function testAddProcessWithoutRange()
    {
        $spawn = new Spawn();
        $spawn->addProcessesFromCommand('php tests/test.php', 'arg1');
        static::assertEquals(1, $spawn->getProcessesCount());
    }

    public function testAddSingleProcess()
    {
        $spawn = new Spawn();
        $spawn->addSingleProcess('php tests/test.php');
        static::assertEquals(1, $spawn->getProcessesCount());
    }
}
