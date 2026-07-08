(function () {
    'use strict';

    function initBlockSwiper(el) {
        if (typeof Swiper === 'undefined' || el.dataset.swiperInitialized === 'true') {
            return;
        }

        var config = {
            effect: el.dataset.effect || 'fade',
            loop: el.dataset.loop !== 'false',
            speed: parseInt(el.dataset.speed || '600', 10),
        };

        if (config.effect === 'fade') {
            config.fadeEffect = {crossFade: true};
        }

        if (el.dataset.autoplay !== 'false') {
            config.autoplay = {
                delay: parseInt(el.dataset.autoplayDelay || '5000', 10),
            };
        }

        var next = el.querySelector('.swiper-button-next');
        var prev = el.querySelector('.swiper-button-prev');
        if (el.dataset.showNavigation !== 'false' && next && prev) {
            config.navigation = {nextEl: next, prevEl: prev};
        }

        var pagination = el.querySelector('.swiper-pagination');
        if (el.dataset.showPagination !== 'false' && pagination) {
            config.pagination = {el: pagination, clickable: true};
        }

        el.dataset.swiperInitialized = 'true';
        new Swiper(el, config);
    }

    function initAll() {
        document.querySelectorAll('.block--swiper.swiper').forEach(initBlockSwiper);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
