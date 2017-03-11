<?php declare(strict_types=1);

namespace Symplify\DoctrineExtensionsTree\DI;

use Gedmo\Tree\TreeListener;
use Kdyby\Events\DI\EventsExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;

final class TreeExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->addDefinition($this->prefix('listener'), $this->createTreeListenerServiceDefinition());
    }

    private function createTreeListenerServiceDefinition(): ServiceDefinition
    {
        $serviceDefinition = new ServiceDefinition();
        $serviceDefinition->setClass(TreeListener::class);
        $serviceDefinition->addSetup('setAnnotationReader', ['@Doctrine\Common\Annotations\Reader']);
        $serviceDefinition->addTag(EventsExtension::TAG_SUBSCRIBER);

        return $serviceDefinition;
    }
}
