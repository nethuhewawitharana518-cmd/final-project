/**
 * FoodRescue — Main Application JavaScript
 * Cart management, QR Scanner, AJAX utilities
 */

'use strict';

// ─── CSRF Token Setup ──────────────────────────────────────────
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

// ─── Cart Module ───────────────────────────────────────────────
const Cart = {
    add(foodId, quantity = 1) {
        return fetch(`/customer/cart/add`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ food_id: foodId, quantity }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Cart.updateBadge(data.cart_count);
                Toast.show(data.message, 'success');
            } else {
                Toast.show(data.message || 'Failed to add to cart.', 'error');
            }
            return data;
        });
    },

    updateBadge(count) {
        document.querySelectorAll('.cart-count-badge').forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? 'inline' : 'none';
        });
    },

    remove(itemId) {
        if (!confirm('Remove this item from cart?')) return;
        fetch(`/customer/cart/${itemId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        }).then(() => window.location.reload());
    },
};

// ─── QR Scanner Module ─────────────────────────────────────────
const QRScanner = {
    stream: null,
    scanning: false,

    async init(videoEl, resultCallback) {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            });
            videoEl.srcObject = this.stream;
            videoEl.play();
            this.scanning = true;

            const { BarcodeDetector } = window;
            if (BarcodeDetector) {
                this.detectLoop(videoEl, resultCallback);
            } else {
                console.warn('BarcodeDetector not supported. Use manual input.');
            }
        } catch (err) {
            console.error('Camera error:', err);
            Toast.show('Camera access denied. Use manual token input.', 'error');
        }
    },

    detectLoop(videoEl, callback) {
        if (!this.scanning) return;
        const detector = new BarcodeDetector({ formats: ['qr_code'] });

        const scan = async () => {
            if (!this.scanning) return;
            try {
                const codes = await detector.detect(videoEl);
                if (codes.length > 0) {
                    this.scanning = false;
                    callback(codes[0].rawValue);
                    return;
                }
            } catch {}
            requestAnimationFrame(scan);
        };
        scan();
    },

    stop() {
        this.scanning = false;
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
    },

    verify(token) {
        return fetch('/api/qr/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ qr_token: token }),
        }).then(r => r.json());
    },
};

// ─── Toast Notification ────────────────────────────────────────
const Toast = {
    show(message, type = 'info', duration = 3500) {
        const el = document.createElement('div');
        const colors = { success: '#2ECC71', error: '#E74C3C', info: '#3498DB', warning: '#F39C12' };
        el.style.cssText = `
            position:fixed; bottom:24px; right:24px; z-index:9999;
            background:${colors[type] || '#333'}; color:#fff;
            padding:14px 20px; border-radius:12px;
            box-shadow:0 8px 24px rgba(0,0,0,0.2);
            font-family:Inter,sans-serif; font-weight:600; font-size:0.9rem;
            max-width:320px; line-height:1.4;
            animation: slideUp 0.3s ease;
        `;
        el.textContent = message;
        document.body.appendChild(el);
        setTimeout(() => { el.style.animation = 'slideDown 0.3s ease'; setTimeout(() => el.remove(), 300); }, duration);
    }
};

// ─── Expiry Countdown Timers ───────────────────────────────────
function initExpiryCountdowns() {
    document.querySelectorAll('[data-expiry-datetime]').forEach(el => {
        const expiryDate = new Date(el.dataset.expiryDatetime);
        const update = () => {
            const diff = expiryDate - Date.now();
            if (diff <= 0) { el.textContent = '⏱ Expired'; el.style.color = '#E74C3C'; return; }

            const h = Math.floor(diff / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);

            if (h < 2)      { el.style.color = '#E74C3C'; }
            else if (h < 6) { el.style.color = '#F39C12'; }
            else            { el.style.color = '#2ECC71'; }

            el.textContent = h > 0 ? `⏱ ${h}h ${m}m left` : `⏱ ${m}m left`;
        };
        update();
        setInterval(update, 30000);
    });
}

// ─── Admin/Business AI Risk Refresh ───────────────────────────
function initAIBadges() {
    document.querySelectorAll('[data-ai-risk]').forEach(el => {
        const risk = el.dataset.aiRisk;
        const icons = { high: '🔴', medium: '🟡', low: '🟢' };
        el.textContent = `${icons[risk] || '⚪'} ${risk.charAt(0).toUpperCase() + risk.slice(1)} Risk`;
    });
}

// ─── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initExpiryCountdowns();
    initAIBadges();

    // Add-to-cart buttons
    document.querySelectorAll('[data-cart-add]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id  = btn.dataset.cartAdd;
            const qty = parseInt(btn.closest('[data-qty-selector]')?.querySelector('.qty-input')?.value || 1);
            Cart.add(id, qty);
        });
    });

    // Sidebar toggle (mobile)
    const toggleBtn = document.querySelector('#sidebar-toggle');
    const sidebar   = document.querySelector('.sidebar');
    toggleBtn?.addEventListener('click', () => sidebar?.classList.toggle('open'));
});
