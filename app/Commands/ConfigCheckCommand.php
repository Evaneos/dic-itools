<?php

namespace DICIT\Tools\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DICIT\Config\YML;
use DICIT\Tools\Validation\Validator;
use DICIT\Tools\Validation\DependencyValidator;
use DICIT\Tools\Validation\ConstructorArgumentsValidator;
use DICIT\Tools\Validation\EmptyNodeValidator;
use Psr\Log\LogLevel;
use DICIT\Tools\Validation\CyclicDependencyValidator;

class ConfigCheckCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('config:check')
            ->setDescription('Validate a DIC-IT configuration file')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'The main DIC-IT configuration file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        $output->writeln(PHP_EOL . 'Parsing YAML configuration file : ' . $file . PHP_EOL);

        $logger = new CommandLogger($output);
        $logger->enableFiltering();
        $logger->addLevel(LogLevel::ERROR);
        $logger->addLevel(LogLevel::WARNING);
        $logger->addLevel(LogLevel::INFO);

        $config = new YML($file);
        $validator = new Validator($config, $logger);
        $validator->add(new EmptyNodeValidator());
        $validator->add(new DependencyValidator());
        $validator->add(new ConstructorArgumentsValidator());
        $validator->add(new CyclicDependencyValidator());
        $validator->validate();

        $output->writeln(PHP_EOL . 'Done.');
    }
}