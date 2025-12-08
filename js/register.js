
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


    $('#password').on('input', function () {
        var password = $(this).val();
        var strength = 0;
        var message = '';
        var color = '';

        if (password.length === 0) {
            $('#passwordStrength').text('');
            return;
        }


        if (password.length >= 6) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;


        if (strength < 2) {
            message = 'Weak';
            color = 'red';
        } else if (strength < 4) {
            message = 'Medium';
            color = 'orange';
        } else {
            message = 'Strong';
            color = 'green';
        }

        $('#passwordStrength').text(message).css('color', color);
    });

    $('#registerForm').on('submit', function (e) {
        e.preventDefault();

        var username = $('#username').val();
        var email = $('#email').val();
        var password = $('#password').val();

        $.ajax({
            url: 'php/register.php',
            type: 'POST',
            data: {
                username: username,
                email: email,
                password: password
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#message').html('<div class="alert alert-success">Registration Successful! Redirecting to Login...</div>');
                    $('#registerForm')[0].reset();
                    $('#passwordStrength').text('');
                    setTimeout(function () {
                        window.location.href = 'login.html';
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
