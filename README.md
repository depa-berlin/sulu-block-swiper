# sulu-block-swiper

Swiper/Carousel block collection for Sulu CMS — 8 configurable slider and carousel blocks.

## Included Blocks

| Block | Description |
|---|---|
| `block--swiper` | Base swiper container with slides |
| `block--swiper-slide` | Standard slide |
| `block--swiper-slide-facts` | Facts/statistics slide |
| `block--swiper-3-image` | 3-image carousel container |
| `block--swiper-3-image-slide` | Slide for 3-image carousel |
| `block--swiper-bg` | Background-image swiper |
| `block--swiper-bg-slide` | Background slide with content overlay |
| `block--swiper-hero` | Hero/fullscreen carousel |

## Block Hierarchy

```
block--swiper
  └── block--swiper-slide-facts

block--swiper-slide
  └── block--swiper-slide-facts

block--swiper-3-image
  └── block--swiper-3-image-slide

block--swiper-bg
  └── block--swiper-bg-slide
```

## Requirements

- PHP 8.2+
- Symfony 7.0+
- Sulu CMS 3.0+
- `depa/sulu-block-helper`
- Swiper.js (loaded via `asset_collector` in templates)

## Installation

```bash
composer require depa/sulu-block-swiper
```

Register in `config/bundles.php`:

```php
Depa\SuluBlockHelperBundle\SuluBlockHelperBundle::class => ['all' => true],
Depa\SuluBlockSwiperBundle\SuluBlockSwiperBundle::class => ['all' => true],
```

Publish the bundle assets (JS under `/bundles/sulublockswiper/`):

```bash
bin/console assets:install
```

## Assets

- `Resources/public/js/block--swiper.js` — Swiper initialization for `block--swiper`;
  reads the block settings from the `data-*` attributes rendered by the template.
  Requires Swiper.js (`swiper-lib.js`) to be provided by the app.
- The stylesheets and the JS for the other blocks (`block--swiper-bg`, `block--swiper-hero`,
  `block--swiper-3-image`) are currently still expected under `/website/…` in the app.

## License

Proprietary — Copyright (c) depa Berlin GmbH & Co. KG. All rights reserved.
See [LICENSE](LICENSE) for details.
