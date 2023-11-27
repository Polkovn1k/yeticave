<?php
    function format_price($price) {
        if (!is_numeric($price)) {
            return "0 ₽";
        }
        $price = ceil($price);
        $price = $price > 1000 ? number_format($price, 0, '', ' ') : $price;
        return "$price ₽";
    }

    function get_time_left($date) {
        date_default_timezone_set('Europe/Moscow');
        $current = date_create();
        $target_data = date_create($date);
        $interval = date_diff($current, $target_data);
        $total_hours = $interval->invert ? 0 : (($interval->days * 24) + $interval->h);
        $total_minutes = $interval->invert ? 0 : $interval->i;
        return [str_pad($total_hours, 2, '0', STR_PAD_LEFT), str_pad($total_minutes, 2, '0', STR_PAD_LEFT)];
    }

    function validate_date ($date) {
        if (is_date_valid($date)) {
            $now = date_create();
            $d = date_create($date);
            $diff = date_diff($d, $now);
            $interval = date_interval_format($diff, "%d");

            if ($interval < 1) {
                return 'Дата должна быть больше текущей не менее чем на один день';
            };
        } else {
            return 'Содержимое поля «дата завершения» должно быть датой в формате «ГГГГ-ММ-ДД»';
        }
    }

    function db_get_prepare_stmt_version($link, $sql, $data = []) {
        $stmt = mysqli_prepare($link, $sql);

        if ($stmt === false) {
            $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
            die($errorMsg);
        }

        if ($data) {
            $types = '';
            $stmt_data = [];

            foreach ($data as $key => $value) {
                $type = 's';

                if (is_int($value)) {
                    $type = 'i';
                }
                else if (is_double($value)) {
                    $type = 'd';
                }

                if ($type) {
                    $types .= $type;
                    $stmt_data[] = $value;
                }
            }

            $values = array_merge([$stmt, $types], $stmt_data);
            mysqli_stmt_bind_param(...$values);

            if (mysqli_errno($link) > 0) {
                $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
                die($errorMsg);
            }
        }

        return $stmt;
    }

    function validate_email ($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "E-mail должен быть корректным";
        }
    }

    function validate_length ($value, $min, $max) {
        if ($value) {
            $len = strlen($value);
            if ($len < $min or $len > $max) {
                return "Значение должно быть от $min до $max символов";
            }
        }
    }

    function get_arrow ($result_query) {
        $row = mysqli_num_rows($result_query);
        if ($row === 1) {
            $arrow = mysqli_fetch_assoc($result_query);
        } else if ($row > 1) {
            $arrow = mysqli_fetch_all($result_query, MYSQLI_ASSOC);
        }

        return $arrow;
    }
