<?php

namespace BitWeb\DoctrineExtensionModule;

use BitWeb\DoctrineExtension\File;
use BitWeb\DoctrineExtension\Listener\FileListener;
use BitWeb\DoctrineExtension\Type\FileType;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\Types\Type;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $this->initializeBitWebDoctrineExtensions($e);
    }

    public function initializeBitWebDoctrineExtensions(MvcEvent $e)
    {
        $locator = $e->getApplication()->getServiceManager();

        Type::addType('file', FileType::class);

        File::setDefaultBasePath(dirname($_SERVER['SCRIPT_FILENAME']) . '/files');
        File::setDefaultUploadBasePath(File::getDefaultBasePath());

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $locator->get('doctrine.entitymanager.orm_default');

        new FileListener($em->getEventManager());

        AnnotationRegistry::registerFile(__DIR__ . '/../../../../doctrine-extension/src/Mapping/File.php');
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }
}
