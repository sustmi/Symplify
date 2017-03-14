<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\DI;

use Kdyby\Events\DI\EventsExtension;
use Knp\DoctrineBehaviors\Model\Tree\Node;
use Knp\DoctrineBehaviors\ORM\Tree\TreeSubscriber;
use Nette\DI\ServiceDefinition;
use Nette\Utils\Validators;

final class TreeExtension extends AbstractBehaviorExtension
{
    /**
     * @var mixed[]
     */
    private $defaults = [
        'isRecursive' => true,
        'nodeTrait' => Node::class
    ];

    public function loadConfiguration(): void
    {
        $config = $this->validateConfig($this->defaults);
        $this->validateConfigTypes($config);

        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->addDefinition($this->prefix('listener'), $this->createListenerServiceDefinition($config));
    }

    /**
     * @param mixed[] $config
     */
    private function validateConfigTypes(array $config): void
    {
        Validators::assertField($config, 'isRecursive', 'bool');
        Validators::assertField($config, 'nodeTrait', 'type');
    }

    /**
     * @param mixed[] $config
     */
    private function createListenerServiceDefinition(array $config): ServiceDefinition
    {
        $listenerServiceDefinition = new ServiceDefinition();
        $listenerServiceDefinition->setClass(TreeSubscriber::class);
        $listenerServiceDefinition->setArguments([
            '@' . $this->getClassAnalyzer()->getClass(),
            $config['isRecursive'],
            $config['nodeTrait']
        ]);
        $listenerServiceDefinition->setAutowired(false);
        $listenerServiceDefinition->addTag(EventsExtension::TAG_SUBSCRIBER);

        return $listenerServiceDefinition;
    }
}
