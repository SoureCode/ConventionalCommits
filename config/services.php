<?php

use SoureCode\ConventionalCommits\Application;
use SoureCode\ConventionalCommits\Configuration\ConfigurationLoader;
use SoureCode\ConventionalCommits\FileLoader\JsonFileLoader;
use SoureCode\ConventionalCommits\Git\GitCommitRanges;
use SoureCode\ConventionalCommits\Validator\Validator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symplify\GitWrapper\GitWrapper;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->defaults()
        ->autowire(true)
        ->autoconfigure(true);

    $services
        ->set(Application::class)
        ->call('setCommandLoader', [service('console.command_loader')])
        ->public();

    $services
        ->load('SoureCode\\ConventionalCommits\\Commands\\', '../src/Commands/*')
        ->tag('console.command');

    $services
        ->set('configuration.locator', FileLocator::class)
        ->args(
            [
                param('kernel.working_directory'),
            ]
        );

    $services
        ->set('configuration.loader.json', JsonFileLoader::class)
        ->tag('configuration.loader')
        ->args(
            [
                service('configuration.locator'),
            ]
        );

    $services
        ->set('configuration.loader.resolver', LoaderResolver::class)
        ->call('addLoader', [service('configuration.loader.json')]);

    $services
        ->set('configuration.loader.delegating', DelegatingLoader::class)
        ->args(
            [
                service('configuration.loader.resolver'),
            ]
        );

    $services
        ->set('configuration.loader', ConfigurationLoader::class)
        ->args(
            [
                service('configuration.locator'),
                service('configuration.loader.delegating'),
            ]
        )
        ->alias(ConfigurationLoader::class, 'configuration.loader');

    $services
        ->set('validation.validator', ValidatorInterface::class)
        ->factory([Validation::class, 'createValidator']);

    $services
        ->set('validation.commit.validator', Validator::class)
        ->args(
            [
                service('validation.validator'),
                service('configuration.loader'),
            ]
        )
        ->alias(Validator::class, 'validation.commit.validator');

    $services
        ->set(GitWrapper::class);

    $services->set(GitCommitRanges::class)
        ->args(
            [
                service(GitWrapper::class),
                param('kernel.working_directory'),
            ]
        );

};
