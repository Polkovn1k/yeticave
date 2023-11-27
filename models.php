<?php
    function get_query_list_lots() {
        return 'SELECT lots.id, title, categories.name_category as category, start_price as price, img as image, date_end as expiration FROM lots '
             . 'INNER JOIN categories on lots.category_id = categories.id '
             . 'WHERE date_end > CURRENT_TIMESTAMP ORDER BY date_creation DESC';
    }

    function get_query_categories() {
        return 'SELECT id, name_category, character_code FROM categories';
    }

    function get_query_lot($id_lot) {
        return 'SELECT lots.title, lots.start_price, lots.img, lots.date_end, lots.lot_description, categories.name_category FROM lots '
             . 'JOIN categories ON lots.category_id=categories.id '
             . 'WHERE lots.id='.$id_lot.';';
    }

    function set_new_lot($user_id) {
        return "INSERT INTO lots (title, category_id, lot_description, start_price, step, date_end, img, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, $user_id);";
    }

    function get_query_create_user() {
        return "INSERT INTO users (date_registration, email, user_password, user_name, contacts) VALUES (NOW(), ?, ?, ?, ?);";
    }

    function get_users_data($con) {
        if (!$con) {
            $error = mysqli_connect_error();
            return $error;
        } else {
            $sql = "SELECT email, user_name FROM users;";
            $result = mysqli_query($con, $sql);
            if ($result) {
                $users_data= get_arrow($result);
                return $users_data;
            }
            $error = mysqli_error($con);
            return $error;
        }
    }
