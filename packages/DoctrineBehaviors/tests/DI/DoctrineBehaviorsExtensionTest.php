<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\Tests\DI;

use Doctrine\Common\EventManager;
use Knp\DoctrineBehaviors\ORM\Loggable\LoggableSubscriber;
use Knp\DoctrineBehaviors\ORM\Sluggable\SluggableSubscriber;
use Knp\DoctrineBehaviors\ORM\SoftDeletable\SoftDeletableSubscriber;
use Knp\DoctrineBehaviors\ORM\Timestampable\TimestampableSubscriber;
use Knp\DoctrineBehaviors\ORM\Translatable\TranslatableSubscriber;
use Knp\DoctrineBehaviors\ORM\Tree\TreeSubscriber;
use PHPUnit\Framework\TestCase;
use Symplify\DoctrineBehaviors\Tests\ContainerFactory;

final class DoctrineBehaviorsExtensionTest extends TestCase
{
    /**
     * @var int
     */
    private const LISTENER_COUNT = 13;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var string[]
     */
    private $listeners = [
        LoggableSubscriber::class,
        SluggableSubscriber::class,
        SoftDeletableSubscriber::class,
        TimestampableSubscriber::class,
        TranslatableSubscriber::class,
        TreeSubscriber::class,
    ];

    protected function setUp()
    {
        $container = (new ContainerFactory)->create();

        $this->eventManager = $container->getByType(EventManager::class);
    }


    public function testExtensions(): void
    {
        $count = 0;

        foreach ($this->eventManager->getListeners() as $listenerSet) {
            foreach ($listenerSet as $listener) {
                $this->assertContains(get_class($listener), $this->listeners);
                ++$count;
            }
        }

        $this->assertSame(self::LISTENER_COUNT, $count);
    }
}
