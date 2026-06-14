<?php
include_once 'db.php';

// Dynamic Meta Tags Logic
$page_title = "News Portal - Latest Breaking News";
$meta_desc = "Your trusted source for the latest news, politics, sports, and entertainment updates.";

// If viewing a single news article, update title/desc
if(isset($news) && is_array($news)) {
    $page_title = $news['title'] . " - News Portal";
    $meta_desc = limit_words($news['description'], 20); // 20 words approx 160 chars
}
// If viewing a category
elseif(isset($category) && is_array($category)) {
    $page_title = $category['category_name'] . " News - News Portal";
    $meta_desc = "Latest news and updates from " . $category['category_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;800&family=Noto+Sans+Bengali:wght@400;700&family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('css/style.css'); ?>">
</head>
<body>

<!-- Top Bar -->
<div class="top-bar py-1">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="date-info">
            <i class="far fa-calendar-alt me-1 text-primary-light"></i>
            <small><?php echo date('l, F j, Y'); ?></small>
        </div>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top main-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold fs-2 d-flex align-items-center" href="<?php echo base_url(); ?>">
            <img src="<?php echo base_url('logo.png'); ?>" alt="Logo" height="50" class="me-2">
            <span class="text-secondary brand-style">আলফা</span><span class="text-primary brand-style">নিউজ</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo base_url(); ?>">হোম</a>
                </li>
                <?php
                // Fetch all categories
                $cat_sql = "SELECT * FROM categories";
                $cat_result = $conn->query($cat_sql);
                $categories = [];
                while($row = $cat_result->fetch_assoc()) {
                    $categories[] = $row;
                }

                $show_limit = 5;
                $count = 0;

                // Show first few categories directly
                foreach($categories as $cat):
                    if($count < $show_limit):
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('category.php?id='.$cat['id']); ?>"><?php echo $cat['category_name']; ?></a>
                </li>
                <?php 
                    endif;
                    $count++;
                endforeach; 
                ?>

                <?php if(count($categories) > $show_limit): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">অন্যান্য বিভাগ</a>
                    <ul class="dropdown-menu shadow-lg border-0">
                        <?php 
                        for($i = $show_limit; $i < count($categories); $i++): 
                            $extra_cat = $categories[$i];
                        ?>
                        <li><a class="dropdown-item" href="<?php echo base_url('category.php?id='.$extra_cat['id']); ?>"><?php echo $extra_cat['category_name']; ?></a></li>
                        <?php endfor; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            
            <div class="d-flex align-items-center gap-3">
                <form class="search-form d-flex" action="<?php echo base_url('search.php'); ?>" method="GET">
                    <div class="input-group">
                        <input type="search" name="q" class="form-control" placeholder="Search..." aria-label="Search">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>

                <!-- Dark Mode Toggle -->
                <div class="theme-switch-wrapper">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Ticker -->
<div class="ticker-section">
    <div class="container d-flex align-items-center">
        <div class="ticker-label bg-danger text-white px-3 py-1">
            <i class="fas fa-bolt me-2"></i>ব্রেকিং নিউজ
        </div>
        <div class="ticker-wrap flex-grow-1 overflow-hidden ms-3">
            <div class="ticker-content">
                <?php
                $ticker_sql = "SELECT title FROM news ORDER BY created_at DESC LIMIT 5";
                $ticker_result = $conn->query($ticker_sql);
                if($ticker_result->num_rows > 0) {
                    while($row = $ticker_result->fetch_assoc()) {
                        echo '<span class="ticker-item me-5"><i class="fas fa-circle-dot me-2 text-danger" style="font-size: 0.5rem;"></i>' . $row['title'] . '</span>';
                    }
                } else {
                    echo '<span class="ticker-item me-5">Welcome to NewsPeper - Your daily dose of latest news updates.</span>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
