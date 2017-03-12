<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Nette\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionFinder;

final class SymfonyEventDispatcherExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        if ($this->isKdybyEventsRegistered()) {
            return;
        }

        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../../../config/services.neon')['services']
        );
    }

    public function beforeCompile(): void
    {
        $eventDispatcher = DefinitionFinder::getByType($this->getContainerBuilder(), EventDispatcherInterface::class);

        if ($this->isKdybyEventsRegistered()) {
            $eventDispatcher->setClass(EventDispatcher::class);
            $eventDispatcher->setFactory(null);
        }

        $this->addSubscribersToEventDispatcher();
        $this->bindEventDispatcherToSymfonyConsole();
        $this->bindNetteEvents();
    }

    private function isKdybyEventsRegistered(): bool
    {
        return (bool) $this->compiler->getExtensions('Kdyby\Events\DI\EventsExtension');
    }

    private function addSubscribersToEventDispatcher(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            EventDispatcherInterface::class,
            EventSubscriberInterface::class,
            'addSubscriber'
        );
    }

    private function bindNetteEvents(): void
    {
        $containerBuilder = $this->getContainerBuilder();

        $netteEventList = (new NetteEventListFactory)->create();
        foreach ($netteEventList as $netteEvent) {
            if (! $serviceDefinitions = $containerBuilder->findByType($netteEvent->getClass())) {
                return;
            }

            foreach ($serviceDefinitions as $serviceDefinition) {
                $this->decorateServiceDefinitionWithNetteEvent($serviceDefinition, $netteEvent);
            }
        }
    }

    private function decorateServiceDefinitionWithNetteEvent(
        ServiceDefinition $serviceDefinition,
        NetteEventItem $netteEvent
    ): void {
        $propertyStatement = new Statement('function () {
			$class = ?;
			$event = new $class(...func_get_args());
			?->dispatch(?, $event);
		}', [
            $netteEvent->getEventClass(),
            '@' . EventDispatcherInterface::class,
            $netteEvent->getEventName()
        ]);

        $serviceDefinition->addSetup('$service->?[] = ?;', [$netteEvent->getProperty(), $propertyStatement]);
    }

    private function bindEventDispatcherToSymfonyConsole(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        if ($consoleApplicationName = $containerBuilder->getByType('Symfony\Component\Console\Application')) {
            $containerBuilder->getDefinition($consoleApplicationName)
                ->addSetup('setDispatcher');
        }
    }
}
