<?php
    $admin_id = 1;
    session_start();
    $is_auth = isset($_SESSION['name']);
    $user_name = $is_auth ? $_SESSION['name'] : null;
