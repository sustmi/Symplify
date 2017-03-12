<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application\Command;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Sniffs\Classes\ClassDeclarationSniff;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;

final class RunCommandTest extends TestCase
{
    public function testConfiguration(): void
    {
        $runCommand = $this->createRunCommandWithConfiguration([]);
        $this->assertEmpty($runCommand->getSkipped());
        $this->assertEmpty($runCommand->getConfiguration());
        $this->assertEmpty($runCommand->getSniffs());
        $this->assertSame([__DIR__], $runCommand->getSources());
        $this->assertFalse($runCommand->isFixer());
        $this->assertFalse($runCommand->shouldClearCache());
    }

    public function testSniffs(): void
    {
        $runCommand = $this->createRunCommandWithConfiguration([
            RunCommand::OPTION_CHECKERS => [
                ClassDeclarationSniff::class,
                DeclareStrictTypesFixer::class
            ]
        ]);

        $this->assertCount(1, $runCommand->getSniffs());
        $this->assertSame([ClassDeclarationSniff::class => []], $runCommand->getSniffs());
    }

    public function testFixers(): void
    {
        $runCommand = $this->createRunCommandWithConfiguration([
            RunCommand::OPTION_CHECKERS => [
                ClassDeclarationSniff::class,
                DeclareStrictTypesFixer::class
            ]
        ]);

        $this->assertCount(1, $runCommand->getFixers());
        $this->assertSame([DeclareStrictTypesFixer::class => []], $runCommand->getFixers());
    }

    private function createRunCommandWithConfiguration(array $configuration = []): RunCommand
    {
        return RunCommand::createFromSourceFixerAndData(
            [__DIR__],
            false,
            false,
            $configuration
        );
    }
}
