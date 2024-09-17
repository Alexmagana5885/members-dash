<?php
session_start();
// Destroy the current session
session_unset();
session_destroy();
// Redirect to the homepage
header("Location: https://www.agl.or.ke");
exit();
?>
