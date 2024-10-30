<?php

add_action( 'wpcf7_before_send_mail', 'sicf_wpcf7_before_send_mail_start_function');

function sicf_wpcf7_before_send_mail_start_function($contact_form){
    
    //Проверяем включена ли отправка оповещений в Slack
    if(sicf_wp_alt_check_activate($contact_form->id()) != 0 ) {
        $datas = sicf_wp_atl_get_slack_data($contact_form->id());
            if(isset($datas->url_hook) && !empty($datas->url_hook)) {
                $url_hook = $datas->url_hook;
                $headers = $datas->headers;
            }else {
                return false;
            }
    }else {
        return false;
    }
            //Получение данных формы Contact form 7
            $submission = WPCF7_Submission::get_instance();

            //Данные с формы Без служебной информации
            $posted_data = $submission->get_posted_data(); 

            //Название сайта
            $siteName = ['site'=>"Запрос с сайта ".get_option('blogname')];
            //url сайта
            $urlSite  = ['url'=>site_url()];
            // Складываем массивы чтобы значения шли по порядку
            $posted_data = $siteName +$urlSite+$posted_data;
            // Преобразуем заголовки в сообщении 
            if(isset($headers) && !empty(trim($headers))) {
                $posted_data = sicf_transform_headers_in_slack($posted_data, $headers);
            }
    
    $text='';
    //Преобразуем массив в текст
    foreach ($posted_data as $key=>$item) {
        $text.=$key ." - ". $item."\n";
    }
    
    // вызов функции отправки данных в slack  (slack_api_data.php) 
    $result = sicf_send_text_to_slack($text, $url_hook);
        if($result['response']['code'] != 200) {
            error_log( __('Error send data Slack. Code error - '.$result['response']['code'].', Message - '.$result['response']['message'] , SICF_DOMAIN_TEXT));
        }else if(isset($result['errors']) && !empty ($result['errors'])) {
            error_log( __('Error send data Slack. Code error - '.print_r($result['errors']), SICF_DOMAIN_TEXT));
        }
    return ($result);
}

// Функция преобразования заголовком в сообщении для Slack
function sicf_transform_headers_in_slack ($data, $headers) {
    //Создадим из текста массив разбив по определенному разделителю
    $headers =  preg_split("/\\}[\s]*\\{/", $headers);
        foreach ($headers as $key=>&$item) {
                //Уберем не нужные символы в значения в массива 
                $sym = array('{', '}');
                $item = str_replace($sym, '', $item);
            //Создадим дополнительный массив разбив значения по символу =    
            $arr = explode('=', $item);
            //Создадим массив с ключами из предыдущего массива
            $transorm[trim($arr[0])] = trim($arr[1]);
        }
    
        foreach ($data as $key1=>$item1){
            foreach ($transorm as $key2=>$item2) {
                if ($key1 == $key2) {
                    //Изменяем ключ массива
                    $data[$transorm[$key2]] = $item1;
                        unset($data[$key1]);
                }
            }
        }
    return $data;
}

//Хук срабатывает при сохраннении формы Contact Form 7
add_action('wpcf7_save_contact_form', 'sicf_wpcf7_slack_save_contact_form');

function sicf_wpcf7_slack_save_contact_form($contact_form) {
    
  $data=['form' => $contact_form->id()];  
    
            if ( isset( $_POST['ctz-slack-activate'] ) && $_POST['ctz-slack-activate'] == '1' ) {
                $data['active'] = '1';
            } else {
                $data['active'] = '0';
            }

            if ( isset( $_POST['ctz-slack-hook-url'] ) ) {
                $data['url_hook'] = sanitize_url( $_POST['ctz-slack-hook-url'] );
            }
            
            if (isset ($_POST['ctz-slack-trans-headers'])) {
                $data['headers'] = sanitize_text_field($_POST['ctz-slack-trans-headers']);
            }
            
            // Проверяем есть ли в BD запись для текущей контактной формы
            if(sicf_wp_atl_getVar($contact_form->id())) {
                    // Если есть обновляем 
                    $result = sicf_wp_atl_update_slack_table ($data);
            }else {
                    // Если нет создаем запись
                    $result = sicf_wp_atl_add_slack_table($data); 
            }
            
            if($result === false) {
                error_log( __('Error add data in databases...' , SICF_DOMAIN_TEXT));
            }
}

