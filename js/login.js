$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
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
            success: function(response) {
                if (response.status === 'success') {
                    // Store token in LocalStorage
                    localStorage.setItem('session_token', response.token);
                    $('#message').html('<div class="alert alert-success">Login Successful! Redirecting...</div>');
                    setTimeout(function() {
                        window.location.href = 'profile.html';
                    }, 1500);
                } else {
                    $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#message').html('<div class="alert alert-danger">An error occurred.</div>');
            }
        });
    });
});
