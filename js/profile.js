
(function () {
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'light') {
        document.documentElement.classList.add('light-mode');
    }
})();

$(document).ready(function () {

    var token = localStorage.getItem('session_token');
    if (!token) {
        window.location.href = 'login.html';
        return;
    }


    $.ajax({
        url: 'php/profile.php',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        }
    });


    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    todayStr = yyyy + '-' + mm + '-' + dd;
    $('#dob').attr('max', todayStr);


    $('#profileForm').on('submit', function (e) {
        e.preventDefault();

        var age = $('#age').val();
        var dob = $('#dob').val();
        var contact = $('#contact').val();
        var address = $('#address').val();


        if (!age || !dob || !contact || !address) {
            $('#message').html('<div class="alert alert-danger">Please fill in all fields.</div>');
            return;
        }


        var dobDate = new Date(dob);
        var today = new Date();

        if (dobDate > today) {
            $('#message').html('<div class="alert alert-danger">Date of Birth cannot be in the future!</div>');
            return;
        }

        var calculatedAge = today.getFullYear() - dobDate.getFullYear();
        var m = today.getMonth() - dobDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dobDate.getDate())) {
            calculatedAge--;
        }

        if (parseInt(age) !== calculatedAge) {
            $('#message').html('<div class="alert alert-danger">Age (' + age + ') does not match Date of Birth! (Should be: ' + calculatedAge + ')</div>');
            return;
        }

        $.ajax({
            url: 'php/profile.php',
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: {
                token: token,
                age: age,
                dob: dob,
                contact: contact,
                address: address
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#message').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }
        });
    });


    $('#logoutBtn').click(function () {
        localStorage.removeItem('session_token');
        window.location.href = 'login.html';
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
