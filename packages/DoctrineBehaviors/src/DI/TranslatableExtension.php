<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\DI;

use Kdyby\Events\DI\EventsExtension;
use Knp\DoctrineBehaviors\Model\Translatable\Translation;
use Knp\DoctrineBehaviors\ORM\Translatable\TranslatableSubscriber;
use Nette\DI\ServiceDefinition;
use Nette\Utils\Validators;
use Symplify\DoctrineBehaviors\Entities\Attributes\TranslatableTrait;

final class TranslatableExtension extends AbstractBehaviorExtension
{
    /**
     * @var mixed[]
     */
    private $defaults = [
        'currentLocaleCallable' => null,
        'defaultLocaleCallable' => null,
        'translatableTrait' => TranslatableTrait::class,
        'translationTrait' => Translation::class,
        'translatableFetchMode' => 'LAZY',
        'translationFetchMode' => 'LAZY',
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
        Validators::assertField($config, 'currentLocaleCallable', 'null|array');
        Validators::assertField($config, 'translatableTrait', 'type');
        Validators::assertField($config, 'translationTrait', 'type');
        Validators::assertField($config, 'translatableFetchMode', 'string');
        Validators::assertField($config, 'translationFetchMode', 'string');
    }

    /**
     * @param mixed[] $config
     */
    private function createListenerServiceDefinition(array $config): ServiceDefinition
    {
        $listenerServiceDefinition = new ServiceDefinition();
        $listenerServiceDefinition->setClass(TranslatableSubscriber::class);
        $listenerServiceDefinition->setArguments([
            '@' . $this->getClassAnalyzer()->getClass(),
            $config['currentLocaleCallable'],
            $config['defaultLocaleCallable'],
            $config['translatableTrait'],
            $config['translationTrait'],
            $config['translatableFetchMode'],
            $config['translationFetchMode']
        ]);
        $listenerServiceDefinition->setAutowired(false);
        $listenerServiceDefinition->addTag(EventsExtension::TAG_SUBSCRIBER);

        return $listenerServiceDefinition;
    }
}
