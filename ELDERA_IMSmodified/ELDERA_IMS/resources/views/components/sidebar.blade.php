<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM"
    crossorigin="anonymous"
  />
  <!-- Cache Manager -->
  <script src="{{ asset('js/cache-manager.js') }}" defer></script>
  
  <!-- Service Worker Registration -->
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
          .then(function(registration) {
            console.log('ServiceWorker registration successful');
          })
          .catch(function(err) {
            console.log('ServiceWorker registration failed: ', err);
          });
      });
    }
  </script>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f5faff;
    }
    .sidebar {
      width: 250px;
      background: #222;
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px 0;
    }
    .sidebar img {
      display: block;
      margin: 0 auto;
      width: 100px;
    }
    .sidebar h2 {
      text-align: center;
      font-size: 14px;
      margin: 10px 0;
    }
    .sidebar-header {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      margin-bottom: 30px;
    }
    .sidebar-logos {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 18px;
     
    }
    .sidebar-logo {
      width: 60px;
      height: auto;
      border: 2px solid #e31575;
        border-radius: 50%;
        background-color: #e31575;
        object-fit: cover;
    }
    .sidebar-title {
      font-size: 18px;
      font-weight: 900;
      color: #e31575;
      line-height: 1.1;
      text-align: center;
    }
    .sidebar-subtitle {
      color: #ffffff;
      font-size: 13px;
      font-weight: 800;
    }
    .sidebar ul {
      list-style: none;
      padding: 0;
    }
    .sidebar ul li {
      padding: 14px 25px;
      cursor: pointer;
      font-size: 14px;
    }
    .sidebar a {
      color: inherit;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 12px;
      width: 100%;
    }
    .sidebar a:hover {
      color: inherit;
      text-decoration: none;
    }
    .sidebar li.active {
      background: #e31575;
      color: black;
      font-weight: bold;
    }
    .sidebar li:hover {
      background: #ffb7ce;
      color: #111;
      font-weight: bold;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: white;
      padding: 20px;
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      height: 30px;
      z-index: 1;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .main {
      margin-left: 250px;
      padding: 70px 20px 20px;
    }
    .footer {
      width: 100%;
      background: #fff;
      padding: 18px 0 10px;
      display: flex;
      justify-content: center;
      align-items: center;
      box-shadow: 0 -1px 4px #0001;
    }
    .footer-logo {
      height: 38px;
      display: block;
      margin: 0 auto;
    }
    .search {
      border: 1px solid #ccc;
      padding: 5px 10px;
      border-radius: 5px;
      width: 300px;
    }

    /* Submenu Styles */
    .submenu-toggle {
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
    }
    .submenu-toggle .toggle-icon {
      transition: transform 0.3s ease;
    }
    .submenu-toggle.active .toggle-icon {
      transform: rotate(90deg);
    }
    .submenu {
        margin-top: 10px;
      max-height: 0;
      width: 225px;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }
    .submenu.active {
      max-height: 500px;
    }
    .submenu li {
      margin-left: -10px;
      font-size: 14px;
    }
    .submenu li:hover {
      background: #e31575;
    }
    .submenu li.active {
      background: #333;
    }
    .submenu a {
      color: #000000;
    }
    .submenu li:hover a {
      color: #111;
    }
    .submenu li.active a {
      color: #ffffff;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logos">
        <img src="{{ asset('images/LCSCF_Logo.png') }}" alt="Logo" class="sidebar-logo" />
      </div>
      <div class="sidebar-title">
        SENIOR CITIZEN<br />
        <span class="sidebar-subtitle">INFORMATION<br />MANAGEMENT SYSTEM</span>
      </div>
    </div>

    <ul>
      <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}">
          <i class="fas fa-th-large"></i> Dashboard
        </a>
      </li>
      <li class="{{ request()->routeIs('add_new_senior') ? 'active' : '' }}">
        <a href="{{ route('add_new_senior') }}"><i class="fas fa-user-plus"></i> Add New Senior</a>
      </li>
      <li class="{{ request()->routeIs(['seniors', 'view_senior', 'edit_senior', 'seniors.benefits', 'seniors.pension', 'seniors.id-applications']) ? 'active' : '' }}">
        <a href="{{ route('seniors') }}"><i class="fas fa-users"></i> Senior Citizen</a>
      </li>

      <li id="applicationFormsMenu" class="{{ request()->routeIs(['form_existing_senior', 'form_pension', 'form_seniorID']) ? 'active' : '' }}">
        <div class="submenu-toggle" onclick="toggleSubmenu(this)">
          <div style="display: flex; align-items: center; gap: 12px; ">
            <i class="fas fa-clipboard"></i> Application Forms
          </div>
          <i class="fas fa-chevron-right toggle-icon"></i>
        </div>
        <ul class="submenu" onclick="event.stopPropagation()">
          <li class="{{ request()->routeIs('form_existing_senior') ? 'active' : '' }}">
            <a href="{{ route('form_existing_senior') }}"><i class="fas fa-user-check"></i> ONCBP</a>
          </li>
          <li class="{{ request()->routeIs('form_pension') ? 'active' : '' }}">
            <a href="{{ route('form_pension') }}"><i class="fas fa-hand-holding-usd"></i> Social Pension</a>
          </li>
          <li class="{{ request()->routeIs('form_seniorID') ? 'active' : '' }}">
            <a href="{{ route('form_seniorID') }}"><i class="fas fa-id-card"></i> Senior ID</a>
          </li>
        </ul>
      </li>

      
      <li class="{{ request()->routeIs('events') ? 'active' : '' }}">
        <a href="{{ route('events') }}"><i class="fas fa-calendar-alt"></i> Events</a>
      </li>
      <li class="{{ request()->routeIs('admin.password-reset-requests.*') ? 'active' : '' }}">
        <a href="{{ route('admin.password-reset-requests.index') }}"><i class="fas fa-key"></i> Password Reset Requests</a>
      </li>
    </ul>
  </div>

  @if(isset($slot))
    {{ $slot }}
  @endif

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <script>
    function toggleSubmenu(element) {
      const submenu = element.nextElementSibling;
      const toggleIcon = element.querySelector('.toggle-icon');

      if (!submenu || !submenu.classList.contains('submenu')) return;

      submenu.classList.toggle('active');
      element.classList.toggle('active');

      const parentLi = element.closest('li');
      if (parentLi) parentLi.classList.toggle('active');

      // Close siblings
      const siblings = Array.from(parentLi.parentElement.children);
      siblings.forEach(sibling => {
        if (sibling !== parentLi) {
          sibling.classList.remove('active');
          const sub = sibling.querySelector('.submenu');
          const toggle = sibling.querySelector('.submenu-toggle');
          const icon = sibling.querySelector('.toggle-icon');
          if (sub) sub.classList.remove('active');
          if (toggle) toggle.classList.remove('active');
          if (icon) icon.classList.remove('active');
        }
      });
    }

    document.addEventListener('DOMContentLoaded', function () {
      const currentRoute = '{{ request()->route()->getName() }}';
      const applicationFormRoutes = ['form_existing_senior', 'form_pension', 'form_seniorID'];

      // Handle Application Forms submenu
      if (applicationFormRoutes.includes(currentRoute)) {
        const toggle = document.querySelector('#applicationFormsMenu .submenu-toggle');
        const submenu = document.querySelector('#applicationFormsMenu .submenu');
        if (toggle && submenu) {
          toggle.classList.add('active');
          submenu.classList.add('active');
          document.getElementById('applicationFormsMenu').classList.add('active');
        }
      }
    });
  </script>
</body>
</html>
