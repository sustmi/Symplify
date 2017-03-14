<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\DI;

use Kdyby\Events\DI\EventsExtension;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\ORM\Timestampable\TimestampableSubscriber;
use Nette\DI\ServiceDefinition;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;

final class TimestampableExtension extends AbstractBehaviorExtension
{
    /**
     * @var mixed[]
     */
    private $defaults = [
        'isRecursive' => true,
        'trait' => Timestampable::class,
        'dbFieldType' => 'datetime',
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
     * @throws AssertionException
     */
    private function validateConfigTypes(array $config): void
    {
        Validators::assertField($config, 'isRecursive', 'bool');
        Validators::assertField($config, 'trait', 'type');
        Validators::assertField($config, 'dbFieldType', 'string');
    }

    /**
     * @param mixed[] $config
     */
    private function createListenerServiceDefinition(array $config): ServiceDefinition
    {
        $listenerServiceDefinition = new ServiceDefinition();
        $listenerServiceDefinition->setClass(TimestampableSubscriber::class);
        $listenerServiceDefinition->setArguments([
            '@' . $this->getClassAnalyzer()->getClass(),
            $config['isRecursive'],
            $config['trait'],
            $config['dbFieldType']
        ]);
        $listenerServiceDefinition->setAutowired(false);
        $listenerServiceDefinition->addTag(EventsExtension::TAG_SUBSCRIBER);

        return $listenerServiceDefinition;
    }
}
