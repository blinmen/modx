<?php
/*
 * Template name: Import
 */
get_header(); ?>
    <div id="main-content">
        <?php

        $exportedPosts = json_decode(file_get_contents(WP_CONTENT_DIR . '/export.json'), true);
        //        var_dump($exportedPosts);
        foreach ($exportedPosts as $exportedPost) {
//            echo $exportedPost['pagetitle'] . '<br><br>';
//            echo $exportedPost['introtext'] . '<br><br>';
//            echo $exportedPost['publishedon'] . '<br><br>';
//            echo explode('/', $exportedPost['uri'])[1] . '<br><br>';
//            echo $exportedPost['content'] . '<br><br>';
//            echo $exportedPost['seo_title'] . '<br><br>';
//            echo $exportedPost['seo_kw'] . '<br><br>';
//            echo $exportedPost['seo_description'] . '<br><br>';
//            echo $exportedPost['post_tags'] . '<br><br>';
//            echo $featured_image = json_decode($exportedPost['featured_image'], true)['src'];
//            echo '<br><br><hr><br><br>';

            $post_id = wp_insert_post([
                'post_title' => $exportedPost['pagetitle'],
                'post_name' => explode('/', $exportedPost['uri'])[1],
                'post_excerpt' => $exportedPost['introtext'],
                'post_content' => $exportedPost['content'],
                'post_status' => 'publish',
                'post_author' => 1,
                'post_category' => array(11),
                'post_date' => $exportedPost['publishedon'],
                'post_date_gmt' => $exportedPost['publishedon'],
            ]);

            update_post_meta($post_id, 'rank_math_primary_category', 11);
            update_post_meta($post_id, 'rank_math_title', $exportedPost['seo_title']);
            update_post_meta($post_id, 'rank_math_description', $exportedPost['seo_description']);
            update_post_meta($post_id, 'rank_math_focus_keyword', $exportedPost['seo_kw']);

            $featured_image = json_decode($exportedPost['featured_image'], true)['src'];
            $image_url = 'http://localhost/wp-content/uploads/exported-images/' . $featured_image;
            set_featured_image($post_id, $image_url);


            set_post_tags($post_id, $exportedPost['post_tags']);


        }


        function set_featured_image($post_id, $image_url)
        {
            if (!get_post($post_id)) {
                return false;
            }

            $image_data = file_get_contents($image_url);

            if (!$image_data) {
                return false;
            }

            $filename = basename($image_url);

            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['path'] . '/' . $filename;

            file_put_contents($file_path, $image_data);

            if (!file_exists($file_path)) {
                return false;
            }

            $filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type' => $filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attachment_id = wp_insert_attachment($attachment, $file_path, $post_id);

            if (!$attachment_id) {
                return false;
            }

            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
            wp_update_attachment_metadata($attachment_id, $attachment_data);

            set_post_thumbnail($post_id, $attachment_id);

            return true;
        }


        function set_post_tags($post_id, $tags_string)
        {
            if (!get_post($post_id)) {
                return false;
            }

            $tags_array = explode(',', $tags_string);

            $tags_array = array_map('trim', $tags_array);

            wp_set_post_terms($post_id, $tags_array, 'post_tag', false);

            return true;
        }

        ?>
    </div>
<?php get_footer();
