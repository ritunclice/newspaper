<?php include 'header.php'; ?>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Total News Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded">
                            <i class="far fa-newspaper fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 fw-medium">Total News</p>
                        <?php $news_count = $conn->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count']; ?>
                        <h3 class="mb-0 fw-bold"><?php echo number_format($news_count); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Categories Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded">
                            <i class="fas fa-layer-group fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 fw-medium">Categories</p>
                        <?php $cat_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count']; ?>
                        <h3 class="mb-0 fw-bold"><?php echo number_format($cat_count); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Comments Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 fw-medium">Comments</p>
                        <?php $comm_count = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count']; ?>
                        <h3 class="mb-0 fw-bold"><?php echo number_format($comm_count); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unread Messages Card -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-warning bg-opacity-10 text-warning p-3 rounded">
                            <i class="fas fa-envelope-open-text fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted mb-1 fw-medium">Unread Inbox</p>
                        <?php $msg_count = $conn->query("SELECT COUNT(*) as count FROM contacts WHERE status='unread'")->fetch_assoc()['count']; ?>
                        <h3 class="mb-0 fw-bold"><?php echo number_format($msg_count); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent News Table -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-history me-2"></i>Recently Published</h6>
                <a href="news.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Title</th>
                                <th>Author</th>
                                <th>Date</th>
                                <th class="text-center pe-4">Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $latest_sql = "SELECT title, author, created_at, views FROM news ORDER BY created_at DESC LIMIT 5";
                            $latest_res = $conn->query($latest_sql);
                            if($latest_res->num_rows > 0):
                                while($row = $latest_res->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="ps-4"><div class="text-dark fw-semibold"><?php echo limit_words($row['title'], 8); ?></div></td>
                                <td><small class="text-muted"><?php echo $row['author']; ?></small></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td class="text-center pe-4"><span class="badge bg-light text-dark border"><?php echo $row['views']; ?></span></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center py-4">No news articles found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Feedback / Recent Messages -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-inbox me-2"></i>New Messages</h6>
                <a href="contacts.php" class="text-decoration-none small">Go to Inbox</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php
                    $msg_sql = "SELECT name, subject, created_at FROM contacts ORDER BY created_at DESC LIMIT 5";
                    $msg_res = $conn->query($msg_sql);
                    if($msg_res && $msg_res->num_rows > 0):
                        while($m = $msg_res->fetch_assoc()):
                    ?>
                    <div class="list-group-item list-group-item-action py-3 border-0 border-bottom">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 text-dark fw-bold"><?php echo htmlspecialchars($m['name']); ?></h6>
                            <small class="text-muted"><?php echo date('M d', strtotime($m['created_at'])); ?></small>
                        </div>
                        <p class="mb-1 small text-muted text-truncate"><?php echo htmlspecialchars($m['subject']); ?></p>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="p-4 text-center text-muted small">No recent messages.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<?php include 'footer.php'; ?>
