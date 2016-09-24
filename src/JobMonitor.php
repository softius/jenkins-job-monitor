<?php

namespace Softius\JenkinsJobMonitor;

use GuzzleHttp\Psr7\Request;

/**
 * Class JobMonitor
 * @package Softius\JenkinsJobMonitor
 */
class JobMonitor
{
    /**
     * Job name as configured in Jenkins
     * @var string
     */
    private $jobName;

    /**
     * The name to be displayed rather than the build number
     * @var string
     */
    private $displayName;

    /**
     * Long description of the build
     * @var string
     */
    private $description;

    /**
     * When the job was started running, in milliseconds
     * @var integer
     */
    private $startedOn;

    /**
     * Returns total duration of job execution, in milliseconds
     * @var int
     */
    private $duration;

    /**
     * Integer indicating the error code. 0 is success and everything else is failure
     * @var integer
     */
    private $exitCode;

    /**
     * Build log
     * @var string
     */
    private $log;

    /**
     * JobMonitor constructor.
     * @param string $jobName
     * @param string $displayName
     * @param string $description
     */
    public function __construct($jobName, $displayName = null, $description = null)
    {
        $this->jobName = $jobName;
        $this->displayName = $displayName;
        $this->description = $description;

        $this->exitCode = $this->startedOn = $this->duration = null;
        $this->clearLog();
    }

    /**
     * Starts job monitoring
     */
    public function start()
    {
        $this->startedOn = round(microtime(true) * 1000);
        $this->duration = null;
    }

    /**
     * Stops job monitoring
     * @param string $exitCode
     * @param string $log if log is provided, existing log data will be overwritten
     * @see setLog
     * @see appendLog
     */
    public function stop($exitCode, $log = null)
    {
        $completedOn = round(microtime(true) * 1000);
        $this->setDuration(($completedOn - $this->startedOn));
        $this->setExitCode($exitCode);
        if ($log) {
            $this->setLog($log);
        }
    }

    /**
     * @param int $exitCode
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;
    }

    /**
     * Returns exit code
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Returns true only and only this job is completed
     * @return boolean
     */
    public function isCompleted()
    {
        return $this->duration !== null;
    }

    /**
     * Returns true only and only this job is completed and successfull
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->isCompleted() && $this->exitCode == 0;
    }

    /**
     * @param integer $duration Duration in milliseconds
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * Returns total duration of job execution, in milliseconds
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * Resets logs to empty string
     */
    public function clearLog()
    {
        $this->log = null;
    }

    /**
     * Appends provided log information to existing log
     * @param $log
     */
    public function appendLog($log)
    {
        $this->log = $this->log.$log;
    }

    /**
     * Returns the log of this job
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @return string
     */
    private function getRequestBody()
    {
        $xmlTemplate = '<run><log encoding="hexBinary">%s</log><result>%d</result>%s</run>';
        $encodedLog = current(unpack('H*', $this->log));
        $xmlElements = null;
        if ($this->isCompleted()) {
            $xmlElements .= sprintf('<duration>%d</duration>', $this->getDuration());
        }

        if ($this->displayName !== null) {
            $xmlElements .= sprintf('<displayName>%s</displayName>', $this->displayName);
        }

        if ($this->description !== null) {
            $xmlElements .= sprintf('<description>%s</description>', $this->description);
        }

        $xmlRequest = sprintf($xmlTemplate, $encodedLog, $this->exitCode, $xmlElements);
        return $xmlRequest;
    }

    /**
     * Constructs and returns the URI, where the monitor data must sent
     *
     * @return string
     */
    private function getRequestUri()
    {
        return sprintf('/job/%s/postBuildResult', $this->jobName);
    }

    /**
     * Constructs and returns the Monitor request to be to Jenkins
     *
     * @return Psr\Http\Message\RequestInterface
     */
    public function getRequest()
    {
        return new Request('POST', $this->getRequestUri(), [], $this->getRequestBody());
    }
}
