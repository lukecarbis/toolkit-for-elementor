if( document.querySelectorAll("[data-tklazy-method='interaction']") ){
    const elementsLoadOnUserInteraction = function () {
        const t = ["mouseover", "keydown", "touchstart", "touchmove", "wheel"],
            e = function e() {
                n(),
                    clearTimeout(o),
                    t.forEach(function (t) {
                        window.removeEventListener(t, e, { passive: !0 });
                    });
            };
        var n = function () {
                document.querySelectorAll("[data-tklazy-method='interaction']").forEach(function (t) {
                    t.getAttribute("data-tklazy-attributes")
                        .split(",")
                        .forEach(function (e) {
                            const n = t.getAttribute("data-tklazy-".concat(e));
                            t.setAttribute(e, n);
                        });
                });
            },
            o = setTimeout(e, 10e3);
        t.forEach(function (t) {
            window.addEventListener(t, e, { passive: !0 });
        });
    };
    elementsLoadOnUserInteraction();
}

if( document.querySelectorAll("[data-tklazy-method='viewport']") ){
    const elementsLoadOnViewPort = function () {
        var t = new IntersectionObserver(
            function (e) {
                e.forEach(function (e) {
                    if (e.isIntersecting) {
                        t.unobserve(e.target),
                            e.target
                                .getAttribute("data-tklazy-attributes")
                                .split(",")
                                .forEach(function (t) {
                                    const n = e.target.getAttribute("data-tklazy-".concat(t));
                                    e.target.setAttribute(t, n);
                                });
                    }
                });
            },
            { rootMargin: "300px" }
        );
        document.querySelectorAll("[data-tklazy-method='viewport']").forEach(function (e) {
            t.observe(e);
        });
    };
    elementsLoadOnViewPort();
}
