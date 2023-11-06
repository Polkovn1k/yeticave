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
