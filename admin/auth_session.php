<?php
session_start();
if(!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'editor'])) {
    header("Location: index.php");
    exit();
}
?>
