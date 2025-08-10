<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <script src="https://cdn.tailwindcss.com/3.4.16"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { primary: '#4F46E5', secondary: '#6B7280' },
          borderRadius: { 'button': '8px' }
        }
      }
    }
  </script>
  <style>
    .edit-mode { border: 2px solid #4F46E5; }
    .edit-icon { opacity: 0.5; transition: opacity 0.2s; }
    .edit-icon:hover { opacity: 1; }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 py-8 max-w-3xl">
  <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='../homepage.php';"></button>
    <div class="text-center mb-8">
      <div class="relative inline-block group">
        <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 mx-auto">
          <img id="profileImage" src="" alt="Profile Picture" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity cursor-pointer">
          <i class="ri-camera-line text-white text-2xl"></i>
        </div>
      </div>
      <h2 id="fullNameDisplay" class="text-2xl font-semibold mt-4"></h2>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h3 class="text-xl font-semibold mb-6">Personal Information</h3>
      <div class="space-y-6">
        <div class="relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            <div class="flex items-center">
              <div class="w-5 h-5 flex items-center justify-center mr-2"><i class="ri-user-line text-primary"></i></div>
              First Name
            </div>
          </label>
          <div class="flex items-center">
            <input type="text" id="fname" class="w-full px-4 py-2 rounded border-gray-300 focus:outline-none pl-10" disabled>
            <div class="w-8 h-8 flex items-center justify-center ml-2 cursor-pointer edit-icon" onclick="toggleEdit('fname')"><i class="ri-pencil-line text-gray-600"></i></div>
          </div>
          <div id="fname-buttons" class="hidden mt-2 space-x-2">
            <button onclick="saveField('fname')" class="px-4 py-2 bg-primary text-white rounded-button">Save</button>
            <button onclick="cancelEdit('fname')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button">Cancel</button>
          </div>
        </div>
        <div class="relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            <div class="flex items-center">
              <div class="w-5 h-5 flex items-center justify-center mr-2"><i class="ri-user-line text-primary"></i></div>
              Last Name
            </div>
          </label>
          <div class="flex items-center">
            <input type="text" id="lname" class="w-full px-4 py-2 rounded border-gray-300 focus:outline-none pl-10" disabled>
            <div class="w-8 h-8 flex items-center justify-center ml-2 cursor-pointer edit-icon" onclick="toggleEdit('lname')"><i class="ri-pencil-line text-gray-600"></i></div>
          </div>
          <div id="lname-buttons" class="hidden mt-2 space-x-2">
            <button onclick="saveField('lname')" class="px-4 py-2 bg-primary text-white rounded-button">Save</button>
            <button onclick="cancelEdit('lname')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button">Cancel</button>
          </div>
        </div>
        <div class="relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            <div class="flex items-center">
              <div class="w-5 h-5 flex items-center justify-center mr-2"><i class="ri-user-3-line text-primary"></i></div>
              Username
            </div>
          </label>
          <div class="flex items-center">
            <input type="text" id="username" class="w-full px-4 py-2 rounded bg-gray-100 text-gray-600 pl-10" disabled>
          </div>
        </div>
        <div class="relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">
            <div class="flex items-center">
              <div class="w-5 h-5 flex items-center justify-center mr-2"><i class="ri-mail-line text-primary"></i></div>
              Email
            </div>
          </label>
          <div class="flex items-center">
            <input type="email" id="email" class="w-full px-4 py-2 rounded border-gray-300 focus:outline-none pl-10" disabled>
            <div class="w-8 h-8 flex items-center justify-center ml-2 cursor-pointer edit-icon" onclick="toggleEdit('email')"><i class="ri-pencil-line text-gray-600"></i></div>
          </div>
          <div id="email-buttons" class="hidden mt-2 space-x-2">
            <button onclick="saveField('email')" class="px-4 py-2 bg-primary text-white rounded-button">Save</button>
            <button onclick="cancelEdit('email')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button">Cancel</button>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
      <h3 class="text-xl font-semibold mb-6">
        <div class="flex items-center">
          <div class="w-6 h-6 flex items-center justify-center mr-2"><i class="ri-lock-line text-primary"></i></div>
          Password Security
        </div>
      </h3>
      <div class="flex items-center">
        <button onclick="showPasswordModal()" class="px-4 py-2 bg-primary text-white rounded-button flex items-center">
          <div class="w-5 h-5 flex items-center justify-center mr-2"><i class="ri-lock-password-line text-white"></i></div>
          Change Password
        </button>
      </div>
    </div>
  </div>
  <div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
  <div class="bg-white rounded-lg p-6 w-96">
    <h3 class="text-lg font-semibold mb-4">Upload Profile Picture</h3>
    <form id="uploadForm" enctype="multipart/form-data" class="space-y-4">
      <input type="file" class="form-control" id="profilePicInput" name="profile_pic" accept="image/png, image/jpeg" class="w-full px-3 py-2 border rounded-lg">
      <div class="flex justify-end space-x-3">
        <button type="button" onclick="closeUploadModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-button">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-indigo-600">Upload</button>
      </div>
    </form>
  </div>
</div>
  <div id="notification" class="fixed top-4 right-0 bg-green-500 text-white px-6 py-3 rounded shadow-lg transform transition-transform duration-300 translate-x-full "></div>
  <script>
    let originalValues = {};
    let activeField = null;

    document.addEventListener('DOMContentLoaded', () => {
      fetchUserData();
      setupProfileImageUpload();
    });

    function fetchUserData() {
      fetch('fetch_user_data.php?viewProfile=1', { credentials: 'include' })
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
      document.getElementById('profileImage').src = data.profile_pic ? `data:image/jpeg;base64,${data.profile_pic}` : '../registration/user.png'; // Replace with your default URL
      document.getElementById('fname').value = data.fname || '';
      document.getElementById('lname').value = data.lname || '';
      document.getElementById('username').value = data.username || '';
      document.getElementById('email').value = data.email || '';
      document.getElementById('fullNameDisplay').textContent = `${data.fname || ''} ${data.lname || ''}`.trim();
      Object.keys(data).forEach(key => originalValues[key] = data[key]);
    }

    function toggleEdit(fieldId) {
      if (activeField && activeField !== fieldId) cancelEdit(activeField);
      const input = document.getElementById(fieldId);
      const buttons = document.getElementById(`${fieldId}-buttons`);
      if (input.disabled) {
        input.disabled = false;
        input.classList.add('edit-mode');
        buttons.classList.remove('hidden');
        activeField = fieldId;
      }
    }

    function saveField(fieldId) {
      const input = document.getElementById(fieldId);
      const value = input.value.trim();
      if (!value) return showNotification('Field cannot be empty', 'error');

      const formData = new FormData();
      formData.append('field', fieldId);
      formData.append('value', value);

      fetch('update_user_data.php', { method: 'POST', body: formData, credentials: 'include' })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            updateUIAfterSave(fieldId, value);
            showNotification('Changes saved successfully');
          } else {
            showNotification(data.message || 'Failed to save changes', 'error');
          }
        })
        .catch(error => {
          console.error('Error saving data:', error);
          showNotification('Failed to save changes', 'error');
        });
    }

    function updateUIAfterSave(fieldId, value) {
      originalValues[fieldId] = value;
      if (fieldId === 'fname' || fieldId === 'lname') {
        document.getElementById('fullNameDisplay').textContent = `${originalValues.fname || ''} ${originalValues.lname || ''}`.trim();
      }
      const input = document.getElementById(fieldId);
      input.disabled = true;
      input.classList.remove('edit-mode');
      document.getElementById(`${fieldId}-buttons`).classList.add('hidden');
      activeField = null;
    }

    function cancelEdit(fieldId) {
      const input = document.getElementById(fieldId);
      input.value = originalValues[fieldId] || '';
      input.disabled = true;
      input.classList.remove('edit-mode');
      document.getElementById(`${fieldId}-buttons`).classList.add('hidden');
      activeField = null;
    }

    function setupProfileImageUpload() {
      document.querySelector('.group').addEventListener('click', () => {
        document.getElementById('uploadModal').style.display = 'flex';
      });

      document.getElementById('uploadForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const fileInput = document.getElementById('profilePicInput');
        console.log(fileInput.files); // Debug: Check if a file is selected
        const file = fileInput.files[0];
        if (!file) return showNotification('Please select an image', 'error');
        if (!['image/png', 'image/jpeg'].includes(file.type)) return showNotification('Only PNG or JPG allowed', 'error');
        if (file.size > 5 * 1024 * 1024) return showNotification('Image must be under 5MB', 'error');

        const formData = new FormData();
        formData.append('profile_pic', file);

        fetch('upload_profile_image.php', { method: 'POST', body: formData, credentials: 'include' })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              document.getElementById('profileImage').src = `data:image/jpeg;base64,${data.profile_pic}`;
              originalValues.profile_pic = data.profile_pic;
              closeUploadModal();
              showNotification('Profile picture updated successfully');
            } else {
              showNotification(data.message || 'Failed to upload image', 'error');
            }
          })
          .catch(error => {
            console.error('Error uploading image:', error);
            showNotification('Failed to upload image', 'error');
          });
      });
    }

    function closeUploadModal() {
      document.getElementById('uploadModal').style.display = 'none';
      document.getElementById('uploadForm').reset();
    }

    function showPasswordModal() {
      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center';
      modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 w-96">
          <h3 class="text-lg font-semibold mb-4">Change Password</h3>
          <form id="passwordForm" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
              <input type="password" name="current_password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
              <input type="password" name="new_password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
              <input type="password" name="confirm_password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
              <button type="button" onclick="this.closest('.fixed').remove()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-button">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-indigo-600">Save Changes</button>
            </div>
          </form>
        </div>
      `;
      document.body.appendChild(modal);

      modal.querySelector('#passwordForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        fetch('update_password.php', { method: 'POST', body: formData, credentials: 'include' })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              modal.remove();
              showNotification('Password updated successfully');
            } else {
              showNotification(data.message || 'Failed to update password', 'error');
            }
          })
          .catch(error => {
            console.error('Error updating password:', error);
            showNotification('Failed to update password', 'error');
          });
      });
    }

    function showNotification(message, type = 'success') {
      const notification = document.getElementById('notification');
      //notification.classList.remove('hidden');
      notification.textContent = message;
      notification.classList.remove('translate-x-full', 'bg-green-500', 'bg-red-500');
      notification.classList.add(type === 'success' ? 'bg-green-500' : 'bg-red-500');
      setTimeout(() => notification.classList.add('translate-x-full'), 3000);
      //notification.classList.add('hidden');
    }
  </script>
</body>
</html>