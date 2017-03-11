<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Adapter\Nette\Console;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symplify\ModularDoctrineFilters\Tests\Adapter\Nette\ContainerFactory;

final class ConsoleTest extends TestCase
{
    /**
     * @var Application
     */
    private $consoleApplication;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected function setUp(): void
    {
        $container = (new ContainerFactory)->create();
        $this->consoleApplication = $container->getByType(Application::class);
        $this->consoleApplication->setAutoExit(false);
        $this->entityManager = $container->getByType(EntityManagerInterface::class);
    }

    public function testEnablingOnlyOnce(): void
    {
        $filterCollection = $this->entityManager->getFilters();
        $this->assertCount(0, $filterCollection->getEnabledFilters());

        $stringInput = new StringInput('help');
        $this->consoleApplication->run($stringInput, new NullOutput);

        $this->assertCount(2, $filterCollection->getEnabledFilters());

        $this->consoleApplication->run($stringInput, new NullOutput);

        $this->assertCount(2, $filterCollection->getEnabledFilters());
    }
}
