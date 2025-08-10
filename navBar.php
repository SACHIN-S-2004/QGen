<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
<script>
    tailwind.config = {
    theme: {
        extend: {
        colors: {
            primary: "#2563EB",
            secondary: "#60A5FA",
        },
        borderRadius: {
            none: "0px",
            sm: "4px",
            DEFAULT: "8px",
            md: "12px",
            lg: "16px",
            xl: "20px",
            "2xl": "24px",
            "3xl": "32px",
            full: "9999px",
            button: "8px",
        },
        },
    },
    };
</script>

<style>
    :where([class^="ri-"])::before { content: "\f3c2"; }
    .gradient-bg {
    background: linear-gradient(135deg, rgba(37,99,235,0.1) 0%, rgba(96,165,250,0.1) 100%);
    }
    .hover-scale {
    transition: transform 0.2s ease-in-out;
    }
    .hover-scale:hover {
    transform: scale(1.02);
    }
    .hover-lift {
    transition: transform 0.2s ease-in-out;
    }
    .hover-lift:hover {
    transform: translateY(-4px);
    }
    .hover-shadow {
    transition: box-shadow 0.2s ease-in-out;
    }
    .hover-shadow:hover {
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    a, button {
    transition: all 0.2s ease-in-out;
    }
    .split {
    /*background-color: rgb(37 99 235 / var(--tw-bg-opacity, 1));*/
    border:1;
    color: black;
    border-radius: 5px;
    text-decoration: none;
    cursor: pointer;
    margin-right:5px;
    position: relative; 
    align:right;
    }
    .split:hover .dropdown-content {
    display: block; 
    }
    .dropdown-content {
    display: none;
    position: absolute;
    right: 0; 
    top:40px;
    background-color: #f9f9f9;
    min-width: 200px;
    width:280px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    border-radius: 5px;
    }
    .dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    }
    .dropdown-content a:hover {
    /*background-color: #f1f1f1;*/
    color: blue;
    }
    body{
    font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
    }
    .tab {
    opacity: 0; /* Start invisible */
    transform: translateX(100vw); /* Start off-screen to the right */
    transition: transform 0.8s ease-out, opacity 0.8s ease-out; /* Smooth transition */
    }

    /* Show state for the tabs */
    .tab.show {
    opacity: 1; /* Fully visible */
    transform: translateX(0); /* Move to original position */
    }

    /* Ensure tabs are hidden initially but still in the layout */
    .tab.hidden {
    display: inline-block; /* Keep them in the flow */
    }
</style>
<div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[1001] flex justify-end items-start">
    <div class="bg-white p-6 rounded-lg max-w-sm w-full shadow-lg mt-4 mr-4">
        <div class="text-center">
            <div class="w-12 h-12 rounded-full bg-green-100 mx-auto flex items-center justify-center">
                <i class="ri-check-line text-2xl text-green-500"></i>
            </div>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Logging Out...</h3>
            <p class="mt-2 text-sm text-gray-500">Thanks for using QGen!</p>
            <div class="mt-4 w-full bg-gray-200 rounded-full h-1">
                <div id="progressBar" class="bg-primary h-1 rounded-full" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>
<nav class="fixed top-0 w-full bg-white shadow-sm z-[1000]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <img src="logo.png" alt="Logo" class="img-fluid h-12 w-auto flex-shrink-0">
                <div class="hidden md:flex items-center space-x-8 ml-10">
                <a href="homepage.php" class="tab text-gray-900 hover:text-primary" id="tab1">Home</a>
                <a href="createPaper.php" class="tab text-gray-600 hover:text-primary" id="tab2">Create Paper</a>
                <a href="history.php" class="tab text-gray-600 hover:text-primary" id="tab3">My Papers</a>
                <a href="completedWork.php" class="tab text-gray-600 hover:text-primary" id="tab4">Generate Paper</a>
                </div>
            </div>
            <div class="split hover:text-primary/90 h-10 top-3" id="login-menu">
                <div class="flex">
                    <p id="cookieValue" style="padding: 10px 15px;"></p>
                    <div>
                        <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200">
                            <img id="profileImage" src="" alt="Profile Picture" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <i class="ri-arrow-down-s-line" style="padding: 10px 15px;"></i>  
                </div>
                <div class="dropdown-content" id="dropdown">
                    <a href="viewProfile/viewProfile.php"><b>View Profile</b></a>
                    <a href="trash/recycle_bin.php"><b>Recycle Bin</b></a>
                    <a href="difficulty/difficulty.php"><b>Set Diffculty Pattern</b></a>
                    <a href="chapter/chapter.php"><b>Set Module Pattern</b></a>
                    <a href="" onclick="logout(); return false;"><b>Logout</b></a>
                    <!--<a href="Registration.php"><b>Register</b></a>-->
                </div>
            </div>
        </div>
    </div>
</nav>
<script>
    function logout(){
        document.cookie = "user_id=; expires=" + new Date(0).toUTCString() + "; path=/";
        document.cookie = "username=; expires=" + new Date(0).toUTCString() + "; path=/";
        const successModal = document.getElementById('successModal');
        const progressBar = document.getElementById('progressBar');
        successModal.classList.remove('hidden');
        let width = 0;
        const interval = setInterval(() => {
            if (width >= 100) {
                clearInterval(interval);
                window.location.href = 'home.php';
            } else {
                width += 2;
                progressBar.style.width = width + '%';
            }
        }, 30);

        //window.location.href = "home.php";
    }
    
    function appearTab() {
        const tab1 = document.getElementById("tab1");
        const tab2 = document.getElementById("tab2");
        const tab3 = document.getElementById("tab3");
        const tab4 = document.getElementById("tab4");
        const tab5 = document.getElementById("login-menu"); // New variable for tab5
        
        // Remove 'hidden' class and add 'show' class to make tabs appear
        [tab1, tab2, tab3, tab4, tab5].forEach(tab => {
            tab.classList.remove("hidden"); // Ensure it's visible
            tab.classList.add("show");     // Trigger animation
        });
    }
    
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
    
    /*document.getElementById("show-tabs-button").addEventListener("click", function () {
        
    });*/
    
    document.addEventListener("DOMContentLoaded", function () {
        const userCookie = getCookie('username'); // Replace 'user_id' with your cookie name
        document.getElementById('cookieValue').textContent = userCookie ? userCookie : 'user';
        appearTab();
        fetchUserData();
    });

    function fetchUserData() {
    fetch('viewProfile/fetch_user_data.php?section=helo', { credentials: 'include' })
        .then(response => response.json())
        .then(data => {
        if (data.success) {
            populateUserData(data.data);
        } else {
            showNotification(data.message || 'Failed to load user data', 'error');
        }
        })
        .catch(error => {
        console.error('Error fetching user data:', error);
        showNotification('Failed to load user data', 'error');
        });
    }

    function populateUserData(data) {
        document.getElementById('profileImage').src = data.profile_pic ? `data:image/jpeg;base64,${data.profile_pic}` : 'registration/user.png'; // Replace with your default URL
        
    }
</script>