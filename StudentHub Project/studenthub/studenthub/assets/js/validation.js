// Registration form validation with regular expressions (Practical 5)
(function () {
    var form = document.getElementById('registrationForm');
    if (!form) return;
    var rules = {
        name: function (v) { return /^[A-Za-z][A-Za-z .'-]{1,59}$/.test(v) || 'Enter a valid full name (letters only, 2-60 characters).'; },
        email: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) || 'Enter a valid email address.'; },
        mobile: function (v) { return /^[0-9]{10}$/.test(v) || 'Mobile number must contain exactly 10 digits.'; },
        password: function (v) { return (v.length >= 8 && /[a-z]/.test(v) && /[A-Z]/.test(v) && /[0-9]/.test(v)) || 'Use 8+ characters with an uppercase letter, a lowercase letter and a digit.'; },
        confirm_password: function (v) { return (v !== '' && v === form.elements.password.value) || 'Passwords do not match.'; },
        course: function (v) { return v !== '' || 'Please select your course.'; },
        year: function (v) { return /^[1-4]$/.test(v) || 'Year must be between 1 and 4.'; }
    };
    function setError(field, msg) {
        var span = document.getElementById(field + 'Error');
        if (span) span.textContent = msg || '';
        var input = form.elements[field];
        if (input && input.setAttribute) input.setAttribute('aria-invalid', msg ? 'true' : 'false');
    }
    function check(field) {
        var input = form.elements[field];
        var value = field.indexOf('password') > -1 ? input.value : input.value.trim();
        var result = rules[field](value);
        setError(field, result === true ? '' : result);
        return result === true;
    }
    function checkGender() {
        var ok = !!form.querySelector('input[name="gender"]:checked');
        var span = document.getElementById('genderError');
        if (span) span.textContent = ok ? '' : 'Please select your gender.';
        return ok;
    }
    function checkTerms() {
        var ok = form.elements.terms.checked;
        var span = document.getElementById('termsError');
        if (span) span.textContent = ok ? '' : 'You must accept the terms and conditions.';
        return ok;
    }
    function strength() {
        var v = form.elements.password.value, s = 0;
        if (v.length >= 8) s++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
        if (/[0-9]/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v)) s++;
        var labels = ['Too weak', 'Weak', 'Fair', 'Good', 'Strong'], colors = ['#dc3545', '#dc3545', '#f59e0b', '#2563eb', '#16a34a'];
        var fill = document.getElementById('strengthFill'), text = document.getElementById('strengthText');
        if (fill) { fill.style.width = (v ? (s + 1) * 20 : 0) + '%'; fill.style.background = colors[v ? s : 0]; }
        if (text) text.textContent = v ? 'Password strength: ' + labels[s] : '';
    }
    Object.keys(rules).forEach(function (f) {
        var input = form.elements[f];
        input.addEventListener('input', function () {
            if (f === 'password') { strength(); if (form.elements.confirm_password.value) check('confirm_password'); }
            check(f);
        });
        input.addEventListener('blur', function () { check(f); });
        input.addEventListener('change', function () { check(f); });
    });
    form.addEventListener('submit', function (e) {
        var ok = true;
        Object.keys(rules).forEach(function (f) { if (!check(f)) ok = false; });
        if (!checkGender()) ok = false;
        if (!checkTerms()) ok = false;
        if (!ok) { e.preventDefault(); var first = form.querySelector('[aria-invalid="true"]'); if (first) first.focus(); }
    });
})();
