<?php

declare(strict_types=1);

namespace Depa\SuluBlockSwiperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class SuluBlockSwiperExtension extends Extension implements PrependExtensionInterface
{
    private const BLOCKS = [
        'block--swiper', 'block--swiper-3-image',
        'block--swiper-3-image-slide', 'block--swiper-bg',
        'block--swiper-bg-slide', 'block--swiper-hero',
        'block--swiper-slide', 'block--swiper-slide-facts',
    ];

    private const CHILDREN = [
        'block--swiper'         => ['block--swiper-slide-facts'],
        'block--swiper-slide'   => ['block--swiper-slide-facts'],
        'block--swiper-3-image' => ['block--swiper-3-image-slide'],
        'block--swiper-bg'      => ['block--swiper-bg-slide'],
    ];

    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->setParameter('sulu_block_swiper.bundle_metadata', [
            'bundle'   => 'SuluBlockSwiperBundle',
            'package'  => 'depa-berlin/sulu-block-swiper',
            'blocks'   => self::BLOCKS,
            'children' => self::CHILDREN,
        ]);
    }

    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('twig')) {
            $container->prependExtensionConfig('twig', [
                'paths' => [
                    __DIR__ . '/../../Resources/views' => null,
                ],
            ]);
        }

        if ($container->hasExtension('sulu_admin')) {
            $container->prependExtensionConfig('sulu_admin', [
                'templates' => [
                    'block' => [
                        'directories' => [
                            'sulu_block_swiper' => __DIR__ . '/../../Resources/config/blocks',
                        ],
                    ],
                ],
            ]);
        }
    }
}
