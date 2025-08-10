<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Account</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#146ef5',
                            secondary: '#6B7280'
                        },
                        borderRadius: {
                            'none': '0px',
                            'sm': '4px',
                            DEFAULT: '8px',
                            'md': '12px',
                            'lg': '16px',
                            'xl': '20px',
                            '2xl': '24px',
                            '3xl': '32px',
                            'full': '9999px',
                            'button': '8px'
                        }
                    }
                }
            }
        </script>
        <style>
        :where([class^="ri-"])::before { content: "\f3c2"; }
        .file-input::-webkit-file-upload-button {
            display: none;
        }
        .file-input::file-selector-button {
            display: none;
        }
        body {
            height: 100vh; /* Full viewport height */
            margin: 0 auto; /* Centers the page horizontally */
            background: url('classroom.jpg') no-repeat center center/cover;
            backdrop-filter: blur(5px);
        }
        </style>
    </head>
    <body class="bg-gray-50 flex items-center justify-center m-0 p-0">
        <div class="max-w-[26rem] w-full max-h-[800px] bg-white p-6 rounded-lg shadow-lg"> <!-- Added max-h-[700px], space-y-6, p-6 -->
            <div class="header">
                <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='../home.php';"></button>
            </div>    
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900">Create Account</h1>
                <p class="mt-2 text-sm text-gray-600">Join us today and explore amazing features</p>
            </div>
            <form id="registrationForm" class="mt-4 space-y-4"> <!-- Adjusted mt-8 to mt-6, space-y-4 -->
                <div class="flex flex-col items-center mb-4"> <!-- Adjusted mb-6 to mb-4 -->
                    <div class="relative w-24 h-24 mb-4"> <!-- Set to w-24 h-24 -->
                        <img id="preview" class="w-full h-full rounded-full object-cover border-4 border-primary" src="user.png" alt="Profile picture">
                        <label for="profilePicture" class="absolute bottom-0 right-0 bg-primary text-white p-2 rounded-full cursor-pointer">
                            <div class="w-6 h-6 flex items-center justify-center">
                                <i class="ri-camera-line"></i>
                            </div>
                        </label>
                        <input type="file" id="profilePicture" name="profile_pic" accept="image/*" class="hidden" onchange="previewImage(event)">
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" id="firstName" name="fname" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-button text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" id="lastName" name="lname" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-button text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="username" name="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-button text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-button text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-button text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <button type="button" onclick="togglePassword('password', 'passwordToggleIcon')" class="absolute right-2 top-1/2 -translate-y-1/2">
                                <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                    <i class="ri-eye-line" id="passwordToggleIcon"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="confirmPassword" name="confirm_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-button text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" oninput="return validatePasswords()">
                            <button type="button" onclick="togglePassword('confirmPassword', 'confirmPasswordToggleIcon')" class="absolute right-2 top-1/2 -translate-y-1/2">
                                <div class="w-6 h-6 flex items-center justify-center text-gray-500">
                                    <i class="ri-eye-line" id="confirmPasswordToggleIcon"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="text-red-500 text-sm mt-2" id="password-error"></div>
                </div>
                <div>
                    <button type="submit" id ="subBtn" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-button text-sm font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary cursor-pointer">
                        Create Account
                    </button>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="../home.php" class="font-medium text-primary hover:text-primary/90">Sign in</a>
                    </p>
                </div>
            </form>
            <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg max-w-sm w-full mx-4">
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full bg-green-100 mx-auto flex items-center justify-center">
                            <i class="ri-check-line text-2xl text-green-500"></i>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Registration Successful!</h3>
                        <p class="mt-2 text-sm text-gray-500">Redirecting to login page...</p>
                        <div class="mt-4 w-full bg-gray-200 rounded-full h-1">
                            <div id="progressBar" class="bg-primary h-1 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="successModal1" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg max-w-sm w-full mx-4">
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-full bg-green-100 mx-auto flex items-center justify-center">
                            <i class="ri-check-line text-2xl text-green-500"></i>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Verification Successful!</h3>
                        <p class="mt-2 text-sm text-gray-500">Sending OTP...
                            <div class="spinner-border mt-2" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function validatePasswords() {
                var password = document.getElementById('password').value;
                var confirmPassword = document.getElementById('confirmPassword').value;
                var errorMsg = document.getElementById('password-error');
                var subBtn = document.getElementById('subBtn');
                errorMsg.textContent = 'Passwords do not match';

                if (password !== confirmPassword) {
                    errorMsg.style.display = 'block';
                    subBtn.style.display = 'none';
                    return false;
                } else {
                    errorMsg.style.display = 'none';
                    subBtn.style.display = 'block';
                    return true;
                }
            }
            function previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('preview').src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            }
            function togglePassword(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'ri-eye-off-line';
                } else {
                    input.type = 'password';
                    icon.className = 'ri-eye-line';
                }
            }
            document.getElementById("registrationForm").addEventListener("submit", function (event) {
                event.preventDefault(); // ✅ Stop form from reloading page
                //loadScreenOTP();
                let formData = new FormData(this);

                fetch("verify_email.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Response:", data); // Debugging
                    //const sentOtp = data.message;                    
                    const errorDiv = document.getElementById("password-error");
                    
                    if (data.status.trim() === "success") {

                        loadScreenOTP();

                        fetch("otp.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Response:", data); // Debugging
                            const sentOtp = data.message;
                            closeScreenOTP();
                            //const errorDiv = document.getElementById("password-error");
                            
                            if (data.status.trim() === "success") {

                                Swal.fire({
                                    title: 'Verify OTP',
                                    text: 'An OTP has been sent to your email.',
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

                                        fetch("registerDtbase.php", {
                                            method: "POST",
                                            body: formData
                                        })
                                        .then(response => response.text())
                                        .then(data => {
                                            console.log("Response:", data);                                                          
                                            
                                            if (data.trim() === "success") {
                                                successRegister();
                                            } else {
                                                errorDiv.style.display = 'block';
                                                errorDiv.innerHTML = `${data}`;
                                            }
                                        })
                                        .catch(error => {
                                            console.error("Fetch Error:", error);
                                            errorDiv.style.display = 'block';
                                            errorDiv.innerHTML = 'An error occurred. Please try again.';
                                        });
                                    }
                                });
                            } else {
                                errorDiv.style.display = 'block';
                                errorDiv.innerHTML = `${data.message}`;
                            }
                        })
                        .catch(error => {
                            console.error("Fetch Error:", error);
                            errorDiv.style.display = 'block';
                            errorDiv.innerHTML = 'Aan error occurred. Please try again.';
                        });
                        /*fetch("otp.php", { method: "POST", body: formData })
                        .then(response => response.text())
                        .then(text => {
                            console.log("Raw Response:", text);
                        })
                        .catch(err => console.error("Fetch Error:", err));*/
                       
                    } else {
                        errorDiv.style.display = 'block';
                        errorDiv.innerHTML = `${data.message}`;
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    errorDiv.style.display = 'block';
                    errorDiv.innerHTML = 'An error occurred. Please try again.';
                });
            });
        
            function loadScreenOTP(){
                const successModal = document.getElementById('successModal1');
                successModal.classList.remove('hidden');
            }
            function closeScreenOTP(){
                const successModal = document.getElementById('successModal1');
                successModal.classList.add('hidden');
            }
            function successRegister(){
                const successModal = document.getElementById('successModal');
                const progressBar = document.getElementById('progressBar');
                successModal.classList.remove('hidden');
                let width = 0;
                const interval = setInterval(() => {
                    if (width >= 100) {
                        clearInterval(interval);
                        window.location.href = '../home.php';
                    } else {
                        width += 2;
                        progressBar.style.width = width + '%';
                    }
                }, 30);
            }  
        </script>
    </body>
</html>