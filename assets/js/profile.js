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

        // Validation
        if (!age || !dob || !contact || !address) {
            $('#message').html('<div class="alert alert-danger">Please fill in all fields.</div>');
            return;
        }

        // Validate Age vs DOB
        var dobDate = new Date(dob);
        var today = new Date();
        var calculatedAge = today.getFullYear() - dobDate.getFullYear();
        var m = today.getMonth() - dobDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dobDate.getDate())) {
            calculatedAge--;
        }

        if (parseInt(age) !== calculatedAge) {
            $('#message').html('<div class="alert alert-danger">Age does not match Date of Birth! (Calculated: ' + calculatedAge + ')</div>');
            return;
        }

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
