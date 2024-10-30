<?php

/*
Plugin Name: Integrate Slack with Contact Form 7
Description: Creating plugin for slack messages from Contact Form 7
Version: 1.1.0
Author: Atlas-it
Author URI: http://atlas-it.by
Text Domain: sicf-atl-wp-slack-noty
Domain Path: /lang/
License:     GPL2
Copyright 2020  Atlas  (email: atlas.webdev89@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*langs file*/
$plugin_dir = basename( dirname( __FILE__ ) );
load_plugin_textdomain( 'sicf-atl-wp-slack-noty', null, $plugin_dir.'/lang/' );

global $wpdb;
define('SICF_DOMAIN_TEXT', 'sicf-atl-wp-slack-noty'); // Домен для перевода    
define('SICF_ANBLOG_SLACK_DIR', plugin_dir_path(__FILE__));     //полный путь к корню папки плагина (от сервера)
define('SICF_ANBLOG_TEST_URL', plugin_dir_url(__FILE__));      //путь к корню папки плагина (лучше его использовать)
define('SICF_SLACK_TABLE',  $wpdb->get_blog_prefix() .  'sicf_slack_noty_table'); // Название таблицы в БД

// строки для перевода заголовков плагина, чтобы они попали в .po файл.
__( 'Integrate Slack with Contact Form 7', SICF_DOMAIN_TEXT);
__( 'Creating plugin for slack messages from Contact Form 7', SICF_DOMAIN_TEXT);

//Функции для активации и деактивации плагина
require_once 'includes/sicf_start_end_activation.php';

/*Хук срабатывания при активации плагина*/
register_activation_hook(__FILE__, 'sicf_atl_wp_slack_noty_active');
/*Хук срабатывает при удалении плагина*/
register_uninstall_hook     (__FILE__, 'sicf_atl_wp_slack_noty_unistall');

// Проверяем установлен ли плагин Contact Form 7 по наличию функция этого плагина 
add_action('init', 'sifc_wp_atl_check_cf7');
function sifc_wp_atl_check_cf7 () {     
    if(!function_exists( 'wpcf7_contact_form' ) && !function_exists( 'wpcf7_save_contact_form' )) {
        add_action('admin_notices', 'general_admin_notice_slack');
            function general_admin_notice_slack(){
                    global $pagenow;
                            if ( $pagenow == 'plugins.php' ) {
                                echo   '<div class="notice notice-error is-dismissible">
                                            <p>'.__('For the Slack integration Contact form 7 plugin to work, you need to install the Contact Form 7 plugin', SICF_DOMAIN_TEXT).'</p>
                                        </div>';
                                error_log(__("Activate plugin Contact Form 7, please..",SICF_DOMAIN_TEXT));
                            }
                }
                
            return false;
    }
}

// Функция для работы с api slack
require_once 'includes/sicf_slack_api_data.php';
// Функция для вывода панели настройки плагина Slack
require_once 'includes/sicf_slack_panel_view.php';
// Функции для работы с БД
require_once 'includes/sicf_slack_databases_api.php';
// Основные функции плагина 
require_once 'includes/sicf_slack_main_func.php';