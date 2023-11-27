<?php
    require_once('helpers.php');
    require_once('functions.php');
    require_once('mysql_init.php');
    require_once('models.php');
    require_once('data.php');

    if (!$mysql_connect_link) {
        die('Ошибка соединения: ' . mysqli_connect_error());
    }

    $sql_get_categories = get_query_categories();
    $categories_result = mysqli_query($mysql_connect_link, $sql_get_categories);
    if (!$categories_result) {
        die('Ошибка соединения: ' . mysqli_error($mysql_connect_link));
    }
    $categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);

    $page_content = include_template('main-login.php', []);

    $layout_content = include_template('layout-login.php', [
        'content' => $page_content,
        'categories' => $categories,
        'title' => 'Вход на сайт',
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]);

    print($layout_content);
