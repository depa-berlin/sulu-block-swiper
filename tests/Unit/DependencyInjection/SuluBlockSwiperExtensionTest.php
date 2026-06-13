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
        self::assertArrayHasKey('bundle', $meta);
        self::assertArrayHasKey('package', $meta);
        self::assertArrayHasKey('blocks', $meta);
        self::assertArrayHasKey('children', $meta);
    }

    public function testBundleMetadataContainsCorrectBundleName(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertSame('SuluBlockSwiperBundle', $meta['bundle']);
    }

    public function testBundleMetadataContainsCorrectPackageName(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertSame('depa-berlin/sulu-block-swiper', $meta['package']);
    }

    public function testBundleMetadataContains8BlockTypes(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');
        self::assertCount(8, $meta['blocks']);
    }

    public function testBundleMetadataContainsExpectedBlockTypes(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');

        $expected = [
            'block--swiper',
            'block--swiper-3-image',
            'block--swiper-3-image-slide',
            'block--swiper-bg',
            'block--swiper-bg-slide',
            'block--swiper-hero',
            'block--swiper-slide',
            'block--swiper-slide-facts',
        ];

        foreach ($expected as $blockType) {
            self::assertContains($blockType, $meta['blocks']);
        }
    }

    public function testSwiperHasSwiperSlideFactsAsChild(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');

        self::assertArrayHasKey('block--swiper', $meta['children']);
        self::assertContains('block--swiper-slide-facts', $meta['children']['block--swiper']);
    }

    public function testSwiper3ImageHasCorrectChildSlide(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');

        self::assertArrayHasKey('block--swiper-3-image', $meta['children']);
        self::assertContains('block--swiper-3-image-slide', $meta['children']['block--swiper-3-image']);
    }

    public function testSwiperBgHasBgSlideAsChild(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');

        self::assertArrayHasKey('block--swiper-bg', $meta['children']);
        self::assertContains('block--swiper-bg-slide', $meta['children']['block--swiper-bg']);
    }

    public function testSwiperSlideHasSlideFactsAsChild(): void
    {
        $this->extension->load([], $this->container);
        $meta = $this->container->getParameter('sulu_block_swiper.bundle_metadata');

        self::assertArrayHasKey('block--swiper-slide', $meta['children']);
        self::assertContains('block--swiper-slide-facts', $meta['children']['block--swiper-slide']);
    }
}
