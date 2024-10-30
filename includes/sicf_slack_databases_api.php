<?php

//Функция добавления данных в таблицу
function sicf_wp_atl_add_slack_table (array $data) {
    global $wpdb;
    //Отключаем показ ошибок на экране
    $wpdb->show_errors = FALSE;
    
    if(is_array($data) && !empty($data)) {
        
        $result = $wpdb->insert(SICF_SLACK_TABLE, 
            [
                "form"=>$data["form"],
                "active"=>$data["active"],
                "url_hook" => $data["url_hook"],
                "headers" => $data["headers"],
                "date_update" => time(),
            ],
            [
                "%d",
                "%d",
                "%s",
                "%s",
                "%d",
            ]);
        if(!$wpdb->last_error) {
                return $wpdb->insert_id;
        }else {
                error_log(((strpos($wpdb->last_error,'Duplicate entry')) !== false) ? __('The specified key already exists (UNIQUE constraint)',SICF_DOMAIN_TEXT).' ['.$wpdb->last_error .']' : $wpdb->last_error);
          return $result;
        }
    }
}

//Функция обновления данных
function sicf_wp_atl_update_slack_table (array $data, $date_modity = null) {
   global $wpdb; 
   
   //Проверяем есть ли в таблице запись для данной формы. 
   if($wpdb->get_var("SELECT COUNT(*) FROM ".SICF_SLACK_TABLE." WHERE form = ".$data['form']." " ) == 1) {
        
       if(isset($data['url_hook']) && isset($data['active'])) {
                return $wpdb->update(SICF_SLACK_TABLE, 
                     [
                         "url_hook" => $data['url_hook'],
                         'active' =>$data['active'],
                         "headers" => $data["headers"],
                     ],
                     [
                         "form" => $data['form'],
                     ],
                        ["%s", "%d", "%s"],["%d"]
                   );
       }
    }
} 

//Функция получения данных
function sicf_wp_atl_get_slack_data($form) {
    global $wpdb; 
            $prepare = $wpdb->prepare(
                "SELECT * FROM ".SICF_SLACK_TABLE." WHERE form = %d", [$form]
            );
        $result = $wpdb->get_row($prepare);
    return $result;
}

//Функция проверки наличия в таблице записи для указаной формы
function sicf_wp_atl_getVar($number) {
    global $wpdb; 
        $prepare = $wpdb->prepare(
            "SELECT COUNT(*) FROM ".SICF_SLACK_TABLE." WHERE form = %d", [$number]
        );
        $result = $wpdb->get_var($prepare);
    return $result;
}

//Функция проверки включена ли функция отправки в Slack

function sicf_wp_alt_check_activate ($id_form) {
    global $wpdb; 
        $prepare = $wpdb->prepare(
            "SELECT active FROM ".SICF_SLACK_TABLE." WHERE form = %d", [$id_form]
        );
        $result = $wpdb->get_col($prepare);
    return $result[0];
}

