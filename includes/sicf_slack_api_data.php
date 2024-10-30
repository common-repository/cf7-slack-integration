<?php

// Функция отправки сообщения в Slack
function sicf_send_text_to_slack($text, $url_hook){
        $data = array(
            "text" => $text
        );
    
        $args = array(
            'body'      => json_encode( $data ),
            'headers'   => array(
              'Content-Type'  => 'application/json',
            ),
        );
        //wp_remote_post - отправка post запроса Обертка для curl
        $result = wp_remote_post( $url_hook, $args);
    return $result;
}

