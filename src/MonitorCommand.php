<?php

namespace Softius\JenkinsJobMonitor;

use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class MonitorCommand
 * @package Softius\JenkinsJobMonitor
 */
class MonitorCommand extends Command
{
    /**
     * @var GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * Configures the monitor command.
     */
    protected function configure()
    {
        $this
            ->setName('monitor')
            ->setDescription('Monitors the non-interactive execution of processes')
            ->addArgument('url', InputArgument::REQUIRED, 'Jenkins home URL')
            ->addArgument('job', InputArgument::REQUIRED, 'Job name as configured in Jenkins')
            ->addArgument('monitor', InputArgument::OPTIONAL, 'Command to be monitored')
            ->addOption('display', null, InputOption::VALUE_OPTIONAL, '')
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, '');
    }

    /**
     * Executes the monitor command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $command = $this->getMonitorArgument($input);

            $monitor = new JobMonitor(
                $input->getArgument('job'),
                $input->getOption('display'),
                $input->getOption('description')
            );

            $monitor->start();
            $process = new Process($command);
            $process->run();
            $monitor->stop($process->getExitCode(), $process->getOutput());

            $this->getClient()->send($monitor->getRequest(), ['base_uri' => $input->getArgument('url')]);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return 1;
        }

        return 0;
    }

    /**
     * Returns the command to monitor.
     *
     * If monitor argument is not provided, STDIN is being used instead.
     *
     * @param InputInterface $input
     * @throws \RuntimeException
     * @return string
     */
    private function getMonitorArgument(InputInterface $input)
    {
        $monitor = $input->getArgument('monitor');
        if (!$monitor && 0 === ftell(STDIN)) {
            $monitor = '';
            while (!feof(STDIN)) {
                $monitor .= fread(STDIN, 1024);
            }
        }

        if (!$monitor) {
            throw new \RuntimeException('Please provide a command or pipe content to STDIN.');
        }

        return $monitor;
    }

    /**
     * @param GuzzleHttp\ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
