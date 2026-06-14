<?php

declare(strict_types=1);

namespace Depa\SuluBlockSwiperBundle\DependencyInjection;

use Depa\SuluBlockHelperBundle\DependencyInjection\AbstractBlockExtension;

class SuluBlockSwiperExtension extends AbstractBlockExtension
{
    protected function getBundleName(): string
    {
        return 'SuluBlockSwiperBundle';
    }

    protected function getPackageName(): string
    {
        return 'depa-berlin/sulu-block-swiper';
    }

    protected function getMetadataParameterName(): string
    {
        return 'sulu_block_swiper.bundle_metadata';
    }

    protected function getSuluAdminTemplateKey(): string
    {
        return 'sulu_block_swiper';
    }
}
