<?php
/**
 * Template Name: Special Edition
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

<div id="content" class="special-edition" role="main">

        <?php if (have_posts()) : while (have_posts()) : the_post();

            $thumbnail = "";
            if (has_post_thumbnail())
            {
                $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($id),'full');
                $thumbnail = $thumbnail[0];
            }
        ?>
        <div class="post" id="post-<?php the_ID(); ?>">
            <div class="entry">
                <span class="header"></span>
                <?php if ($thumbnail): ?><a href="<?php the_permalink(); ?>"><img class="header_img" src="<?php echo $thumbnail?>"></a><?php endif; ?>
                <?php the_content('Read more...'); ?>

                <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
            </div>
            <?php
                $args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post->ID, 'order' => 'ASC' );
                $attachment_link;
                $attachments = get_posts($args);
                if ($attachments)
                {
            ?>
            <div class="gallery_container" id="gallery_container-<?php the_ID(); ?>">
                <ul class="gallery_main" id="gallery_main-<?php the_ID(); ?>">
            <?php
                    foreach ( $attachments as $attachment )
                    {
                        $attachment_link = wp_get_attachment_image_src($attachment->ID,array(575, 425));
                        $attachment_link = $attachment_link[0];
                        if ($attachment_link == $thumbnail) continue;
            ?>
                        <li><img src="<?php echo $attachment_link?>"/></li>
            <?php

                    }
            ?>
                </ul>
                <div class="gallery_nav" id="gallery_nav-<?php the_ID(); ?>"></div>
            </div>
            <?php
                }
            ?>
            <?php
                $shoplink = get_post_meta($id,'shop_link',true);
                if ($shoplink)
                {
            ?>
                <a class="shop_link" href="<?php echo $shoplink?>">Shop</a>
            <?php
                }
            ?>
            <div class="clear"></div>
        </div>
        <?php endwhile; endif; ?>
    <?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>

    </div>
    <div class="clear"></div>

    <div class="navigation">
        <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
        <div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
    </div>


<?php get_footer(); ?>
