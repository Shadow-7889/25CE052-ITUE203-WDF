
document.addEventListener('DOMContentLoaded', function () {
    var root = document.documentElement;

    var note = document.getElementById('notification');
    var closeNote = document.getElementById('closeNotification');
    if (note && closeNote) closeNote.addEventListener('click', function () { note.classList.add('hidden'); });

    var burger = document.getElementById('hamburger');
    var nav = document.getElementById('navLinks');
    if (burger && nav) {
        burger.addEventListener('click', function () {
            var open = nav.classList.toggle('show');
            burger.setAttribute('aria-expanded', open);
            burger.innerHTML = open ? '&#10005;' : '&#9776;';
        });
    }

    var themeBtn = document.getElementById('themeToggle');
    function paintThemeBtn() {
        if (themeBtn) themeBtn.innerHTML = root.classList.contains('dark') ? '&#9728;&#65039; Light' : '&#127769; Dark';
    }
    paintThemeBtn();
    if (themeBtn) themeBtn.addEventListener('click', function () {
        root.classList.toggle('dark');
        try { localStorage.setItem('theme', root.classList.contains('dark') ? 'dark' : 'light'); } catch (e) {}
        paintThemeBtn();
    });

    document.querySelectorAll('.faq-question').forEach(function (q) {
        q.addEventListener('click', function () {
            var item = q.parentElement;
            var open = item.classList.toggle('open');
            q.setAttribute('aria-expanded', open);
            q.querySelector('span').textContent = open ? '\u2212' : '+';
        });
    });

    var modal = document.getElementById('joinModal');
    var closeModal = document.getElementById('closeModal');
    function hide() { if (modal) { modal.classList.remove('show'); modal.setAttribute('aria-hidden', 'true'); } }
    document.querySelectorAll('.openModal').forEach(function (b) {
        b.addEventListener('click', function () { modal.classList.add('show'); modal.setAttribute('aria-hidden', 'false'); closeModal.focus(); });
    });
    if (closeModal) closeModal.addEventListener('click', hide);
    if (modal) modal.addEventListener('click', function (e) { if (e.target === modal) hide(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') hide(); });

    var slides = document.querySelectorAll('.slide');
    if (slides.length) {
        var dots = document.querySelectorAll('.dot'), cur = 0;
        function show(i) {
            cur = (i + slides.length) % slides.length;
            slides.forEach(function (s, n) { s.classList.toggle('active', n === cur); });
            dots.forEach(function (d, n) { d.classList.toggle('active', n === cur); });
        }
        document.getElementById('prevSlide').addEventListener('click', function () { show(cur - 1); });
        document.getElementById('nextSlide').addEventListener('click', function () { show(cur + 1); });
        dots.forEach(function (d, n) { d.addEventListener('click', function () { show(n); }); });
        setInterval(function () { show(cur + 1); }, 5000);
    }
});
