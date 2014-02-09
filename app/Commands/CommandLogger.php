<?php

namespace DICIT\Tools\Commands;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandLogger implements LoggerInterface
{


    private $filtering = false;

    private $enabled = array();

    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function enableFiltering()
    {
        $this->filtering = true;
    }

    public function addLevel($level)
    {
        $this->enabled[] = $level;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->log(\Psr\Log\LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function alert($message, array $context = array())
    {
        $this->log(\Psr\Log\LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function critical($message, array $context = array())
    {
        $this->log(\Psr\Log\LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function error($message, array $context = array())
    {
        $this->log(\Psr\Log\LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function warning($message, array $context = array())
    {
        $this->log(\Psr\Log\LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function notice($message, array $context = array())
    {
        $this->log(\Psr\Log\LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function info($message, array $context = array())
    {
        $this->log(\Psr\Log\LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
    */
    public function debug($message, array $context = array())
    {
        $this->log(\Psr\Log\LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
    */
    public function log($level, $message, array $context = array())
    {
        if ($this->filtering && ! in_array($level, $this->enabled)) {
            return;
        }

        if ($level == \Psr\Log\LogLevel::ERROR) {
            $this->output->writeln(sprintf('<error>%s</error>', $message));
        }
        elseif ($level == \Psr\Log\LogLevel::WARNING) {
            $this->output->writeln(sprintf('<comment>%s</comment>', $message));
        }
        elseif ($level == \Psr\Log\LogLevel::INFO) {
            $this->output->writeln(sprintf('<fg=green>%s</fg=green>', $message));
        }
        else {
            $this->output->writeln($message);
        }
    }
}
