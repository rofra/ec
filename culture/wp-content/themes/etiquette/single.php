<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>

<!--<script type="text/javascript" src="< ?php bloginfo('template_url'); ?>/assets/js/jcarousel.js"></script>
<link type="text/css" src="< ?php bloginfo('template_url'); ?>/assets/css/carousel-gallery-jquery.css" /> -->

<div id="content" role="main">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			
            <div class="post-date">
                <span class="day"><?php the_time('d') ?></span>
                <span class="month clear"><?php the_time('M') ?></span>
            </div>
            
            <div class="post-body">
        
                <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                
                <span class="auth">by <?php the_author() ?></span>
                       

				<div class="entry">
					<?php the_content(); ?>
                </div>		

				<p class="postmetadata">
                    <a class="addthis_button" href="http://www.addthis.com/bookmark.php"><img src="<?php bloginfo('template_url'); ?>/assets/img/share.png" border="0" alt="Share" /> Share</a>
                    <script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>
                    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4da76b2a59bee778"></script>
                </p>

			</div>
		
        </div>	

	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

	<?php endif; ?>
	
    <div class="posts navigation">
        <div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
        <div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
    </div>

</div>

<?php get_sidebar(); ?>

<div style="clear:both"></div>


<?php get_footer(); ?>
