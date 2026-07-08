<?php

declare(strict_types=1);

namespace Depa\SuluBlockSwiperBundle\Tests\Unit\Config;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class BlockConfigConsistencyTest extends TestCase
{
    private const SULU_NS = 'http://schemas.sulu.io/template/template';

    /**
     * Block-Typen, die nicht in diesem Bundle liegen, sondern von einer
     * Abhängigkeit bereitgestellt werden. Neue externe Referenzen müssen
     * hier mit ihrem Herkunftspaket eingetragen werden.
     */
    private const EXTERNAL_TYPES = [
        'block--content-button' => 'depa/sulu-block-content',
        'block--content-button-content' => 'depa/sulu-block-content',
        'block--content-button-grid' => 'depa/sulu-block-content',
        'block--content-headline' => 'depa/sulu-block-content',
        'block--content-html' => 'depa/sulu-block-content',
        'block--content-lead' => 'depa/sulu-block-content',
        'block--content-text' => 'depa/sulu-block-content',
        'block--content-title' => 'depa/sulu-block-content',
        'block--hero-content' => 'depa/sulu-block-hero',
    ];

    /**
     * @return iterable<string, array{string}>
     */
    public static function blockXmlProvider(): iterable
    {
        foreach (self::blockXmlFiles() as $file) {
            yield basename($file) => [$file];
        }
    }

    #[DataProvider('blockXmlProvider')]
    public function testKeyMatchesFilename(string $file): void
    {
        $keys = self::loadXml($file)->getElementsByTagNameNS(self::SULU_NS, 'key');
        self::assertSame(1, $keys->length, sprintf('%s muss genau ein <key>-Element haben', basename($file)));

        $key = $keys->item(0);
        self::assertNotNull($key);
        self::assertSame(
            basename($file, '.xml'),
            trim($key->textContent),
            sprintf('<key> in %s muss dem Dateinamen entsprechen', basename($file))
        );
    }

    #[DataProvider('blockXmlProvider')]
    public function testBlockHasTemplate(string $file): void
    {
        $template = self::basePath() . '/templates/includes/blocks/' . basename($file, '.xml') . '.html.twig';
        self::assertFileExists($template, sprintf('Zu %s fehlt das Twig-Template', basename($file)));
    }

    public function testSlotsYamlEntriesExistAsBlockXml(): void
    {
        $slotsFile = self::basePath() . '/config/blocks/_slots.yaml';
        self::assertFileExists($slotsFile);

        $slots = Yaml::parseFile($slotsFile);
        self::assertIsArray($slots);
        self::assertNotEmpty($slots);

        foreach ($slots as $slot => $blocks) {
            self::assertIsArray($blocks, sprintf("Slot '%s' muss eine Liste von Blöcken sein", (string) $slot));

            foreach ($blocks as $block) {
                self::assertIsString($block);
                self::assertFileExists(
                    self::basePath() . '/config/blocks/' . $block . '.xml',
                    sprintf("Slot '%s' referenziert '%s', aber die Block-XML fehlt", (string) $slot, $block)
                );
            }
        }
    }

    public function testReferencedChildTypesAreResolvable(): void
    {
        $internal = array_map(
            static fn (string $file): string => basename($file, '.xml'),
            self::blockXmlFiles()
        );

        foreach (self::blockXmlFiles() as $file) {
            foreach (self::collectTypeRefs($file) as $ref) {
                self::assertTrue(
                    \in_array($ref, $internal, true) || \array_key_exists($ref, self::EXTERNAL_TYPES),
                    sprintf(
                        "%s referenziert unbekannten Block-Typ '%s' — weder im Bundle vorhanden noch in EXTERNAL_TYPES deklariert",
                        basename($file),
                        $ref
                    )
                );
            }
        }
    }

    #[DataProvider('blockXmlProvider')]
    public function testDefaultTypeIsAmongDeclaredTypes(string $file): void
    {
        foreach (self::loadXml($file)->getElementsByTagNameNS(self::SULU_NS, 'block') as $block) {
            $defaultType = $block->getAttribute('default-type');
            if ('' === $defaultType) {
                continue;
            }

            $declared = [];
            foreach ($block->getElementsByTagNameNS(self::SULU_NS, 'type') as $type) {
                $declared[] = $type->getAttribute('ref');
            }

            self::assertContains(
                $defaultType,
                $declared,
                sprintf(
                    "default-type '%s' in %s ist nicht in den <types> des Blocks '%s' deklariert",
                    $defaultType,
                    basename($file),
                    $block->getAttribute('name')
                )
            );
        }

        $this->addToAssertionCount(1);
    }

    /**
     * @return list<string>
     */
    private static function collectTypeRefs(string $file): array
    {
        $doc = self::loadXml($file);
        $refs = [];

        foreach ($doc->getElementsByTagNameNS(self::SULU_NS, 'type') as $type) {
            $refs[] = $type->getAttribute('ref');
        }

        foreach ($doc->getElementsByTagNameNS(self::SULU_NS, 'block') as $block) {
            $refs[] = $block->getAttribute('default-type');
        }

        return array_values(array_filter($refs, static fn (string $ref): bool => '' !== $ref));
    }

    /**
     * @return list<string>
     */
    private static function blockXmlFiles(): array
    {
        $files = glob(self::basePath() . '/config/blocks/block--*.xml');
        self::assertNotFalse($files);
        self::assertNotEmpty($files);

        return $files;
    }

    private static function loadXml(string $file): \DOMDocument
    {
        $doc = new \DOMDocument();
        self::assertTrue($doc->load($file), sprintf('%s ist kein wohlgeformtes XML', basename($file)));

        return $doc;
    }

    private static function basePath(): string
    {
        return \dirname(__DIR__, 3);
    }
}
