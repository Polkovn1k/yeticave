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

    $page404_content = include_template('404.php', [
        'categories' => $categories,
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]);


    $lot_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($lot_id) {
        $sql_get_lot = get_query_lot($lot_id);
    } else {
        http_response_code(404);
        print($page404_content);
        die();
    }
    $lot_result = mysqli_query($mysql_connect_link, $sql_get_lot);
    $lot = mysqli_fetch_assoc($lot_result);
    if (!$lot) {
        http_response_code(404);
        print($page404_content);
        die();
    }

    $page_content = include_template('main-lot.php', [
        'lot' => $lot,
    ]);
    $layout_content = include_template('layout-lot.php', [
        'content' => $page_content,
        'categories' => $categories,
        'title' => 'Страница лота',
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]);

    print($layout_content);
