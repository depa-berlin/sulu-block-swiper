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
  ├── block--swiper-slide
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

## Required image formats

The block templates render `<img>`/`<picture>` tags using the following Sulu
image formats. They must be defined in the app (`config/image-formats.xml`),
otherwise rendering fails with missing thumbnail keys:

| Block | Formats | Used for |
|---|---|---|
| `block--swiper-slide-facts` | `content-image` | slide image |
| `block--swiper-3-image-slide` | `card-lg`, `card-md`, `card-xs` | `<picture>` sources: ≥ 768 px, ≥ 576 px, fallback |
| `block--swiper-bg-slide` | `1920x`, `1400x`, `1200x`, `992x`, `767x` | `<picture>` sources: ≥ 1400 px, ≥ 1200 px, ≥ 992 px, ≥ 576 px, fallback |

## Accessibility

`block--swiper-bg` treats its slides as **purely decorative** background images:
the images are rendered with `alt=""`, and the slide wrapper, navigation arrows
and pagination are intentionally hidden from screen readers and keyboard users
(`aria-hidden="true"`, `tabindex="-1"`). The actual content (headline, text,
buttons) lives outside the hidden wrapper and remains fully accessible.

**Do not use `block--swiper-bg` for images that carry meaning** — those users
must be able to perceive. Use one of the other swiper blocks for content-bearing
images instead.

The autoplay toggle buttons (`block--swiper-bg`, `block--swiper-hero`) implement
the WCAG 2.2.2 pause mechanism and are only rendered when autoplay is enabled.

## License

Proprietary — Copyright (c) depa Berlin GmbH & Co. KG. All rights reserved.
See [LICENSE](LICENSE) for details.
