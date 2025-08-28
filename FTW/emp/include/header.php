<?php 
session_start(); 
$id = $_SESSION['id'];
$name = $_SESSION['name'];
?>

<header class="app-header">
  <!-- Application Logo -->
  <a class="app-header__logo" href="find.php">FTW</a>

  <!-- Sidebar Toggle Button -->
  <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>

  <!-- Navbar Right Menu -->
  <ul class="app-nav">
    <!-- User Menu -->
    <li class="dropdown">
      <a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu">
        <i class="fa fa-user fa-lg"></i>
        <!-- <b>Welcome Back </b>:- <?php echo $name . ' ' . $id; ?> -->
        <b>Welcome Back </b>:- <?php echo $name ?>
      </a>

      <!-- Dropdown Menu -->
      <ul class="dropdown-menu settings-menu dropdown-menu-right">
        <li>
          <a class="dropdown-item" href="../Profile/change-password.php">
            <i class="fa fa-cog fa-lg"></i> Change Password
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="../Profile/my-profile.php">
            <i class="fa fa-user fa-lg"></i> Profile
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="../logout.php">
            <i class="fa fa-sign-out fa-lg"></i> Logout
          </a>
        </li>
      </ul>
    </li>
  </ul>
</header>
