<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- <header>
        <div class="header-container">
            <div class="logo">
                <a href="<?php echo SITE_URL; ?>">
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="BookSwap Logo">
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>/pages/dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/browse.php"><i class="fas fa-book"></i> Browse Books</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo SITE_URL; ?>/pages/add_book.php"><i class="fas fa-plus-circle"></i> Add Book</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo SITE_URL; ?>/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header> -->

    <header>
        <div class="logo">
            <a href="<?php echo SITE_URL; ?>">
                <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="BookSwap Logo">
            </a>
        </div>
        <div class="hamburger" onclick="toggleMenu()">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        </div>
        <nav class="nav-menu">
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/pages/dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?php echo SITE_URL; ?>/pages/browse.php"><i class="fas fa-book"></i> Browse Books</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/add_book.php"><i class="fas fa-plus-circle"></i> Add Book</a></li>
                <li><a href="<?php echo SITE_URL; ?>/pages/messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="<?php echo SITE_URL; ?>/pages/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                 <li><a href="<?php echo SITE_URL; ?>/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                <li><a href="<?php echo SITE_URL; ?>/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="<?php echo SITE_URL; ?>/auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                    <?php endif; ?>
            </ul>
        </nav>
</header>
    <main class="container">