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
    $categories_id_list = array_column($categories, 'id');

    if (!$is_auth) {
        $page403_content = include_template('403.php', [
            'categories' => $categories,
            'is_auth' => $is_auth,
            'title' => 'Доступ запрещен',
            'user_name' => $user_name
        ]);
        http_response_code(403);
        print($page403_content);
        die();
    }

    $page_content = include_template('main-add.php', [
        'categories' => $categories,
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required_fields = ['lot-name', 'message', 'category', 'lot-rate', 'lot-date', 'lot-step'];
        $errors = [];

        $rules = [
            'category' => function ($category_id) use($categories_id_list) {
                if (!in_array($category_id, $categories_id_list)) {
                    return 'Выберите категорию';
                }
            },
            'lot-rate' => function ($num) {
                if ((int)$num > 0) {
                    return NULL;
                }
                return 'Содержимое поля должно быть целым числом больше ноля';
            },
            'lot-step' => function ($num) {
                if ((int)$num > 0) {
                    return NULL;
                }
                return 'Содержимое поля должно быть целым числом больше ноля';
            },
            'lot-date' => function ($date) {
                return validate_date($date);
            }
        ];

        $new_lot_fields = filter_input_array(INPUT_POST, [
            'lot-name' => FILTER_DEFAULT,
            'category' => FILTER_DEFAULT,
            'message' => FILTER_DEFAULT,
            'lot-rate' => FILTER_DEFAULT,
            'lot-step' => FILTER_DEFAULT,
            'lot-date' => FILTER_DEFAULT
        ], true);

        foreach ($new_lot_fields as $field => $value) {
            if (isset($rules[$field])) {
                $rule_for_field = $rules[$field];
                $errors[$field] = $rule_for_field($value);
            }
            if (in_array($field, $required_fields) && empty($value)) {
                $errors[$field] = "Поле $field не заполнено";
            }
        }

        $errors = array_filter($errors);

        if (!empty($_FILES['lot-img']['name'])) {
            $tmp_name = $_FILES['lot-img']['tmp_name'];
            $path = $_FILES['lot-img']['name'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);
            if ($file_type === 'image/jpeg') {
                $ext = '.jpg';
            } else if ($file_type === 'image/png') {
                $ext = '.png';
            }
            if ($ext) {
                $filename = uniqid() . $ext;
                $new_lot_fields['path'] = $filename;
                move_uploaded_file($_FILES['lot-img']['tmp_name'], "uploads/$filename");
            } else {
                $errors['lot-img'] = 'Допустимые форматы файлов: jpg, jpeg, png';
            }
        } else {
            $errors['lot-img'] = 'Загрузите изображение';
        }

        if (count($errors)) {
            $page_content = include_template('main-add.php', [
                'categories' => $categories,
                'errors' => $errors,
                'lot' => $new_lot_fields,
            ]);
        } else {
            $sql = set_new_lot($admin_id);
            $stmt = db_get_prepare_stmt_version($mysql_connect_link, $sql, $new_lot_fields);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                $lot_id = mysqli_insert_id($mysql_connect_link);
                header("Location: /lot.php?id=" .$lot_id);
            } else {
                $error = mysqli_error($mysql_connect_link);
            }
        }
    }

    $layout_content = include_template('layout-add.php', [
        'content' => $page_content,
        'categories' => $categories,
        'title' => 'Страница добавления лота',
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]);

    print($layout_content);
