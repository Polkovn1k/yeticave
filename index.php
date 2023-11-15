<?php
    require_once('helpers.php');
    require_once('functions.php');
    require_once('mysql_init.php');
    require_once('models.php');
    require_once('data.php');

    if (!$mysql_connect_link) {
        die('Ошибка соединения: ' . mysqli_connect_error());
    }

    $sql_get_lots =  get_query_list_lots();
    $sql_get_categories = get_query_categories();

    $lots_result = mysqli_query($mysql_connect_link, $sql_get_lots);
    $categories_result = mysqli_query($mysql_connect_link, $sql_get_categories);
    if (!$lots_result) {
        die('Ошибка соединения: ' . mysqli_error($mysql_connect_link));
    }
    if (!$categories_result) {
        die('Ошибка соединения: ' . mysqli_error($mysql_connect_link));
    }
    $goods = mysqli_fetch_all($lots_result, MYSQLI_ASSOC);
    $categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);

    $page_content = include_template('main.php', [
        'categories' => $categories,
        'goods' => $goods,
    ]);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'categories' => $categories,
        'title' => 'Главная страница',
        "is_auth" => $is_auth,
        "user_name" => $user_name
    ]);

    print($layout_content);
