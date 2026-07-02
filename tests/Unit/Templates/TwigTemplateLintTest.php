<?php

declare(strict_types=1);

namespace Depa\SuluBlockSwiperBundle\Tests\Unit\Templates;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Source;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigTemplateLintTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function templateProvider(): iterable
    {
        $files = glob(self::viewsPath() . '/includes/blocks/*.html.twig');
        self::assertNotFalse($files);
        self::assertNotEmpty($files);

        foreach ($files as $file) {
            yield basename($file) => [$file];
        }
    }

    #[DataProvider('templateProvider')]
    public function testTemplateHasValidSyntax(string $path): void
    {
        $twig = new Environment(new FilesystemLoader(self::viewsPath()));
        $twig->addFunction(new TwigFunction('sulu_block_preview', static fn (): string => ''));
        $twig->addFunction(new TwigFunction('sulu_resolve_media', static fn (): mixed => null));
        $twig->addFilter(new TwigFilter('trans', static fn (string $key): string => $key));

        $code = file_get_contents($path);
        self::assertNotFalse($code);

        $twig->parse($twig->tokenize(new Source($code, basename($path), $path)));

        $this->addToAssertionCount(1);
    }

    private static function viewsPath(): string
    {
        return \dirname(__DIR__, 3) . '/Resources/views';
    }
}
