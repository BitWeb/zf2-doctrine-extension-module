<?php

namespace BitWebDoctrineExtensionModule;

use BitWeb\DoctrineExtension\File;
use BitWeb\DoctrineExtension\Filter\SoftDeleteFilter;
use BitWeb\DoctrineExtension\Listener\FileListener;
use BitWeb\DoctrineExtension\Listener\IpListener;
use BitWeb\DoctrineExtension\Listener\SoftDeletableListener;
use BitWeb\DoctrineExtension\Listener\UserAgentListener;
use BitWeb\DoctrineExtension\Type\FileType;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
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

        /* @var $em EntityManager */
        $em = $locator->get(EntityManager::class);
        $em->getConfiguration()->addFilter('SoftDelete', SoftDeleteFilter::class);
        $em->getFilters()->enable('SoftDelete');

        new FileListener($em->getEventManager());
        new IpListener($em->getEventManager());
        new SoftDeletableListener($em->getEventManager());
        new UserAgentListener($em->getEventManager());

        $annotationBaseDir = __DIR__ . '/../../../doctrine-extension/src/Mapping/';
        AnnotationRegistry::registerFile($annotationBaseDir . 'File.php');
        AnnotationRegistry::registerFile($annotationBaseDir . 'Ip.php');
        AnnotationRegistry::registerFile($annotationBaseDir . 'SoftDeletable.php');
        AnnotationRegistry::registerFile($annotationBaseDir . 'UserAgent.php');
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
