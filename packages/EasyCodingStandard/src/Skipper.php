<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Finder\Glob;

final class Skipper
{
    /**
     * @var string[][]
     */
    private $skipped = [];

    /**
     * @param string[][] $skipped
     */
    public function setSkipped(array $skipped): void
    {
        $this->skipped = $skipped;
    }

    /**
     * @param Sniff|FixerInterface $checker
     * @param string $relativeFilePath
     */
    public function shouldSkipCheckerAndFile($checker, string $relativeFilePath): bool
    {
        foreach ($this->skipped as $skippedClass => $skippedFiles) {
            if (! is_a($checker, $skippedClass, true)) {
                return false;
            }

            foreach ($skippedFiles as $skippedFile) {
                if ($this->fileMatchesPattern($relativeFilePath, $skippedFile)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function fileMatchesPattern(string $file, string $ignoredPath): bool
    {
        if ((bool) Strings::match($file, Glob::toRegex($ignoredPath))) {
            return true;
        }

        return Strings::endsWith($file, $ignoredPath);
    }
}
