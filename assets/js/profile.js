$(document).ready(function () {
    // Check for token
    var token = localStorage.getItem('session_token');
    if (!token) {
        window.location.href = 'login.html';
        return;
    }

    // Load Profile Data
    $.ajax({
        url: 'php/profile.php',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success' && response.data) {
                $('#age').val(response.data.age);
                $('#dob').val(response.data.dob);
                $('#contact').val(response.data.contact);
                $('#address').val(response.data.address);
            } else if (response.status === 'error') {
                // Token invalid
                localStorage.removeItem('session_token');
                window.location.href = 'login.html';
            }
        }
    });

    // Update Profile
    $('#profileForm').on('submit', function (e) {
        e.preventDefault();

        var age = $('#age').val();
        var dob = $('#dob').val();
        var contact = $('#contact').val();
        var address = $('#address').val();

        $.ajax({
            url: 'php/profile.php',
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token
            },
            data: {
                token: token, // Fallback if headers fail
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

    // Logout
    $('#logoutBtn').click(function () {
        localStorage.removeItem('session_token');
        window.location.href = 'login.html';
    });
});
