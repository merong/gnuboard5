(function () {
    function isMobile() {
        return window.matchMedia('(max-width: 768px)').matches;
    }

    function setup(root, spec) {
        var viewport = root.querySelector(spec.viewport);
        var track = root.querySelector(spec.track);
        var btns = Array.prototype.slice.call(root.querySelectorAll(spec.btn));
        if (!viewport || !track || !btns.length) return;
        if (root.getAttribute('data-swipe-ready')) return;
        root.setAttribute('data-swipe-ready', '1');

        var panels = Array.prototype.slice.call(track.children);
        var idx = 0;
        btns.forEach(function (b, i) {
            if (b.classList.contains('active') || b.classList.contains('is-on')) idx = i;
        });

        function layout(animate) {
            if (!isMobile()) {
                track.style.transform = '';
                track.style.transition = '';
                viewport.style.height = '';
                return;
            }
            track.style.transition = animate === false ? 'none' : 'transform .28s ease';
            track.style.transform = 'translate3d(' + (-idx * 100) + '%,0,0)';
            var p = panels[idx];
            if (p) viewport.style.height = p.offsetHeight + 'px';
        }

        function go(n, animate) {
            if (n < 0) n = 0;
            if (n >= btns.length) n = btns.length - 1;
            idx = n;
            layout(animate !== false);
            btns.forEach(function (b, i) {
                var on = i === idx;
                b.classList.toggle(spec.onClass, on);
                b.classList.toggle('active', on);
                b.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            panels.forEach(function (p, i) {
                var on = i === idx;
                p.classList.toggle('active', on);
                p.classList.toggle('is-on', on);
                if (on) p.removeAttribute('hidden');
                else p.setAttribute('hidden', '');
            });
            if (btns[idx] && btns[idx].scrollIntoView) {
                btns[idx].scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' });
            }
        }

        btns.forEach(function (btn, i) {
            btn.addEventListener('click', function () {
                if (isMobile()) go(i, true);
            });
        });

        var x0 = 0, y0 = 0, axis = null, dragging = false, width = 0;

        viewport.addEventListener('touchstart', function (e) {
            if (!isMobile() || e.touches.length !== 1) return;
            x0 = e.touches[0].clientX;
            y0 = e.touches[0].clientY;
            axis = null;
            dragging = true;
            width = viewport.clientWidth || 1;
            track.style.transition = 'none';
        }, { passive: true });

        viewport.addEventListener('touchmove', function (e) {
            if (!dragging) return;
            var dx = e.touches[0].clientX - x0;
            var dy = e.touches[0].clientY - y0;
            if (axis == null && (Math.abs(dx) > 8 || Math.abs(dy) > 8)) {
                axis = Math.abs(dx) > Math.abs(dy) ? 'h' : 'v';
            }
            if (axis !== 'h') return;
            if (e.cancelable) e.preventDefault();
            var pct = (dx / width) * 100;
            if ((idx === 0 && dx > 0) || (idx === btns.length - 1 && dx < 0)) pct *= 0.32;
            track.style.transform = 'translate3d(' + (-idx * 100 + pct) + '%,0,0)';
        }, { passive: false });

        function endDrag(clientX) {
            if (!dragging) return;
            dragging = false;
            if (axis !== 'h') {
                layout(true);
                return;
            }
            var dx = clientX - x0;
            var next = idx;
            if (dx < -Math.max(40, width * 0.16)) next = idx + 1;
            else if (dx > Math.max(40, width * 0.16)) next = idx - 1;
            go(next, true);
        }

        viewport.addEventListener('touchend', function (e) {
            endDrag(e.changedTouches[0].clientX);
        }, { passive: true });
        viewport.addEventListener('touchcancel', function () {
            dragging = false;
            layout(true);
        }, { passive: true });

        window.addEventListener('resize', function () { layout(false); });
        layout(false);
    }

    function init() {
        var tabs = document.querySelector('.portal_tabs');
        if (tabs) setup(tabs, {
            viewport: '.tab_swipe',
            track: '.tab_swipe_track',
            btn: '.tab_nav button',
            onClass: 'active'
        });
        document.querySelectorAll('.moidam_pop').forEach(function (el) {
            setup(el, {
                viewport: '.moidam_pop_swipe',
                track: '.moidam_pop_track',
                btn: '.moidam_pop_tab',
                onClass: 'is-on'
            });
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
