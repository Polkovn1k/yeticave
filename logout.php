<?php
    session_start();
    $_SESSION = [];
    session_unset();
    header('Location: /index.php');
