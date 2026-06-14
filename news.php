<?php
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container my-5'><h3>Invalid News ID</h3></div>";
    include 'footer.php';
    exit;
}

$id = intval($_GET['id']);

// Update view count
$conn->query("UPDATE news SET views = views + 1 WHERE id = $id");

// Fetch News
$sql = "SELECT news.*, categories.category_name FROM news LEFT JOIN categories ON news.category_id = categories.id WHERE news.id = $id AND news.status='published'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<div class='container my-5'><h3>News not found</h3></div>";
    include 'footer.php';
    exit;
}

$news = $result->fetch_assoc();
?>

<div class="container my-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo base_url('category.php?id='.$news['category_id']); ?>"><?php echo $news['category_name']; ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo limit_words($news['title'], 5); ?></li>
                </ol>
            </nav>

            <h1 class="mb-3"><?php echo $news['title']; ?></h1>
            
            <div class="mb-3 text-muted d-flex align-items-center">
                <span class="me-3"><i class="fas fa-user"></i> <?php echo $news['author']; ?></span>
                <span class="me-3"><i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($news['created_at'])); ?></span>
                <span><i class="far fa-eye"></i> <?php echo $news['views']; ?> Views</span>
            </div>

            <?php if($news['image']): ?>
            <div class="mb-4">
                <img src="<?php echo base_url('uploads/'.$news['image']); ?>" class="img-fluid rounded w-100" alt="<?php echo $news['title']; ?>">
            </div>
            <?php endif; ?>

            <div class="article-content fs-5" style="line-height: 1.8;">
                <?php echo nl2br($news['description']); ?>
            </div>

            <!-- Share Buttons -->
            <div class="my-5 p-4 bg-white border rounded shadow-sm">
                <h5 class="mb-3 fw-bold"><i class="fas fa-share-nodes me-2"></i>Share this article:</h5>
                <div class="d-flex flex-wrap gap-2">
                    <?php 
                    $share_url = base_url('news.php?id=' . $news['id']);
                    $encoded_url = urlencode($share_url);
                    $share_title = urlencode($news['title']);
                    ?>
                    
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_url; ?>" target="_blank" class="btn btn-primary d-flex align-items-center shadow-sm">
                        <i class="fab fa-facebook-f me-2"></i> Facebook
                    </a>
                    
                    <!-- X (Twitter) -->
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $encoded_url; ?>&text=<?php echo $share_title; ?>" target="_blank" class="btn btn-dark d-flex align-items-center shadow-sm">
                        <i class="fab fa-x-twitter me-2"></i> X
                    </a>
                    
                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text=<?php echo $share_title . '%20' . $encoded_url; ?>" target="_blank" class="btn btn-success d-flex align-items-center shadow-sm">
                        <i class="fab fa-whatsapp me-2"></i> WhatsApp
                    </a>

                    <!-- Instagram / Mobile Native Share -->
                    <button onclick="nativeShare()" class="btn btn-danger d-flex align-items-center shadow-sm" style="background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); border: none;">
                        <i class="fab fa-instagram me-2"></i> Share to Instagram
                    </button>

                    <!-- Copy Link -->
                    <button onclick="copyToClipboard()" class="btn btn-outline-secondary d-flex align-items-center shadow-sm">
                        <i class="fas fa-link me-2"></i> Copy Link
                    </button>

                    <script>
                    function nativeShare() {
                        if (navigator.share) {
                            navigator.share({
                                title: '<?php echo addslashes($news['title']); ?>',
                                text: 'Check out this news:',
                                url: '<?php echo $share_url; ?>',
                            }).catch((error) => console.log('Error sharing', error));
                        } else {
                            // Fallback for desktop: Copy link and tell user
                            copyToClipboard();
                            alert('Instagram sharing is best on mobile. The link has been copied to your clipboard, you can now paste it in your Instagram Bio or Direct Message!');
                        }
                    }

                    function copyToClipboard() {
                        const el = document.createElement('textarea');
                        el.value = '<?php echo $share_url; ?>';
                        document.body.appendChild(el);
                        el.select();
                        document.execCommand('copy');
                        document.body.removeChild(el);
                        if (!navigator.share) {
                            alert('Link copied to clipboard!');
                        }
                    }
                    </script>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="my-5">
                <h4 class="sidebar-title">Comments</h4>
                
                <?php
                // Handle Comment Submission
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
                    $name = $conn->real_escape_string($_POST['name']);
                    $comment = $conn->real_escape_string($_POST['comment']);
                    
                    if (!empty($name) && !empty($comment)) {
                        $ins_sql = "INSERT INTO comments (news_id, name, comment) VALUES ($id, '$name', '$comment')";
                        if ($conn->query($ins_sql)) {
                            echo "<div class='alert alert-success'>Comment posted successfully!</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Error posting comment.</div>";
                        }
                    }
                }

                // List Comments
                $comm_sql = "SELECT * FROM comments WHERE news_id = $id ORDER BY created_at DESC";
                $comm_res = $conn->query($comm_sql);
                
                if ($comm_res->num_rows > 0) {
                    while($c = $comm_res->fetch_assoc()) {
                        echo '<div class="d-flex mb-3">';
                        echo '<div class="flex-shrink-0"><img src="https://placehold.co/50x50" class="rounded-circle" alt="..."></div>';
                        echo '<div class="flex-grow-1 ms-3">';
                        echo '<h6 class="fw-bold mb-1">'.$c['name'].' <small class="text-muted fw-normal">'.date('M d, Y', strtotime($c['created_at'])).'</small></h6>';
                        echo '<p>'.$c['comment'].'</p>';
                        echo '</div></div>';
                    }
                } else {
                    echo "<p class='text-muted'>No comments yet. Be the first to comment!</p>";
                }
                ?>

                <hr>
                <h5>Leave a Comment</h5>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="submit_comment" class="btn btn-primary">Post Comment</button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
             <div class="mb-4">
                <h4 class="sidebar-title">Related News</h4>
                <?php
                $rel_sql = "SELECT * FROM news WHERE category_id = {$news['category_id']} AND id != $id AND status='published' LIMIT 4";
                $rel_res = $conn->query($rel_sql);
                while($rel = $rel_res->fetch_assoc()):
                ?>
                <div class="card mb-3 border-0">
                    <div class="row g-0">
                        <div class="col-4">
                            <?php $img = $rel['image'] ? base_url('uploads/'.$rel['image']) : 'https://placehold.co/200x200'; ?>
                            <img src="<?php echo $img; ?>" class="img-fluid rounded" alt="...">
                        </div>
                        <div class="col-8">
                            <div class="card-body py-0">
                                <h6 class="card-title"><a href="<?php echo base_url('news.php?id='.$rel['id']); ?>" class="text-decoration-none text-dark"><?php echo $rel['title']; ?></a></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <div class="mb-4 text-center">
                 <?php 
                 $ad_sidebar = get_ad('sidebar');
                 if($ad_sidebar):
                     echo $ad_sidebar;
                 else:
                 ?>
                 <div class="bg-light border py-5 text-muted">
                    SQUARE ADVERTISEMENT
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
