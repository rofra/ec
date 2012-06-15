<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>

	<div id="content" role="main">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); $i++?>
    	
		<?php if($i<=3):?>

			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<div class="post-date">
					<span class="day"><?php the_time('d') ?></span>
                    <span class="month clear"><?php the_time('M') ?></span>
                </div>
                
                <div class="post-body">
            
                    <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                    
                    <span class="auth">by <?php the_author() ?></span>
                       
                    <div class="entry">
                    	<div class="main-img"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a></div>
                        <?php the_excerpt('Read more...'); ?>
                    </div>
    
                    <p class="postmetadata">
						<a class="addthis_button" href="http://www.addthis.com/bookmark.php"><img src="<?php bloginfo('template_url'); ?>/assets/img/share.png" border="0" alt="Share"/> Share</a>
                      	<script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>
                      	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4da76b2a59bee778"></script>
                    </p>
                    
                </div>    
			</div>
            
		<?php else:?>
        
        	<div <?php post_class('small') ?> id="post-<?php the_ID(); ?>">
				
                <div class="post-body <?php if (($i % 2) == 0): ?>right<?php endif; ?>">
            
                    <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                        
                    <div class="entry">

                    	<div class="main-img"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('small-post-thumb'); ?></a></div>

                        <div class="post-date">
                        
                            <?php the_time('d F Y') ?>
                           
                        </div>
                        <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">Read More...</a>
                    </div>
                    
                </div>    
			</div>
        
        <?php endif;?>
		
		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

		<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php get_search_form(); ?>

	<?php endif; ?>
	<script type="text/javascript">
if( $('#content .small:nth-child(2n)') )
		$('#content .small:nth-child(2n)').addClass('last');	
	</script>
	</div>

<?php get_sidebar(); ?>

<div style="clear:both"></div>

<?php get_footer(); ?>
