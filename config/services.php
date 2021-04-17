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
        ->lazy()
        ->call('setCommandLoader', [service('console.command_loader')])
        ->public();

    $services
        ->load('SoureCode\\ConventionalCommits\\Commands\\', '../src/Commands/*')
        ->lazy()
        ->tag('console.command');

    $services
        ->set('configuration.locator', FileLocator::class)
        ->lazy()
        ->args(
            [
                param('kernel.working_directory'),
            ]
        );

    $services
        ->set('configuration.loader.json', JsonFileLoader::class)
        ->tag('configuration.loader')
        ->lazy()
        ->args(
            [
                service('configuration.locator'),
            ]
        );

    $services
        ->set('configuration.loader.resolver', LoaderResolver::class)
        ->lazy()
        ->call('addLoader', [service('configuration.loader.json')]);

    $services
        ->set('configuration.loader.delegating', DelegatingLoader::class)
        ->lazy()
        ->args(
            [
                service('configuration.loader.resolver'),
            ]
        );

    $services
        ->set('configuration.loader', ConfigurationLoader::class)
        ->lazy()
        ->args(
            [
                service('configuration.locator'),
                service('configuration.loader.delegating'),
            ]
        )
        ->alias(ConfigurationLoader::class, 'configuration.loader');

    $services
        ->set('validation.validator', ValidatorInterface::class)
        ->lazy()
        ->factory([Validation::class, 'createValidator']);

    $services
        ->set('validation.commit.validator', Validator::class)
        ->lazy()
        ->args(
            [
                service('validation.validator'),
                service('configuration.loader'),
            ]
        )
        ->alias(Validator::class, 'validation.commit.validator');

    $services
        ->set(GitWrapper::class)
        ->synthetic()
        ->lazy();

    $services->set(GitCommitRanges::class)
        ->lazy()
        ->args(
            [
                service(GitWrapper::class),
                param('kernel.working_directory'),
            ]
        );

};
