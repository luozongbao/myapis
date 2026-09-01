/* =============================================================
   MyAPIs — app.js (mockup / reference)
   -------------------------------------------------------------
   JS กลางที่ทุกหน้าโหลดจาก footer. ใช้เป็น reference สำหรับ
   implementation ที่ public/assets/js/app.js
   ============================================================= */
(function () {
    'use strict';

    // Mobile nav toggle
    var toggle = document.querySelector('.site-nav__toggle');
    var links = document.querySelector('.site-nav__links');
    if (toggle && links) {
        toggle.addEventListener('click', function () {
            var open = links.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Optional: highlight nav link of the current page
    var current = location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.site-nav__link').forEach(function (link) {
        var href = (link.getAttribute('href') || '').split('#')[0].split('/').pop();
        if (href === current) {
            link.classList.add('is-active');
        }
    });
})();
