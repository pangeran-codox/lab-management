/* ─────────────────────────────────────────────────────────────
   jadwal-penting.js
   ───────────────────────────────────────────────────────────── */

/**
 * Toggle slot section saat checkbox "Blokir Seharian" berubah
 */
window.toggleSlotSection = function (isFullDay) {
    const section = document.getElementById('slotSection');
    const track   = document.getElementById('toggleTrack');
    const thumb   = document.getElementById('toggleThumb');

    if (!section) return;

    section.classList.toggle('hidden', isFullDay);

    if (track) track.classList.toggle('on', isFullDay);
    if (thumb) thumb.style.left = isFullDay ? '22px' : '2px';
};

/**
 * Pilih warna badge
 */
window.selectColor = function (color, el) {
    document.querySelectorAll('.jp-color-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');

    const input   = document.getElementById('colorInput');
    const dot     = document.getElementById('previewDot');

    if (input) input.value = color;
    if (dot)   dot.style.background = color;
};

/**
 * Init on DOMContentLoaded
 */
document.addEventListener('DOMContentLoaded', () => {
    // Sinkronkan toggle state saat halaman load (untuk edit page)
    const cb = document.getElementById('fullDayToggle');
    if (cb) {
        const track = document.getElementById('toggleTrack');
        const thumb = document.getElementById('toggleThumb');
        if (cb.checked) {
            track && track.classList.add('on');
            thumb && (thumb.style.left = '22px');
        }
    }

    // Update preview text saat user ketik nama event
    const titleInput   = document.querySelector('input[name="title"]');
    const previewText  = document.getElementById('previewText');
    if (titleInput && previewText) {
        titleInput.addEventListener('input', () => {
            previewText.textContent = titleInput.value.trim() || 'Preview Event';
        });
    }
});