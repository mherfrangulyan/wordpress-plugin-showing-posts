<?php 
    function showposts_shortcode(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'showing_posts'; 

        $row = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");

        if ($row) {
            $post_count = $row->posts_count;
        }
        
        
        $args = array(
           'post_type' => 'post',
           'numberposts' => $post_count, 
           'orderby' => 'date', 
           'order' => 'DESC',
        );
        $posts = get_posts($args);
        $output = '<div class="posts-block">';

        
        foreach ($posts as $post) {
            $output .= '<div class="post">';
               $output .= '<h2 class="post-title">' . esc_html($post->post_title) . '</h2>';
               $output .= '<div class="post-content">' . wpautop(wp_kses_post($post->post_content)) . '</div>';
            $output .= '</div>';
        }

        return $output . '</div>';
    }
    add_shortcode('shortcodeposts', 'showposts_shortcode');    


?>