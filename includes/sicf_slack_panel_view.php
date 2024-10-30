<?php

// Дополнительная панель для настройки интеграции с Slack
add_filter('wpcf7_editor_panels', 'sicf_wpcf7_editor_panels_slack');
function sicf_wpcf7_editor_panels_slack( $panels )
{
        $panels['slack-panel'] = array(
              'title'     => 'Slack',
              'callback'  => 'sicf_slack_panel_views',
        );
    return $panels;
}

function sicf_slack_panel_views($contact_form)
{
  $activate = '0';
  $hook_url = ' ';
  $headers=' ';

  // Получаем значения из БД для данной формы
  $data = sicf_wp_atl_get_slack_data ($contact_form->id());
  
    if ( isset( $data->active ) ) {
      $activate = $data->active;
    }

    if ( isset( $data->url_hook ) ) {
      $hook_url = $data->url_hook;
    }
    
    if(isset ($data->headers)) {
       $headers = $data->headers;
    }
  
?>

<fieldset>
    <legend>
        <?php _e('Setting integration from Slack', SICF_DOMAIN_TEXT);?>
    </legend>

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label>
                       <?php _e('Integration', SICF_DOMAIN_TEXT);?>                          
                    </label>
                </th>
                <td>
                    <p>
                        <label for="ctz-activate">
                            <input type="checkbox" id="ctz-slack-activate" name="ctz-slack-activate" value="1"<?php checked( $activate, "1" ) ?>>
                            <?php _e('Set send data in Slack', SICF_DOMAIN_TEXT);?>
                        </label>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php _e('Webhook URL', SICF_DOMAIN_TEXT);?>
                    </label>
                </th>
                <td>
                    <p>
                        <label for="ctz-hook-url">
                            <input type="url" id="ctz-slack-hook-url" name="ctz-slack-hook-url" value="<?php echo esc_attr($hook_url); ?>" style="width: 100%;">
                        </label>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label>
                        <?php _e('Transformation header in message from Slack', SICF_DOMAIN_TEXT);?>
                    </label>
                </th>
                <td>
                    <p><?php _e('To set the message headers in slack, the following record format is used: header--name.', SICF_DOMAIN_TEXT);?></p>
                    <p><?php _e('Example {site = Site name}', SICF_DOMAIN_TEXT);?></p>
                    <p>
                        <label for="ctz-slack-trans-headers">
                            <textarea cols="20" rows="8" id="ctz-slack-trans-headers" name="ctz-slack-trans-headers"  style="width: 100%;"><?php echo esc_html($headers);?></textarea>
                        </label>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset>

<?php }

