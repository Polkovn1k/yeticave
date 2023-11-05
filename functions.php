<?php
    function format_price($price) {
        if (!is_numeric($price)) {
            return "0 ₽";
        }
        $price = ceil($price);
        $price = $price > 1000 ? number_format($price, 0, '', ' ') : $price;
        return "$price ₽";
    }
