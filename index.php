<?php include 'header.php'; ?>

<!-- Featured News Section -->
<div class="container my-4">
    <div class="row">
        <!-- Main Featured Article -->
        <div class="col-lg-8">
            <?php
            $featured_sql = "SELECT * FROM news WHERE is_featured=1 AND status='published' ORDER BY created_at DESC LIMIT 1";
            $featured_res = $conn->query($featured_sql);
            if ($featured_res->num_rows > 0):
                $feat = $featured_res->fetch_assoc();
                $img_src = $feat['image'] ? base_url('uploads/'.$feat['image']) : 'https://placehold.co/800x450';
            ?>
            <div class="card text-white bg-dark border-0 main-featured position-relative mb-3">
                <img src="<?php echo $img_src; ?>" class="card-img" alt="<?php echo $feat['title']; ?>">
                <div class="card-img-overlay d-flex flex-column justify-content-end p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                    <h2 class="card-title fw-bold"><a href="<?php echo base_url('news.php?id='.$feat['id']); ?>" class="text-white"><?php echo $feat['title']; ?></a></h2>
                    <p class="card-text d-none d-md-block"><?php echo limit_words($feat['description'], 20); ?></p>
                    <p class="card-text"><small>Last updated <?php echo date('M j, Y', strtotime($feat['created_at'])); ?></small></p>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info">No featured news available.</div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar / Trending -->
        <div class="col-lg-4">
            <h4 class="sidebar-title">ট্রেন্ডিং নিউজ</h4>
            <div class="list-group list-group-flush">
                <?php
                // Trending based on views
                $trending_sql = "SELECT * FROM news WHERE status='published' ORDER BY views DESC LIMIT 5";
                $trending_res = $conn->query($trending_sql);
                while($trend = $trending_res->fetch_assoc()):
                ?>
                <a href="<?php echo base_url('news.php?id='.$trend['id']); ?>" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                    <?php if($trend['image']): ?>
                    <img src="<?php echo base_url('uploads/'.$trend['image']); ?>" alt="twbs" width="60" height="60" class="rounded flex-shrink-0" style="object-fit:cover;">
                    <?php else: ?>
                    <img src="https://placehold.co/60x60" alt="twbs" width="60" height="60" class="rounded flex-shrink-0">
                    <?php endif; ?>
                    <div class="d-flex w-100 justify-content-between">
                        <div>
                            <h6 class="mb-0"><?php echo limit_words($trend['title'], 8); ?></h6>
                            <small class="opacity-50 text-nowrap"><?php echo date('M j', strtotime($trend['created_at'])); ?></small>
                        </div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<!-- Advertisement -->
<div class="container mb-5">
    <?php 
    $ad_content = get_ad('content');
    if($ad_content): 
        echo $ad_content;
    else: 
    ?>
    <div class="bg-light border text-center py-5 text-muted">
        ADVERTISEMENT AREA
    </div>
    <?php endif; ?>
</div>

<!-- Latest News Grid -->
<div class="container mb-5">
    <h3 class="sidebar-title">ব্রেকিং নিউজ</h3>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        $latest_sql = "SELECT news.*, categories.category_name FROM news LEFT JOIN categories ON news.category_id = categories.id WHERE news.status='published' ORDER BY news.created_at DESC LIMIT 6";
        $latest_res = $conn->query($latest_sql);
        while($news = $latest_res->fetch_assoc()):
             $img_src = $news['image'] ? base_url('uploads/'.$news['image']) : 'https://placehold.co/600x400';
        ?>
        <div class="col">
            <div class="card news-card h-100">
                <div class="position-relative">
                    <img src="<?php echo $img_src; ?>" class="card-img-top" alt="...">
                    <span class="category-badge"><?php echo $news['category_name']; ?></span>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a href="<?php echo base_url('news.php?id='.$news['id']); ?>"><?php echo $news['title']; ?></a></h5>
                    <p class="card-text text-muted"><?php echo limit_words($news['description'], 15); ?></p>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <small class="text-muted"><i class="far fa-clock"></i> <?php echo date('M j, Y', strtotime($news['created_at'])); ?></small>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Category Sections (Dynamic) -->
<div class="container mb-5">
    <div class="row g-4">
        <?php
        $main_cat_sql = "SELECT * FROM categories";
        $main_cat_res = $conn->query($main_cat_sql);
        while($cat = $main_cat_res->fetch_assoc()):
            $cat_id = $cat['id'];
            $cat_name = $cat['category_name'];
            
            // Check if there is news in this category
            $cat_news_sql = "SELECT * FROM news WHERE category_id = $cat_id AND status='published' ORDER BY created_at DESC LIMIT 3";
            $cat_news_res = $conn->query($cat_news_sql);
            
            if($cat_news_res->num_rows > 0):
        ?>
        <!-- Category: <?php echo $cat_name; ?> -->
        <div class="col-lg-6 col-md-6">
            <div class="category-section-title">
                <h4><?php echo $cat_name; ?></h4>
                <a href="<?php echo base_url('category.php?id='.$cat_id); ?>" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill">View All</a>
            </div>
            <?php
                while($n = $cat_news_res->fetch_assoc()):
            ?>
            <div class="card mb-3 border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="row g-0">
                    <div class="col-4">
                         <?php $img = $n['image'] ? base_url('uploads/'.$n['image']) : 'https://placehold.co/200x200'; ?>
                        <img src="<?php echo $img; ?>" class="img-fluid" alt="..." style="height: 80px; width: 100%; object-fit: cover;">
                    </div>
                    <div class="col-8">
                        <div class="card-body p-2 px-3">
                            <h6 class="card-title mb-1" style="font-size: 0.9rem; line-height: 1.2;">
                                <a href="<?php echo base_url('news.php?id='.$n['id']); ?>" class="text-decoration-none fw-bold"><?php echo limit_words($n['title'], 7); ?></a>
                            </h6>
                            <p class="card-text mb-0"><small class="text-muted" style="font-size: 0.75rem;"><i class="far fa-calendar-alt me-1"></i><?php echo date('M d, Y', strtotime($n['created_at'])); ?></small></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; endwhile; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
