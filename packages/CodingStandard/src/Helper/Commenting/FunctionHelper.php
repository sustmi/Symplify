<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Helper\Commenting;

use PHP_CodeSniffer\Files\File;

/**
 * Inspired by https://github.com/slevomat/coding-standard/blob/4f81f58625bf86bd91f7fc6f8e4d12160bf03c7c/SlevomatCodingStandard/Helpers/FunctionHelper.php.
 */
final class FunctionHelper
{
    public static function isAbstract(File $codeSnifferFile, int $functionPointer): bool
    {
        return ! isset($codeSnifferFile->getTokens()[$functionPointer]['scope_opener']);
    }

    /**
     * @return string
     */
    public static function findReturnTypeHint(File $codeSnifferFile, int $functionPointer)
    {
        $tokens = $codeSnifferFile->getTokens();
        $isAbstract = self::isAbstract($codeSnifferFile, $functionPointer);
        $colonToken = $isAbstract
            ? $codeSnifferFile->findNext(
                [T_COLON, T_INLINE_ELSE],
                $tokens[$functionPointer]['parenthesis_closer'] + 1,
                null,
                false,
                null,
                true
            )
            : $codeSnifferFile->findNext(
                [T_COLON, T_INLINE_ELSE],
                $tokens[$functionPointer]['parenthesis_closer'] + 1,
                $tokens[$functionPointer]['scope_opener'] - 1
            );
        if ($colonToken === false) {
            return '';
        }

        $returnTypeHint = '';
        $nextToken = $colonToken;
        do {
            $nextToken = $isAbstract
                ? $codeSnifferFile->findNext(
                    [T_WHITESPACE, T_COMMENT, T_SEMICOLON],
                    $nextToken + 1,
                    null,
                    true,
                    null,
                    true
                )
                : $codeSnifferFile->findNext(
                    [T_WHITESPACE, T_COMMENT],
                    $nextToken + 1,
                    $tokens[$functionPointer]['scope_opener'] - 1,
                    true
                );
            $isTypeHint = $nextToken !== false;
            if ($isTypeHint) {
                $returnTypeHint .= $tokens[$nextToken]['content'];
            }
        } while ($isTypeHint);

        return $returnTypeHint;
    }
}
