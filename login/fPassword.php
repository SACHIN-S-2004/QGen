<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        Swal.fire({
            title: 'Reset Password',
            text: 'Please enter your email address',
            input: 'email',
            inputPlaceholder: 'Enter your email',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            preConfirm: async (email) => { // Switch to async/await for cleaner flow
                if (!email) {
                    Swal.showValidationMessage('Email is required');
                    return false;
                }

                Swal.showLoading();
                Swal.update({
                    title: 'Processing...',
                    text: 'Please wait while we verify your email.',
                    showConfirmButton: false
                });

                try {
                    const response = await fetch('verify_email.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'email=' + encodeURIComponent(email)
                    });

                    console.log('Response status:', response.status);
                    const text = await response.text();
                    console.log('Raw response:', text);
                    const data = JSON.parse(text);

                    Swal.hideLoading();
                    Swal.update({
                        showConfirmButton: true
                    });

                    if (!data.success) {
                        Swal.showValidationMessage(data.message); // Show error but don’t throw
                        return false; // Returning false keeps the modal open without breaking the chain
                    }

                    return { email: email, otp: data.otp }; // Success case
                } catch (error) {
                    Swal.hideLoading();
                    Swal.update({
                        showConfirmButton: true
                    });
                    console.error('Error:', error);
                    Swal.showValidationMessage('An error occurred. Please try again.');
                    return false;
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                //console.log("helo");
                const email = result.value.email;
                const sentOtp = result.value.otp;

                Swal.fire({
                    title: 'Verify OTP',
                    text: 'Enter the OTP sent to your email',
                    input: 'text',
                    inputPlaceholder: 'Enter OTP',
                    showCancelButton: true,
                    confirmButtonText: 'Verify',
                    preConfirm: (enteredOtp) => {
                        if (!enteredOtp) {
                            Swal.showValidationMessage('OTP is required');
                            return false;
                        }
                        if (enteredOtp !== sentOtp) {
                            Swal.showValidationMessage('Invalid OTP');
                            return false;
                        }
                        return true;
                    }
                }).then((otpResult) => {
                    if (otpResult.isConfirmed) {
                        Swal.fire({
                            title: 'Set New Password',
                            html:
                                '<input type="password" id="password" class="swal2-input" placeholder="New Password">' +
                                '<input type="password" id="confirm-password" class="swal2-input" placeholder="Confirm Password">',
                            focusConfirm: false,
                            showCancelButton: true,
                            confirmButtonText: 'Reset Password',
                            preConfirm: () => {
                                const password = document.getElementById('password').value;
                                const confirmPassword = document.getElementById('confirm-password').value;
                                
                                if (!password || !confirmPassword) {
                                    Swal.showValidationMessage('Both password fields are required');
                                    return false;
                                }
                                if (password !== confirmPassword) {
                                    Swal.showValidationMessage('Passwords do not match');
                                    return false;
                                }
                                
                                return fetch('update_password.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: 'email=' + encodeURIComponent(email) + 
                                          '&password=' + encodeURIComponent(password)
                                })
                                .then(response => {
                                    console.log('Update status:', response.status);
                                    return response.text();
                                })
                                .then(text => {
                                    console.log('Update raw response:', text);
                                    return JSON.parse(text);
                                })
                                .then(data => {
                                    if (!data.success) {
                                        throw new Error(data.message);
                                    }
                                    return true;
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(error.message);
                                    throw error;
                                });
                            }
                        }).then((passwordResult) => {
                            if (passwordResult.isConfirmed) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Your password has been reset.',
                                    icon: 'success',
                                    confirmButtonText: 'Go to Home'
                                }).then(() => {
                                    // Redirect to home.php in the home directory
                                    window.location.href = '../home.php';
                                });
                            } else if (passwordResult.isDismissed) { // Handle Cancel in Password modal
                                window.location.href = '../home.php';
                            }
                        });
                    } else if (otpResult.isDismissed) { // Handle Cancel in OTP modal
                        window.location.href = '../home.php';
                    }
                });
            } else if (result.isDismissed) { // Handle Cancel in Email modal
                window.location.href = '../home.php';
            }
        });
    </script>
</body>
</html>