function recaptcha_callback() {
    var btns = document.querySelectorAll("button[type='submit']");
    btns.forEach(btn => {
        btn.disabled = false;
    });
}

function recaptcha_expire() {
    var btns = document.querySelectorAll("button[type='submit']");
    btns.forEach(btn => {
        btn.disabled = true;
    });
}

function submit_enter(e) {
    if (!e.disabled) return;
    var captchas = document.querySelectorAll(".g-recaptcha");
    captchas.forEach(captcha => {
        captcha.classList.add("red-border");
    });
}

function submit_leave() {
    var captchas = document.querySelectorAll(".g-recaptcha");
    captchas.forEach(captcha => {
        captcha.classList.remove("red-border");
    });
}

window.callback = recaptcha_callback;
window.expire = recaptcha_expire;
window.submit_enter = submit_enter;
window.submit_leave = submit_leave;

var script = document.createElement("script");
script.src = 'https://www.google.com/recaptcha/api.js';
document.head.appendChild(script);