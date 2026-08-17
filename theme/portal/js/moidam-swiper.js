(function () {
    if (typeof Swiper === "undefined") return;
    var nodes = document.querySelectorAll(".moidam-swiper");
    for (var i = 0; i < nodes.length; i++) {
        var el = nodes[i];
        if (el.swiper) continue;
        var slides = el.querySelectorAll(".swiper-slide").length;
        new Swiper(el, {
            slidesPerView: 4,
            spaceBetween: 12,
            watchOverflow: true,
            grabCursor: slides > 4,
            pagination: {
                el: el.querySelector(".swiper-pagination"),
                clickable: true
            },
            breakpoints: {
                0: { slidesPerView: 1.35, spaceBetween: 10 },
                560: { slidesPerView: 2, spaceBetween: 10 },
                860: { slidesPerView: 3, spaceBetween: 12 },
                1100: { slidesPerView: 4, spaceBetween: 12 }
            }
        });
    }
})();
