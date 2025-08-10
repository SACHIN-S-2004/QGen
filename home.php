<?php
  session_start();
?>
<html>
  <head>
    <title>QGen - Question Paper Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS (optional, for interactive elements like modals, tooltips, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
        background-color: rgb(37 99 235 / var(--tw-bg-opacity, 1));
        color: white;
        padding: 10px 15px;
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
        background-color: #f1f1f1;
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
  </head>

  <body class="bg-white font-Courier New">
    <div id="main-content">
      <nav class="fixed top-0 w-full bg-white shadow-sm z-[1000]">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
              <div class="flex items-center">
                <img src="logo.png" alt="Logo" class="img-fluid h-12 w-auto flex-shrink-0">
                <div class="hidden md:flex items-center space-x-8 ml-10">
                  <a href="" class="tab text-gray-900 hover:text-primary" id="tab1">Home</a>
                  <a href="/pools" class="tab text-gray-600 hover:text-primary" id="tab2">Create Paper</a>
                  <a href="/generate" class="tab text-gray-600 hover:text-primary" id="tab3">Generate Paper</a>
                  <a href="/papers" class="tab text-gray-600 hover:text-primary" id="tab4">My Papers</a>
                </div>
              </div>
              <div class="split hover:bg-primary/90 !rounded-button h-10 top-3" id="user-menu">
                <i class="bi bi-person-circle"></i>
                <div class="dropdown-content" id="dropdown">
                  <a href="" onclick="openPage()"><b>Login</b></a>
                  <a href="registration/registration.php"><b>Register</b></a>
                </div>
              </div>
            </div>
          </div>
        </nav>
        <?php include 'content.php'; ?>
    </div>

    <script>
      /*function isCookieSet(cookieName) {
        // Get all cookies and check if the desired cookie exists
        const cookies = document.cookie.split("; ");
        for (let cookie of cookies) {
          const [name] = cookie.split("=");
          if (name === cookieName) {
            return true; // Cookie is set
          }
        }
        return false; // Cookie is not set
      }*/
      document.getElementById("show-tabs-button").addEventListener("click", function () {
        openPage();
      });
      function openPage() {
        //window.location.href = "login.php";
        document.getElementById('main-content').classList.add('blur');
        event.preventDefault();
        var myModal = new bootstrap.Modal(document.getElementById('loginModal'));
        myModal.show();
        //console.log("Cookie is not set.");
        //appearTab();
      }
      function closePage() {
        document.getElementById('main-content').classList.remove('blur');
        var myModalElement = document.getElementById('loginModal');
        var myModal = bootstrap.Modal.getInstance(myModalElement);
        if (!myModal) {
            myModal = new bootstrap.Modal(myModalElement); // Create instance if it doesn’t exist
        }
        myModal.hide();
      }

      document.addEventListener("DOMContentLoaded", function () {
        const loginModal = document.getElementById("loginModal");

        loginModal.addEventListener("show.bs.modal", function () {
            document.getElementById("loginForm").reset(); // ✅ Clears input fields
            document.getElementById("loginError").innerHTML = ""; // ✅ Clears error message
        });
      });

      document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("loginForm").addEventListener("submit", function (event) {
          event.preventDefault(); // ✅ Stop form from reloading page

          let formData = new FormData(this);

          fetch("login/login.php", {
            method: "POST",
            body: formData
          })
          .then(response => response.text())
          .then(data => {
            console.log("Response:", data); // Debugging
            
            const errorDiv = document.getElementById("loginError");
            
            if (data.trim() === "success") {
                errorDiv.innerHTML = '<span class="text-success">Login successful! Redirecting...</span>';
                
                // ✅ Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = "homepage.php";
                }, 2000);
            } else {
                errorDiv.innerHTML = `<span class="text-danger">${data}</span>`;
            }
          })
          .catch(error => {
              console.error("Fetch Error:", error);
              document.getElementById("loginError").innerHTML = '<span class="text-danger">An error occurred. Please try again.</span>';
          });
        });
      });
    </script>
  </body>
</html>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <?php include 'login/login.html'; ?>
    </div>
  </div>
</div>

<!--ALTER TABLE metadata ADD CONSTRAINT fk_parent FOREIGN KEY (qpool) REFERENCES qpool(qpool_id) ON DELETE CASCADE;-->
<!--ALTER TABLE question ADD CONSTRAINT fk_parent1 FOREIGN KEY (qpool_id) REFERENCES qpool(qpool_id) ON DELETE CASCADE;-->