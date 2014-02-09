<?php

namespace DICIT\Tools\Validation;

use DICIT\Config\AbstractConfig;
use DICIT\ArrayResolver;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Validator
{

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var \DICIT\Config\AbstractConfig
     */
    private $config;

    /**
     *
     * @var \DICIT\Tools\Validation\ConfigValidator[]
     */
    private $validators = array();

    private $errors = array();

    public function __construct(AbstractConfig $config, LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger ?: new NullLogger();
    }

    public function add(ConfigValidator $validator)
    {
        $this->validators[] = $validator;
    }

    public function addError($error)
    {
        $this->logger->error(' - ' . $error);
    }

    public function addWarning($warning)
    {
        $this->logger->warning(' - ' . $warning);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function validate()
    {
        $resolver = new ArrayResolver($this->config->load());
        $services = $resolver->resolve('classes', null);

        foreach ($services as $serviceName => $serviceConfig) {
            $this->logger->info('');
            $this->logger->info('Validating service configuration : ' . $serviceName);
            $this->logger->info('');

            foreach ($this->validators as $validator) {
                $this->logger->debug('Running : ' . get_class($validator));
                $validator->validateService($this, $resolver, $serviceName, $serviceConfig);
            }
        }
    }
}
