<?php

namespace Softius\JenkinsJobMonitor;

class JobMonitorTest extends \PHPUnit_Framework_TestCase
{
    public function testDuration()
    {
        $monitor = new JobMonitor('test-duration');

        $monitor->setDuration(10);
        $this->assertEquals(10, $monitor->getDuration());

        $monitor->start();
        sleep(1);
        $monitor->stop(0);
        $this->assertGreaterThanOrEqual(1000, $monitor->getDuration());
        $this->assertLessThan(1100, $monitor->getDuration());
    }

    public function testLog()
    {
        $monitor = new JobMonitor('test-log');
        $monitor->start();

        $monitor->setLog('test1');
        $this->assertEquals('test1', $monitor->getLog());

        $monitor->appendLog('test2');
        $this->assertEquals('test1test2', $monitor->getLog());

        $monitor->clearLog();
        $this->assertEquals(null, $monitor->getLog());

        $monitor->stop(0, 'test3');
        $this->assertEquals('test3', $monitor->getLog());
    }

    public function testExitCode()
    {
        $monitor = new JobMonitor('test-exit-code');

        $monitor->setExitCode(10);
        $this->assertEquals(10, $monitor->getExitCode());

        $monitor->start();

        $monitor->stop(1);
        $this->assertEquals(1, $monitor->getExitCode());
        $this->assertEquals(true, $monitor->isCompleted());
        $this->assertEquals(false, $monitor->isSuccessful());
    }

    public function testRequest()
    {
        $monitor = new JobMonitor('test-request', 'test-displayName', 'test-description');
        $monitor->start();
        $monitor->stop(-1);
        $monitor->setDuration(0);

        $request = $monitor->getRequest();
        $body = $request->getBody()->getContents();

        $this->assertContains('<result>-1</result>', $body);
        $this->assertContains('<duration>0</duration>', $body);
        $this->assertContains('<displayName>test-displayName</displayName>', $body);
        $this->assertContains('<description>test-description</description>', $body);
    }
}
