/*
 * Time checker form widget
 *
 * Data attributes:
 * - data-control="timechecker" - ticks the browser clock and compares it with the checked server time
 * - data-server-time - the server time in milliseconds, present after a check
 *
 * The widget's AJAX handler replaces this element after each check, so a new control measures the new drift.
 */
'use strict';

(function () {
    jax.registerControl('timechecker', class extends jax.ControlBase {
        connect() {
            this.browserClock = this.element.querySelector('[data-timechecker-browser]');
            this.driftOutput = this.element.querySelector('[data-timechecker-drift]');

            if (this.config.serverTime) {
                this.showDrift(Date.now() - parseInt(this.config.serverTime, 10));
            }

            this.tick();
            this.timer = window.setInterval(this.proxy(this.tick), 1000);
        }

        disconnect() {
            window.clearInterval(this.timer);

            this.browserClock = null;
            this.driftOutput = null;
        }

        tick() {
            this.browserClock.textContent = new Date().toLocaleTimeString();
        }

        showDrift(driftMs) {
            const seconds = (driftMs / 1000).toFixed(2),
                isClose = Math.abs(driftMs) < 2000;

            this.driftOutput.textContent = (driftMs >= 0 ? '+' : '') + seconds + ' s';
            this.driftOutput.classList.toggle('is-close', isClose);
            this.driftOutput.classList.toggle('is-far', !isClose);
        }
    });
})();
