<?php
/**
 * Logout Script
 * 
 * Simple logout script yang menghapus session
 */

session_start();
session_destroy();

header("Location: login.php?message=Logged%20out%20successfully");
exit();
?>
