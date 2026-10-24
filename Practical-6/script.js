const datafiles={
    events: "data/events.json",
    profile: "data/profile.json",
    notice: "data/notice.json ",
    faqs: "data/faqs.json "
};

let currentType="events";

/* Practical-5: Student registration form validation using regular expressions. */
(function () {
    'use strict';

    function addError(input, message) {
        if (!input) return;
        var id = input.id + 'Error';
        var error = document.getElementById(id);
        if (!error) {
            error = document.createElement('span');
            error.id = id;
            error.className = 'field-error';
            error.setAttribute('aria-live', 'polite');
            input.insertAdjacentElement('afterend', error);
        }
        error.textContent = message;
        input.setAttribute('aria-invalid', message ? 'true' : 'false');
        if (message) input.setAttribute('aria-describedby', id);
        else input.removeAttribute('aria-describedby');
    }

    function setupRegistration() {
        var form = document.getElementById('registrationForm');
        if (!form) return;
        // Use our own validation so inline errors are shown beside every invalid field.
        form.noValidate = true;
        var name = document.getElementById('name');
        var email = document.getElementById('email');
        var mobile = document.getElementById('mobile');
        var password = document.getElementById('password');
        var confirmPassword = document.getElementById('confirmPassword');
        var course = document.getElementById('course');
        var year = document.getElementById('year');

        // Add explicit labels, required attributes and useful HTML5 input hints.
        [[name,'Full Name'],[email,'Email Address'],[mobile,'Mobile Number'],[password,'Password'],[confirmPassword,'Confirm Password'],[course,'Course'],[year,'Year']].forEach(function (pair) {
            var input = pair[0];
            if (!input) return;
            input.required = true;
            var label = form.querySelector('label[for="' + input.id + '"]');
            if (!label) {
                label = input.closest('.form-group').querySelector('label');
                if (label) label.setAttribute('for', input.id);
            }
        });
        if (name) { name.minLength = 2; name.autocomplete = 'name'; name.setAttribute('pattern', '[A-Za-z][A-Za-z .\'-]{1,59}'); }
        if (email) email.autocomplete = 'email';
        if (mobile) { mobile.autocomplete = 'tel'; mobile.setAttribute('inputmode','numeric'); mobile.setAttribute('maxlength','10'); }
        if (password) { password.autocomplete = 'new-password'; password.minLength = 8; }
        if (confirmPassword) confirmPassword.autocomplete = 'new-password';
        if (year) year.setAttribute('aria-label','Year of study (1 to 4)');

        // Ensure radio options are grouped and accessible without rewriting the HTML page.
        var genderInputs = form.querySelectorAll('input[name="gender"]');
        genderInputs.forEach(function (radio, index) {
            radio.required = true;
            var label = radio.parentNode;
            if (label && label.classList && label.classList.contains('gender-box')) {
                var text = radio.nextSibling;
                var wrapper = document.createElement('label');
                wrapper.style.display = 'inline-flex'; wrapper.style.alignItems = 'center'; wrapper.style.gap = '4px'; wrapper.style.marginRight = '10px';
                radio.parentNode.insertBefore(wrapper, radio); wrapper.appendChild(radio);
                if (text) wrapper.appendChild(text);
            }
        });
        var genderBox = form.querySelector('.gender-box');
        if (genderBox && !document.getElementById('genderError')) {
            var ge = document.createElement('span'); ge.id='genderError'; ge.className='field-error'; ge.setAttribute('aria-live','polite'); genderBox.insertAdjacentElement('afterend',ge);
        }

        // Terms acceptance field is created dynamically to preserve the original HTML body.
        var termsRow = document.getElementById('termsRow');
        if (!termsRow) {
            termsRow = document.createElement('div'); termsRow.className='terms-row'; termsRow.id='termsRow';
            var terms = document.createElement('input'); terms.type='checkbox'; terms.id='terms'; terms.name='terms'; terms.required=true;
            var termsLabel = document.createElement('label'); termsLabel.htmlFor='terms'; termsLabel.textContent='I accept the terms and conditions.'; termsLabel.style.display='inline';
            termsRow.appendChild(terms); termsRow.appendChild(termsLabel);
            var buttonGroup = form.querySelector('.button-group');
            if (buttonGroup) form.insertBefore(termsRow, buttonGroup); else form.appendChild(termsRow);
            var te=document.createElement('span'); te.id='termsError'; te.className='field-error'; te.setAttribute('aria-live','polite'); termsRow.insertAdjacentElement('afterend',te);
        }

        // Password strength meter.
        var meter = document.getElementById('passwordStrength');
        if (!meter && password) {
            meter=document.createElement('div'); meter.id='passwordStrength'; meter.className='password-meter'; meter.setAttribute('aria-live','polite');
            meter.innerHTML='<span id="strengthText">Password strength: not entered</span><div class="password-meter-track" aria-hidden="true"><div class="password-meter-fill" id="strengthFill"></div></div>';
            password.insertAdjacentElement('afterend',meter);
        }
        function updateStrength() {
            if (!password) return;
            var value=password.value, score=0;
            if (value.length>=8) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[a-z]/.test(value)) score++;
            if (/\d/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;
            var words=['Very weak','Weak','Fair','Good','Strong','Strong'];
            var fill=document.getElementById('strengthFill'), text=document.getElementById('strengthText');
            if (fill) fill.style.width=(score*20)+'%';
            if (text) text.textContent='Password strength: '+(value ? words[score] : 'not entered');
            if (fill) fill.style.backgroundColor=score<2?'#dc3545':score<4?'#f0ad4e':'#28a745';
        }
        if (password) password.addEventListener('input',updateStrength);

        function validateForm() {
            var valid=true;
            var nameRegex=/^[A-Za-z][A-Za-z .'-]{1,59}$/;
            var emailRegex=/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
            var mobileRegex=/^[6-9][0-9]{9}$/;
            var passwordRegex=/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
            if (!name || !nameRegex.test(name.value.trim())) { addError(name,'Enter a valid name (2–60 letters, spaces or . \' - allowed).'); valid=false; } else addError(name,'');
            if (!email || !emailRegex.test(email.value.trim())) { addError(email,'Enter a valid email address.'); valid=false; } else addError(email,'');
            if (!mobile || !mobileRegex.test(mobile.value.trim())) { addError(mobile,'Enter a valid 10-digit Indian mobile number starting with 6–9.'); valid=false; } else addError(mobile,'');
            if (!password || !passwordRegex.test(password.value)) { addError(password,'Use 8+ characters with uppercase, lowercase, a number and a special character.'); valid=false; } else addError(password,'');
            if (!confirmPassword || confirmPassword.value!== (password ? password.value : '')) { addError(confirmPassword,'Passwords do not match.'); valid=false; } else addError(confirmPassword,'');
            if (!course || !course.value) { addError(course,'Please select a course.'); valid=false; } else addError(course,'');
            if (!year || !/^[1-4]$/.test(year.value)) { addError(year,'Enter a study year from 1 to 4.'); valid=false; } else addError(year,'');
            var genderSelected=!!form.querySelector('input[name="gender"]:checked');
            var genderError=document.getElementById('genderError');
            if (!genderSelected) { if(genderError) genderError.textContent='Please select your gender.'; valid=false; } else if(genderError) genderError.textContent='';
            var terms=document.getElementById('terms'), termsError=document.getElementById('termsError');
            if (!terms || !terms.checked) { if(termsError) termsError.textContent='You must accept the terms and conditions.'; valid=false; } else if(termsError) termsError.textContent='';
            var status=document.getElementById('registrationStatus');
            if (!status) { status=document.createElement('div'); status.id='registrationStatus'; status.className='form-status'; status.setAttribute('role','status'); status.setAttribute('aria-live','polite'); form.insertBefore(status,form.firstChild.nextSibling); }
            status.className='form-status '+(valid?'success':'error');
            status.textContent=valid?'Registration form validated successfully. (Demo only; no data has been submitted to a server.)':'Please correct the highlighted fields and try again.';
            if (!valid) { var first=form.querySelector('[aria-invalid="true"]'); if(first) first.focus(); }
            return false; // Demo validation only; do not send/store personal data.
        }
        window.validateForm=validateForm;
        form.addEventListener('submit',function(event){ event.preventDefault(); validateForm(); });
        form.addEventListener('reset',function(){
            window.setTimeout(function(){
                form.querySelectorAll('.field-error').forEach(function(e){e.textContent='';});
                form.querySelectorAll('[aria-invalid]').forEach(function(e){e.removeAttribute('aria-invalid');});
                var status=document.getElementById('registrationStatus'); if(status) status.remove();
                updateStrength();
            },0);
        });
        [name,email,mobile,password,confirmPassword,course,year].forEach(function(input){ if(input) input.addEventListener('input',function(){ if(input.getAttribute('aria-invalid')==='true') addError(input,''); }); });
        updateStrength();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded',setupRegistration);
    else setupRegistration();
}());
