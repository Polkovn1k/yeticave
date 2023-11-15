<?php
    function get_query_list_lots() {
        return 'SELECT title, categories.name_category as category, start_price as price, img as image, date_end as expiration FROM lots '
             . 'INNER JOIN categories on lots.category_id = categories.id'
             . ' WHERE date_end > CURRENT_TIMESTAMP ORDER BY date_creation DESC';
    }

    function get_query_categories() {
        return 'SELECT name_category, character_code FROM categories';
    }
