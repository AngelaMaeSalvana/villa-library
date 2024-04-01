<?php
    include "auth.php"; 
    session_start();
    session_unset();
    session_destroy(); 
    header('location: /villa-library/index.php'); 
?>