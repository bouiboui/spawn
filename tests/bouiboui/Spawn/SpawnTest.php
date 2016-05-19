<?php

namespace bouiboui\Spawn;


class SpawnTest extends \PHPUnit_Framework_TestCase
{
    public function testAddProcessFromDirectory()
    {
        $this->assertCommandCount('test ' . dirname(dirname(__DIR__)) . '/data', 3);
    }

    private function assertCommandCount($command, $count)
    {
        $spawn = new Spawn();
        $spawn->addProcessesFromCommand(explode(' ', $command));
        static::assertEquals($count, $spawn->getProcessesCount());
    }

    public function testAddProcessWithSingleRange()
    {
        $this->assertCommandCount('test arg{0-0}', 1);
    }

    public function testAddProcessWithMultipleRange()
    {
        $this->assertCommandCount('test arg{1-10}', 10);
    }

    public function testAddProcessWithoutRange()
    {
        $this->assertCommandCount('test arg1', 1);
    }

    public function testAddSingleProcess()
    {
        $this->assertCommandCount('test', 1);
    }
}
