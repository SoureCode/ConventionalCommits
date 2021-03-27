<?php

namespace SoureCode\ConventionalCommits\Configuration;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderInterface;

class ConfigurationLoader
{
    private FileLocatorInterface $fileLocator;

    private LoaderInterface $loader;

    private ?array $configuration = null;

    public function __construct(FileLocatorInterface $fileLocator, LoaderInterface $loader)
    {
        $this->fileLocator = $fileLocator;
        $this->loader = $loader;
    }

    public function load(): array
    {
        if (null === $this->configuration) {
            $configurationFiles = (array) $this->fileLocator->locate('conventional-commits.json');

            $configurations = array_map(
                function ($configurationFile) {
                    return $this->loader->load($configurationFile);
                },
                $configurationFiles
            );

            $processor = new Processor();
            $configuration = new Configuration();

            $processedConfiguration = $processor->processConfiguration($configuration, $configurations);

            $this->configuration = $processedConfiguration;
        }

        return $this->configuration;
    }
}
