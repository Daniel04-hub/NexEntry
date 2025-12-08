
(function () {
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'light') {
        document.documentElement.classList.add('light-mode');
    }
})();

$(document).ready(function () {

    $('#togglePassword').on('click', function () {
        const passwordInput = $('#password');
        const icon = $(this).find('i');

        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
        }
    });

    $('#loginForm').on('submit', function (e) {
        e.preventDefault();

        var email = $('#email').val();
        var password = $('#password').val();

        $.ajax({
            url: 'php/login.php',
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {

                    localStorage.setItem('session_token', response.token);
                    $('#message').html('<div class="alert alert-success">Login Successful! Redirecting...</div>');
                    setTimeout(function () {
                        window.location.href = 'profile.html';
                    }, 1500);
                } else {
                    $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('#message').html('<div class="alert alert-danger">An error occurred.</div>');
            }
        });
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('themeToggle');
    const html = document.documentElement;
    const icon = themeToggleBtn.querySelector('i');


    if (html.classList.contains('light-mode')) {
        icon.classList.remove('bi-moon-fill');
        icon.classList.add('bi-sun-fill');
    } else {
        icon.classList.remove('bi-sun-fill');
        icon.classList.add('bi-moon-fill');
    }

    themeToggleBtn.addEventListener('click', () => {
        html.classList.toggle('light-mode');

        if (html.classList.contains('light-mode')) {
            localStorage.setItem('theme', 'light');
            icon.classList.remove('bi-moon-fill');
            icon.classList.add('bi-sun-fill');
        } else {
            localStorage.setItem('theme', 'dark');
            icon.classList.remove('bi-sun-fill');
            icon.classList.add('bi-moon-fill');
        }
    });
});
