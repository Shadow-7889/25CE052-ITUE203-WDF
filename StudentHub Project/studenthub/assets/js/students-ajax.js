// Practical 13: AJAX / Fetch based CRUD against api/students.php
(function () {
    var app = document.getElementById('ajaxApp');
    if (!app) return;
    var api = app.dataset.api;
    var $ = function (id) { return document.getElementById(id); };
    var msg = $('msg'), tbody = $('tbody'), form = $('sForm'), q = $('q'), timer;

    function show(type, text) { msg.hidden = false; msg.className = 'alert ' + type; msg.textContent = text; }
    async function call(method, url, body) {
        var opt = { method: method, headers: { 'Accept': 'application/json' } };
        if (body) { opt.headers['Content-Type'] = 'application/json'; opt.body = JSON.stringify(body); }
        var res = await fetch(url, opt), json;
        try { json = await res.json(); } catch (e) { throw new Error('Server returned an invalid response (HTTP ' + res.status + ').'); }
        if (!res.ok || !json.success) throw new Error(json.message || 'Request failed (HTTP ' + res.status + ').');
        return json;
    }
    async function load() {
        tbody.innerHTML = '<tr><td colspan="7">Loading...</td></tr>';
        try { render((await call('GET', api + '?q=' + encodeURIComponent(q.value.trim()))).data); }
        catch (e) { tbody.innerHTML = '<tr><td colspan="7">Could not load students.</td></tr>'; show('error', e.message); }
    }
    function cell(tr, text) { var td = document.createElement('td'); td.textContent = text; tr.appendChild(td); return td; }
    function render(rows) {
        tbody.innerHTML = '';
        if (!rows.length) { tbody.innerHTML = '<tr><td colspan="7">No students found.</td></tr>'; return; }
        rows.forEach(function (r) {
            var tr = document.createElement('tr');
            ['student_id', 'name', 'email', 'course', 'year', 'status'].forEach(function (k) { cell(tr, r[k]); });
            var td = document.createElement('td'); td.className = 'actions';
            var edit = document.createElement('button'); edit.className = 'btn-sm'; edit.type = 'button'; edit.textContent = 'Edit';
            edit.addEventListener('click', function () { startEdit(r); });
            var del = document.createElement('button'); del.className = 'btn-sm btn-danger'; del.type = 'button'; del.textContent = 'Delete';
            del.addEventListener('click', function () { remove(r); });
            td.append(edit, ' ', del); tr.appendChild(td); tbody.appendChild(tr);
        });
    }
    function startEdit(r) {
        $('student_id').value = r.student_id; $('name').value = r.name; $('email').value = r.email;
        $('course').value = r.course; $('year').value = r.year; $('status').value = r.status;
        $('formTitle').textContent = 'Edit Student #' + r.student_id; $('saveBtn').textContent = 'Update Student'; $('cancelBtn').hidden = false;
        $('name').focus();
    }
    function reset() { form.reset(); $('student_id').value = ''; $('formTitle').textContent = 'Add Student'; $('saveBtn').textContent = 'Add Student'; $('cancelBtn').hidden = true; }
    async function remove(r) {
        if (!confirm('Delete ' + r.name + '?')) return;
        try { show('info', 'Deleting...'); var j = await call('POST', api, { action: 'delete', student_id: r.student_id }); show('success', j.message); load(); }
        catch (e) { show('error', e.message); }
    }
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        var data = { name: $('name').value.trim(), email: $('email').value.trim(), course: $('course').value, year: $('year').value, status: $('status').value };
        if (!/^[A-Za-z][A-Za-z .'-]{1,59}$/.test(data.name)) return show('error', 'Enter a valid name (letters only).');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(data.email)) return show('error', 'Enter a valid email address.');
        if (!/^[1-4]$/.test(data.year)) return show('error', 'Year must be between 1 and 4.');
        var id = $('student_id').value;
        data.action = id ? 'update' : 'add'; if (id) data.student_id = id;
        try { show('info', 'Saving...'); var j = await call('POST', api, data); show('success', j.message); reset(); load(); }
        catch (err) { show('error', err.message); }
    });
    $('cancelBtn').addEventListener('click', reset);
    q.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(load, 300); });
    load();
})();
