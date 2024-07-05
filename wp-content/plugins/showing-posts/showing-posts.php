<?php
/* 
Plugin name: Posts by ShortCode 
Author: Mher Frangulyan
Version: 1.0
Description: Showing Posts by Shortcode
*/

add_action('admin_menu', 'showing_posts_create_menu');

function showing_posts_create_menu() {
    add_menu_page('Posts by ShortCode', 'Posts by ShortCode', 'administrator', __FILE__, 'showing_posts_settings_page');
}

function showing_posts_settings_page() {
  ?>  
      <h1>Настройки</h1>
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'showing_posts';
    $charset_collate = $wpdb->get_charset_collate();
    $plugin_url = plugins_url('/', __FILE__);

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            posts_count int(11) DEFAULT '0' NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        $initial_posts_count = 10;
        $wpdb->insert(
            $table_name,
            array(
                'posts_count' => $initial_posts_count,
            )
        );
        $post_count = $initial_posts_count;
    } else {
        $row = $wpdb->get_row("SELECT * FROM $table_name");
        if ($row) {
            $post_count = $row->posts_count;
        }
    }
?>

<div class="shortcode-block">
    <p>Скопируйте шорткод и поставьте где хотите</p>
    <span>[shortcodeposts]</span>
</div>
<div class="post-counts">
    <label>Количество записей</label><br>
    <input type="text" class="post-numbers" value="<?php echo $post_count; ?>">
    <button type="button" class="save_btn">Сохранить</button>
</div>

<script>
jQuery(document).ready(function($) {
    $('.save_btn').click(function(e) {
        e.preventDefault();

        var numberposts = $('.post-numbers').val();

        if (Number(numberposts) > 0) {
            const url = '<?php echo $plugin_url; ?>ajax/save_settings.php';
            const data = {
                'action': 'save_settings', 
                'nonce': '<?php echo wp_create_nonce('save_settings_nonce'); ?>', 
                'numberposts': numberposts 
            };

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Успешно: ' + response.message);
                        
                    } else {
                        alert('Ошибка: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Ошибка при отправке запроса: ' + error);
                }
            });

        } else {
            alert('Не правильно ввели или не ввели количество записей');
        }
    });
});
</script>

<?php
}

add_action('admin_enqueue_scripts', 'showing_posts_enqueue_admin_styles');
function showing_posts_enqueue_admin_styles() {
    wp_enqueue_style('showing-posts-admin-style', plugins_url('libs/custom-style.css', __FILE__));
}
?>