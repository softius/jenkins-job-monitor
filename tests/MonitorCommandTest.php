<?php

namespace Softius\JenkinsJobMonitor;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MonitorCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicUsage()
    {
        // Create a mock for Guzzle Client
        $mock = new MockHandler([new Response(200)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        // Create the command and
        $command = new MonitorCommand();
        $command->setClient($client);
        $command->setApplication(new Application());
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'command' => 'monitor',
            'url' => 'http://test.com',
            'job' => 'test',
            'monitor' => 'echo 1'
            ]);
        $this->assertEquals(0, $exitCode);
    }
}
