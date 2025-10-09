<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <i class="fa-brands fa-elementor"></i> -->
    <title>Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/Event/assets/css/styles.css">
</head>
<body>
    <div class="site-wrapper">
        <header>
            <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #0f4c75;">
                <div class="container">
                    <a class="navbar-brand" href="/Event/index.php">
                        <img src="/Event/assets/images/logo.png" alt="logo" height="50px" width="50px"> VibeVent
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse fs-5 " id="navbarNav">
                        <ul class="navbar-nav ms-auto ">
                            <li class="nav-item">
                                <a class="nav-link" href="/Event/index.php">Home</a>
                            </li>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="/Event/admin/dashboard.php">Admin Dashboard</a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Event/booking/my_bookings.php">My Bookings</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Event/auth/logout.php">Logout</a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Event/auth/login.php">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/Event/auth/register.php">Register</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main class="content-wrapper container mt-5 pt-5">