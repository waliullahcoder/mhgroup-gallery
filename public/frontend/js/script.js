$(document).ready(function () {

    let lastScrollTop = 0;
    $(window).scroll(function () {
        let currentScroll = $(this).scrollTop();

        if (currentScroll < 100) {
            $(".scrollTop").fadeOut();
            $(".header-middle").removeClass("sticky");
        } else {
            $(".header-middle").addClass("sticky");
            $(".scrollTop").fadeIn();
        }

        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    });

    $(".scrollTop").click(function (e) {
        e.preventDefault();
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    });
});

function initCarousels() {
    document.querySelectorAll(".carousel").forEach(function (el) {
        // Read dataset options
        let slidesPerView = el.dataset.items || 1;
        let slidesPerViewXs = el.dataset.xsItems || slidesPerView;
        let slidesPerViewSm = el.dataset.smItems || slidesPerViewXs;
        let slidesPerViewMd = el.dataset.mdItems || slidesPerViewSm;
        let slidesPerViewLg = el.dataset.lgItems || slidesPerViewMd;
        let slidesPerViewXl = el.dataset.xlItems || slidesPerViewLg;

        let autoplay = el.dataset.autoplay ? { delay: 3000, disableOnInteraction: false } : false;
        let arrows = el.dataset.arrows ? true : false;
        let dots = el.dataset.dots ? true : false;
        let loop = el.dataset.loop ? true : false;
        let margin = el.dataset.margin ? parseInt(el.dataset.margin) : 20;

        // Default options
        let swiperOptions = {
            slidesPerView: parseInt(slidesPerView),
            spaceBetween: margin,
            loop: loop,
            autoplay: autoplay,
            breakpoints: {
                576: { slidesPerView: parseInt(slidesPerViewXs) },
                768: { slidesPerView: parseInt(slidesPerViewSm) },
                992: { slidesPerView: parseInt(slidesPerViewMd) },
                1200: { slidesPerView: parseInt(slidesPerViewLg) },
                1400: { slidesPerView: parseInt(slidesPerViewXl) },
            }
        };

        // Enable arrows if requested
        if (arrows) {
            swiperOptions.navigation = {
                nextEl: el.parentElement.querySelector(".swiper-button-next"),
                prevEl: el.parentElement.querySelector(".swiper-button-prev"),
            };
        }

        // Enable dots if requested
        if (dots) {
            swiperOptions.pagination = {
                el: el.querySelector(".swiper-pagination") || null,
                clickable: true
            };
        }

        // Init Swiper
        new Swiper(el, swiperOptions);
    });
}

// Run on DOM ready
document.addEventListener("DOMContentLoaded", initCarousels);
