<?php
include 'auth_session.php';
include '../db.php';

// Get current page name for dynamic title
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Dashboard';
switch($current_page) {
    case 'news.php': $page_title = 'News Management'; break;
    case 'categories.php': $page_title = 'Categories'; break;
    case 'users.php': $page_title = 'Manage Users'; break;
    case 'comments.php': $page_title = 'Manage Comments'; break;
    case 'contacts.php': $page_title = 'Contact Messages'; break;
    case 'ads.php': $page_title = 'Manage Ads'; break;
    case 'logs.php': $page_title = 'Activity Logs'; break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $page_title; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #064e3b; /* Dark Green */
            --sidebar-hover: #065f46;
            --primary-accent: #f42a41; /* Red */
            --primary-green: #006a4e;
        }
        .bg-primary { background-color: var(--primary-green) !important; }
        .text-primary { color: var(--primary-green) !important; }
        .btn-primary { background-color: var(--primary-green); border-color: var(--primary-green); }
        .btn-primary:hover { background-color: #004d39; border-color: #004d39; }
        body { background-color: #f4f6f9; }
        .sidebar { 
            min-height: 100vh; 
            background: var(--sidebar-bg); 
            color: white; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .sidebar .brand {
            padding: 20px 15px;
            font-size: 1.25rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        .sidebar a { 
            color: rgba(255,255,255,0.7); 
            text-decoration: none; 
            display: block; 
            padding: 12px 20px; 
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        .sidebar a:hover { 
            background: var(--sidebar-hover); 
            color: white; 
        }
        .sidebar a.active { 
            background: var(--sidebar-hover); 
            color: white; 
            border-left: 4px solid var(--primary-accent);
        }
        .sidebar i { width: 25px; }
        .content { padding: 30px; }
        .navbar {
            padding: 15px 30px;
            border-bottom: 1px solid #dee2e6;
        }
        .card { border-radius: 10px; }
        .table-responsive { background: white; border-radius: 10px; padding: 15px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-none d-md-block" style="width: 260px; flex-shrink: 0;">
        <div class="brand">
            <i class="fas fa-newspaper me-2"></i> NewsPeper
        </div>
        <ul class="list-unstyled">
            <li><a href="dashboard.php" class="<?php echo $current_page=='dashboard.php'?'active':''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="news.php" class="<?php echo $current_page=='news.php'?'active':''; ?>"><i class="far fa-newspaper"></i> News</a></li>
            <li><a href="categories.php" class="<?php echo $current_page=='categories.php'?'active':''; ?>"><i class="fas fa-list"></i> Categories</a></li>
            <?php if($_SESSION['role'] == 'admin'): ?>
            <li><a href="users.php" class="<?php echo $current_page=='users.php'?'active':''; ?>"><i class="fas fa-users"></i> Manage Users</a></li>
            <?php endif; ?>
            <li><a href="comments.php" class="<?php echo $current_page=='comments.php'?'active':''; ?>"><i class="fas fa-comments"></i> Manage Comments</a></li>
            <li><a href="contacts.php" class="<?php echo $current_page=='contacts.php'?'active':''; ?>"><i class="fas fa-envelope"></i> Messages</a></li>
            <?php if($_SESSION['role'] == 'admin'): ?>
            <li><a href="ads.php" class="<?php echo $current_page=='ads.php'?'active':''; ?>"><i class="fas fa-ad"></i> Manage Ads</a></li>
            <li>
                <a href="logs.php" class="<?php echo $current_page=='logs.php'?'active':''; ?> d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-clipboard-list"></i> Activity Logs</span>
                    <?php 
                    $unseen_logs = $conn->query("SELECT COUNT(*) FROM activity_logs WHERE is_seen=0")->fetch_row()[0];
                    if($unseen_logs > 0): 
                    ?>
                    <span class="badge bg-danger rounded-pill" style="font-size: 0.7rem;"><?php echo $unseen_logs; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>
            <li class="mt-4 border-top border-secondary pt-2">
                <a href="../" target="_blank"><i class="fas fa-globe"></i> Visit Site</a>
                <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1">
        <nav class="navbar navbar-expand navbar-light bg-white sticky-top">
            <div class="container-fluid p-0">
                <h4 class="mb-0 text-dark"><?php echo $page_title; ?></h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-muted d-none d-sm-inline me-3">
                        <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user_name']; ?>
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-link text-dark dropdown-toggle p-0" type="button" data-bs-toggle="dropdown">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="users.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div class="content">
