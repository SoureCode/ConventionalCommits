<?php

use SoureCode\ConventionalCommits\Application;
use SoureCode\ConventionalCommits\Message\Message;
use SoureCode\ConventionalCommits\Configuration\ConfigurationLoader;
use SoureCode\ConventionalCommits\FileLoader\JsonFileLoader;
use SoureCode\ConventionalCommits\Validator\Validator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

    $configurator->parameters()->set('messageClass', Message::class);

    $services
        ->set('app.configuration.locator', FileLocator::class)
        ->args(
            [
                param('kernel.working_directory'),
            ]
        );

    $services
        ->set('app.configuration.loader.json', JsonFileLoader::class)
        ->tag('configuration.loader')
        ->args(
            [
                service('app.configuration.locator'),
            ]
        );

    $services
        ->set('app.configuration.loader.resolver', LoaderResolver::class)
        ->call('addLoader', [service('app.configuration.loader.json')]);

    $services
        ->set('app.configuration.loader.delegating', DelegatingLoader::class)
        ->args(
            [
                service('app.configuration.loader.resolver'),
            ]
        );

    $services
        ->set('app.configuration.loader', ConfigurationLoader::class)
        ->args(
            [
                service('app.configuration.locator'),
                service('app.configuration.loader.delegating'),
            ]
        )
        ->alias(ConfigurationLoader::class, 'app.configuration.loader');

    $services
        ->set('app.validation.validator', ValidatorInterface::class)
        ->factory([Validation::class, 'createValidator']);

    $services
        ->set('app.validation.commit.validator', Validator::class)
        ->args(
            [
                service('app.validation.validator'),
                service('app.configuration.loader'),
            ]
        )
        ->alias(Validator::class, 'app.validation.commit.validator');

};
