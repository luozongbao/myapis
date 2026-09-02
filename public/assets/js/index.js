/* ============================================================
 * MyAPIs landing page (index.php) client-side enhancements.
 * Extracted from the original inline <script> block.
 * ========================================================== */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initToolCards();
        initButtonLoading();
        initUptimeIndicator();
    });

    /**
     * Clicking anywhere on a tool card (except the action buttons)
     * navigates to the tool itself.
     */
    function initToolCards() {
        document.querySelectorAll('.tool-card').forEach(function (card) {
            card.addEventListener('click', function (e) {
                if (e.target.tagName === 'A') return;
                var primaryButton = card.querySelector('.btn-primary');
                if (primaryButton) {
                    window.location.href = primaryButton.href;
                }
            });
        });
    }

    /**
     * Briefly flash a "Loading..." state on every .btn click.
     */
    function initButtonLoading() {
        document.querySelectorAll('.btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var originalText = this.textContent;
                this.textContent = 'Loading...';
                setTimeout(function () {
                    button.textContent = originalText;
                }, 1000);
            });
        });
    }

    /**
     * Subtle blinking highlight on the uptime percentage stat.
     */
    function initUptimeIndicator() {
        document.querySelectorAll('[data-stat="uptime"]').forEach(function (stat) {
            setInterval(function () {
                stat.style.color = stat.style.color === 'rgb(76, 175, 80)' ? '' : '#4CAF50';
            }, 2000);
        });
    }
})();