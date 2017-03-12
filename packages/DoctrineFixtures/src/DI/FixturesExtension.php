<?php declare(strict_types=1);

namespace Symplify\DoctrineFixtures\DI;

use Faker\Provider\Base;
use Nelmio\Alice\Fixtures\Loader;
use Nelmio\Alice\Fixtures\Parser\Methods\MethodInterface;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class FixturesExtension extends CompilerExtension
{
    /**
     * @var mixed[]
     */
    private $defaults = [
        'locale' => 'cs_CZ',
        'seed' => 1
    ];

    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/services.neon')
        );
    }

    public function beforeCompile(): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $this->loadFakerProvidersToAliceLoader();
        $this->loadParsersToAliceLoader();
    }

    private function loadFakerProvidersToAliceLoader(): void
    {
        $config = $this->validateConfig($this->defaults);

        $containerBuilder = $this->getContainerBuilder();
        $aliceLoaderDefinition = $containerBuilder->getDefinitionByType(Loader::class);
        $aliceLoaderDefinition->setArguments([
                $config['locale'],
                $this->getContainerBuilder()
                    ->findByType(Base::class),
                $config['seed']
            ]);
    }

    private function loadParsersToAliceLoader(): void
    {
        $containerBuilder = $this->getContainerBuilder();

        $aliceLoaderDefinition = $containerBuilder->getDefinitionByType(Loader::class);
        foreach ($containerBuilder->findByType(MethodInterface::class) as $parserDefinition) {
            $aliceLoaderDefinition->addSetup('addParser', ['@' . $parserDefinition->getClass()]);
        }
    }
}
