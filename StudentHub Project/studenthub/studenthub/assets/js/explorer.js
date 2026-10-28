// Practical 6: fetch external JSON, render, search, filter, sort and paginate
(function () {
    var root = document.getElementById('explorer');
    if (!root) return;
    var base = root.dataset.base;
    var $ = function (id) { return document.getElementById(id); };
    var typeSel = $('dataType'), search = $('searchInput'), catSel = $('categoryFilter'), sortSel = $('sortBy'), sizeSel = $('pageSize');
    var list = $('recordList'), statusEl = $('statusMessage'), summary = $('resultsSummary'), pageInfo = $('pageInfo');
    var prev = $('prevBtn'), next = $('nextBtn');
    var records = [], page = 1;

    function normalise(r) {   // one shape for every JSON file
        return {
            title: r.title || r.name || r.question || 'Untitled',
            text: r.description || r.answer || (r.course ? r.course + ' | ' + r.department + ' | ' + r.email : ''),
            category: r.category || r.course || 'General',
            date: r.date || (r.year ? String(r.year) : ''),
            extra: r.venue || ''
        };
    }
    async function load(type) {                      // fetch + parse JSON, handle loading / error state
        statusEl.hidden = false; statusEl.className = 'alert info'; statusEl.textContent = 'Loading data...';
        list.innerHTML = '';
        try {
            var res = await fetch(base + type + '.json');
            if (!res.ok) throw new Error('HTTP ' + res.status);
            var json = await res.json();
            records = json.map(normalise);
            var cats = Array.from(new Set(records.map(function (r) { return r.category; }))).sort();
            catSel.innerHTML = '<option value="all">All categories</option>' + cats.map(function (c) { return '<option>' + c.replace(/</g, '&lt;') + '</option>'; }).join('');
            statusEl.hidden = true; page = 1; render();
        } catch (err) {
            records = []; render();
            statusEl.hidden = false; statusEl.className = 'alert error';
            statusEl.textContent = 'Could not load data (' + err.message + '). Open the site through http://localhost/ (XAMPP), not as a file.';
        }
    }
    function view() {                                // search + filter + sort
        var q = search.value.trim().toLowerCase();
        var out = records.filter(function (r) {
            var hit = !q || (r.title + ' ' + r.text + ' ' + r.category).toLowerCase().indexOf(q) > -1;
            return hit && (catSel.value === 'all' || r.category === catSel.value);
        });
        var s = sortSel.value;
        if (s === 'title-asc') out.sort(function (a, b) { return a.title.localeCompare(b.title); });
        if (s === 'title-desc') out.sort(function (a, b) { return b.title.localeCompare(a.title); });
        if (s === 'date-asc') out.sort(function (a, b) { return a.date.localeCompare(b.date); });
        if (s === 'date-desc') out.sort(function (a, b) { return b.date.localeCompare(a.date); });
        return out;
    }
    function render() {
        var data = view(), size = parseInt(sizeSel.value, 10), pages = Math.max(1, Math.ceil(data.length / size));
        if (page > pages) page = pages;
        var slice = data.slice((page - 1) * size, page * size);
        list.innerHTML = '';
        slice.forEach(function (r) {
            var card = document.createElement('article'); card.className = 'card';
            var h = document.createElement('h3'); h.textContent = r.title;
            var m = document.createElement('p'); m.className = 'muted'; m.textContent = [r.category, r.date, r.extra].filter(Boolean).join(' | ');
            var p = document.createElement('p'); p.textContent = r.text;
            card.append(h, m, p); list.appendChild(card);
        });
        summary.textContent = data.length + ' record(s) found';
        pageInfo.textContent = 'Page ' + page + ' of ' + pages;
        prev.disabled = page <= 1; next.disabled = page >= pages;
    }
    typeSel.addEventListener('change', function () { search.value = ''; sortSel.value = 'default'; load(typeSel.value); });
    [search, catSel, sortSel, sizeSel].forEach(function (el) { el.addEventListener('input', function () { page = 1; render(); }); });
    prev.addEventListener('click', function () { page--; render(); });
    next.addEventListener('click', function () { page++; render(); });
    load(typeSel.value);
})();
