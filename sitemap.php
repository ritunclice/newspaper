<?php
include 'db.php';

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Home Page -->
    <url>
        <loc><?php echo base_url(); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <priority>1.0</priority>
    </url>

    <!-- Categories -->
    <?php
    $cats = $conn->query("SELECT id FROM categories");
    while($c = $cats->fetch_assoc()):
    ?>
    <url>
        <loc><?php echo base_url('category.php?id='.$c['id']); ?></loc>
        <priority>0.8</priority>
    </url>
    <?php endwhile; ?>

    <!-- News Articles -->
    <?php
    $news = $conn->query("SELECT id, created_at FROM news ORDER BY created_at DESC LIMIT 100");
    while($n = $news->fetch_assoc()):
    ?>
    <url>
        <loc><?php echo base_url('news.php?id='.$n['id']); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($n['created_at'])); ?></lastmod>
        <priority>0.6</priority>
    </url>
    <?php endwhile; ?>
</urlset>
