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

    $page_content = include_template('main-sign.php', []);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required_fields = ['email', 'password', 'name', 'message'];
        $errors = [];

        $rules = [
            'email' => function($value) {
                return validate_email($value);
            },
            'password' => function($value) {
                return validate_length ($value, 6, 8);
            },
            'name' => function($value) {
                return validate_length ($value, 3, 30);
            },
            'message' => function($value) {
                return validate_length ($value, 12, 1000);
            }
        ];

        $user = filter_input_array(INPUT_POST,
            [
                'email' => FILTER_DEFAULT,
                'password' => FILTER_DEFAULT,
                'name' => FILTER_DEFAULT,
                'message' => FILTER_DEFAULT
            ], true);

        foreach ($user as $field => $value) {
            if (isset($rules[$field])) {
                $rule = $rules[$field];
                $errors[$field] = $rule($value);
            }
            if (in_array($field, $required_fields) && empty($value)) {
                $errors[$field] = "Поле $field нужно заполнить";
            }
        }

        $errors = array_filter($errors);

        if (count($errors)) {
            $page_content = include_template('main-sign.php', [
                'categories' => $categories,
                'user' => $user,
                'errors' => $errors
            ]);
        } else {
            $users_data = get_users_data($mysql_connect_link);
            $emails = array_column($users_data, 'email');
            $names = array_column($users_data, 'user_name');
            if (in_array($user['email'], $emails)) {
                $errors['email'] = 'Пользователь с таким е-mail уже зарегистрирован';
            }
            if (in_array($user['name'], $names)) {
                $errors['name'] = 'Пользователь с таким именем уже зарегистрирован';
            }

            if (count($errors)) {
                $page_content = include_template('main-sign.php', [
                    'categories' => $categories,
                    'user' => $user,
                    'errors' => $errors
                ]);
            } else {
                $sql = get_query_create_user();
                $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
                $stmt = db_get_prepare_stmt_version($mysql_connect_link, $sql, $user);
                $res = mysqli_stmt_execute($stmt);
                if ($res) {
                    header('Location: /login.php');
                } else {
                    $error = mysqli_error($mysql_connect_link);
                }
            }
        }
    }

    $layout_content = include_template('layout-sign.php', [
        'content' => $page_content,
        'categories' => $categories,
        'title' => 'Страница регистрации',
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]);

    print($layout_content);
