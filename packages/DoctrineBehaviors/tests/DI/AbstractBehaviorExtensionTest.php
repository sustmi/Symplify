<?php declare(strict_types=1);

namespace Symplify\DoctrineBehaviors\Tests\DI;

use Knp\DoctrineBehaviors\ORM\Loggable\LoggerCallable;
use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;
use Nette\DI\Compiler;
use PHPUnit\Framework\TestCase;
use Symplify\DoctrineBehaviors\DI\AbstractBehaviorExtension;
use Symplify\DoctrineBehaviors\Tests\DI\AbstractBehaviorExtensionSource\SomeBehaviorExtension;

final class AbstractBehaviorExtensionTest extends TestCase
{
    /**
     * @var AbstractBehaviorExtension|SomeBehaviorExtension
     */
    private $abstractBehaviorsExtension;

    protected function setUp(): void
    {
        $this->abstractBehaviorsExtension = new SomeBehaviorExtension;
        $this->abstractBehaviorsExtension->setCompiler(new Compiler, 'someBehavior');
    }

    public function testGetClassAnalyzer(): void
    {
        $classAnalyzer = $this->abstractBehaviorsExtension->getClassAnalyzerPublic();
        $this->assertSame(ClassAnalyzer::class, $classAnalyzer->getClass());

        $sameClassAnalyzer = $this->abstractBehaviorsExtension->getClassAnalyzerPublic();
        $this->assertSame($classAnalyzer, $sameClassAnalyzer);
    }

    public function testBuildDefinition(): void
    {
        $definition = $this->abstractBehaviorsExtension->buildDefinitionFromCallablePublic(LoggerCallable::class);

        $this->assertSame(LoggerCallable::class, $definition->getClass());
        $this->assertSame(LoggerCallable::class, $definition->getEntity());
    }
}
