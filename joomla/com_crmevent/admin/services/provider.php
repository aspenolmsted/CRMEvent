<?php

defined('_JEXEC') or die;

use Alliance\Component\Crmevent\Administrator\Extension\CrmeventComponent;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('\\Alliance\\Component\\Crmevent'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Alliance\\Component\\Crmevent'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new CrmeventComponent(
                    $container->get(ComponentDispatcherFactoryInterface::class)
                );
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));

                return $component;
            }
        );
    }
};
