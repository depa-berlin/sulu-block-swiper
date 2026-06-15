<?php

declare(strict_types=1);

namespace Depa\SuluBlockSwiperBundle\Tests\Unit\DependencyInjection;

use Depa\SuluBlockSwiperBundle\DependencyInjection\SuluBlockSwiperExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SuluBlockSwiperExtensionTest extends TestCase
{
    private ContainerBuilder $container;
    private SuluBlockSwiperExtension $extension;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->extension = new SuluBlockSwiperExtension();
    }

    public function testLoadSetsBundleMetadataParameter(): void
    {
        $this->extension->load([], $this->container);
        self::assertTrue($this->container->hasParameter('sulu_block_swiper.bundle_metadata'));
    }

    public function testBundleMetadataHasRequiredKeys(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertIsArray($meta);
        self::assertArrayHasKey('bundle', $meta);
        self::assertArrayHasKey('package', $meta);
        self::assertArrayHasKey('blocks', $meta);
        self::assertArrayHasKey('children', $meta);
    }

    public function testBundleMetadataContainsCorrectBundleName(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertIsArray($meta);
        self::assertSame('SuluBlockSwiperBundle', $meta['bundle']);
    }

    public function testBundleMetadataContainsCorrectPackageName(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertIsArray($meta);
        self::assertSame('depa/sulu-block-swiper', $meta['package']);
    }

    public function testBundleMetadataContainsAtLeastOneBlock(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertIsArray($meta);
        self::assertNotEmpty($meta['blocks']);
    }

    public function testBlocksAreSortedAndUnique(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertIsArray($meta);
        $blocks = $meta['blocks'];
        $sorted = $blocks;
        sort($sorted);
        self::assertSame($sorted, $blocks, 'blocks must be sorted');
        self::assertSame(array_unique($blocks), $blocks, 'blocks must be unique');
    }

    public function testKnownSwiperBlocksArePresent(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertIsArray($meta);

        foreach (['block--swiper', 'block--swiper-slide'] as $expected) {
            self::assertContains($expected, $meta['blocks']);
        }
    }

    public function testSwiperHasChildrenFromXml(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertIsArray($meta);

        self::assertArrayHasKey('block--swiper', $meta['children']);
        self::assertNotEmpty($meta['children']['block--swiper']);
    }

    public function testChildrenValuesAreArraysOfStrings(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertIsArray($meta);

        foreach ($meta['children'] as $parent => $kids) {
            self::assertIsArray($kids, "Children of '{$parent}' must be an array");
            foreach ($kids as $child) {
                self::assertIsString($child);
            }
        }
    }
}
