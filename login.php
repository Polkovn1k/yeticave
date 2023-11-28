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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['email', 'password'];
        $errors = [];

        $rules = [
            'email' => function($value) {
                return validate_email($value);
            },
            'password' => function($value) {
                return validate_length($value, 6, 8);
            }
        ];

        $user_info = filter_input_array(INPUT_POST,
        [
            'email' => FILTER_DEFAULT,
            'password' => FILTER_DEFAULT
        ], true);

        foreach ($user_info as $field => $value) {
            if (isset($rules[$field])) {
                $rule = $rules[$field];
                $errors[$field] = $rule($value);
            }
            if (in_array($field, $required) && empty($value)) {
                $errors[$field] = "Поле $field нужно заполнить";
            }
        }

        $errors = array_filter($errors);

        if (count($errors)) {
            $page_content = include_template('main-login.php', [
                'categories' => $categories,
                'user_info' => $user_info,
                'errors' => $errors
            ]);
        } else {
            $users_data = get_login($mysql_connect_link, $user_info['email']);
            if ($users_data) {
                if (password_verify($user_info['password'], $users_data['user_password'])) {
                    $issession = session_start();
                    $_SESSION['name'] = $users_data['user_name'];
                    $_SESSION['id'] = $users_data['id'];

                    header('Location: /index.php');
                } else {
                    $errors['password'] = 'Вы ввели неверный пароль';
                }
            } else {
                $errors['email'] = 'Пользователь с таким е-mail не зарегестрирован';
            }
            if (count($errors)) {
                $page_content = include_template('main-login.php', [
                    'categories' => $categories,
                    'user_info' => $user_info,
                    'errors' => $errors
                ]);
            }
        }
    }

    $layout_content = include_template('layout-login.php', [
        'content' => $page_content,
        'categories' => $categories,
        'title' => 'Вход на сайт',
        'is_auth' => $is_auth,
        'user_name' => $user_name
    ]);

    print($layout_content);
