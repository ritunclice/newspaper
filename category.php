<?php
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container my-5'><h3>Invalid Category ID</h3></div>";
    include 'footer.php';
    exit;
}

$cat_id = intval($_GET['id']);
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Get Category Name
$cat_name_sql = "SELECT category_name FROM categories WHERE id = $cat_id";
$cat_name_res = $conn->query($cat_name_sql);
if($cat_name_res->num_rows == 0) {
     echo "<div class='container my-5'><h3>Category not found</h3></div>";
     include 'footer.php';
     exit;
}
$category = $cat_name_res->fetch_assoc();

// Get Total Count for Pagination
$count_sql = "SELECT COUNT(*) as total FROM news WHERE category_id = $cat_id AND status='published'";
$count_res = $conn->query($count_sql);
$total_news = $count_res->fetch_assoc()['total'];
$total_pages = ceil($total_news / $limit);

// Fetch News
$sql = "SELECT * FROM news WHERE category_id = $cat_id AND status='published' ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="mb-4 border-bottom pb-2">Category: <span class="text-primary"><?php echo $category['category_name']; ?></span></h2>
            
            <div class="row row-cols-1 row-cols-md-2 g-4 mb-5">
                <?php
                if ($result->num_rows > 0):
                    while($news = $result->fetch_assoc()):
                        $img_src = $news['image'] ? base_url('uploads/'.$news['image']) : 'https://placehold.co/600x400';
                ?>
                <div class="col">
                    <div class="card news-card h-100">
                        <img src="<?php echo $img_src; ?>" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title"><a href="<?php echo base_url('news.php?id='.$news['id']); ?>"><?php echo $news['title']; ?></a></h5>
                            <p class="card-text text-muted"><?php echo limit_words($news['description'], 15); ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <small class="text-muted"><i class="far fa-clock"></i> <?php echo date('M j, Y', strtotime($news['created_at'])); ?></small>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else:
                    echo "<div class='col-12'><p>No news found in this category.</p></div>";
                endif;
                ?>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php echo base_url("category.php?id=$cat_id&page=".($page-1)); ?>">Previous</a>
                    </li>
                    <?php for($i=1; $i<=$total_pages; $i++): ?>
                    <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                        <a class="page-link" href="<?php echo base_url("category.php?id=$cat_id&page=$i"); ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php echo base_url("category.php?id=$cat_id&page=".($page+1)); ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <h4 class="sidebar-title">Recent News</h4>
            <div class="list-group list-group-flush">
                <?php
                $recent_sql = "SELECT * FROM news WHERE status='published' ORDER BY created_at DESC LIMIT 5";
                $recent_res = $conn->query($recent_sql);
                while($rec = $recent_res->fetch_assoc()):
                ?>
                <a href="<?php echo base_url('news.php?id='.$rec['id']); ?>" class="list-group-item list-group-item-action py-3">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1"><?php echo limit_words($rec['title'], 8); ?></h6>
                    </div>
                    <small class="text-muted"><?php echo date('M d', strtotime($rec['created_at'])); ?></small>
                </a>
                <?php endwhile; ?>
            </div>
            
            <div class="my-4 text-center">
                 <?php 
                 $ad_sidebar = get_ad('sidebar');
                 if($ad_sidebar):
                     echo $ad_sidebar;
                 else:
                 ?>
                 <div class="bg-light border py-5 text-muted">
                    ADVERTISEMENT
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
