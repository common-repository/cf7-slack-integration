<?php

function sicf_atl_wp_slack_noty_active () {
    //Определяем вид СУБД и в зависимости от результата создаем таблицу в БД
         sicf_wp_atl_createTableSlack();
}

function sicf_atl_wp_slack_noty_unistall () {
    //Функция удаления таблицы при удалении плагина
        sicf_wp_atl_delete_table_slack();
}

//Функция создание таблицы в БД для хранения курсов валют (при активации плагина)
function sicf_wp_atl_createTableSlack () {
    global $wpdb;
    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
    
    //Для доступа к финкции dbDelta
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    if($wpdb->get_var("SHOW TABLES LIKE '".SICF_SLACK_TABLE."'") != SICF_SLACK_TABLE) {
            $sql = "CREATE TABLE ".SICF_SLACK_TABLE." (
                id int(11) unsigned NOT NULL auto_increment,
                form int(11) unsigned NOT NULL, 
                active bool NOT NULL default 0,
                url_hook varchar(255) NOT NULL,
                headers text  NOT NULL,
                date_modify timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
                date_update int(24) unsigned NOT NULL, 
                PRIMARY KEY id (id),
                UNIQUE KEY form (form) 
            ) {$charset_collate};";
        // UNIQUE KEY form (form)  - Создание уникального поля или уникального индекса
        // KEY form (form)  - Создание  индекса (KEY вместо INDEX)
        
        // Создать таблицу.
            dbDelta( $sql );
        }
    }

function sicf_wp_atl_delete_table_slack () {
    //Функция удаления таблицы при удалении плагина
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS  ".SICF_SLACK_TABLE."");
}    
