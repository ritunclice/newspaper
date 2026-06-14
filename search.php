<?php
include 'header.php';

$query = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if (empty($query)) {
    echo "<div class='container my-5'><h3>Please enter a search term.</h3></div>";
    include 'footer.php';
    exit;
}

// Fetch Search Results
$sql = "SELECT news.*, categories.category_name FROM news LEFT JOIN categories ON news.category_id = categories.id WHERE (title LIKE '%$query%' OR description LIKE '%$query%') AND news.status='published' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="mb-4 border-bottom pb-2">Search Results for: <span class="text-primary">"<?php echo htmlspecialchars($query); ?>"</span></h2>
            
            <div class="list-group">
                <?php
                if ($result->num_rows > 0):
                    while($news = $result->fetch_assoc()):
                        $img_src = $news['image'] ? base_url('uploads/'.$news['image']) : 'https://placehold.co/200x150';
                ?>
                <a href="<?php echo base_url('news.php?id='.$news['id']); ?>" class="list-group-item list-group-item-action mb-3 border rounded">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="<?php echo $img_src; ?>" class="img-fluid rounded-start h-100 object-fit-cover" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?php echo $news['title']; ?></h5>
                                <p class="card-text"><?php echo limit_words($news['description'], 25); ?></p>
                                <p class="card-text"><small class="text-muted">Category: <?php echo $news['category_name']; ?> | <?php echo date('M j, Y', strtotime($news['created_at'])); ?></small></p>
                            </div>
                        </div>
                    </div>
                </a>
                <?php 
                    endwhile; 
                else:
                    echo "<div class='alert alert-warning'>No news found matching your criteria.</div>";
                endif;
                ?>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <h4 class="sidebar-title">Categories</h4>
            <ul class="list-group list-group-flush">
                <?php
                $cat_sql = "SELECT * FROM categories";
                $cat_res = $conn->query($cat_sql);
                while($c = $cat_res->fetch_assoc()):
                ?>
                <li class="list-group-item"><a href="<?php echo base_url('category.php?id='.$c['id']); ?>" class="text-decoration-none text-dark"><?php echo $c['category_name']; ?></a></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
