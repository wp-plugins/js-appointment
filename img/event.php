<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>
<?php
global $post;
  ?>

		<div id="container">
			<div id="content" role="main">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="nav-above" class="navigation">
                <?php
                //$jsgallery_options = get_jsgallery_options();
                /*$enableshop = get_post_meta($post->ID, 'shop', true);
                if ($jsgallery_options['activeshop'] == 1) {
                if ($enableshop == 1) {*/
                if ( ! dynamic_sidebar( 'Cart' ) ) : ?>
                <?php endif; // end primary widget area ?>
                <?php /*}
                }*/
                ?>
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
				</div><!-- #nav-above -->

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<div class="entry-meta">
						<?php //twentyten_posted_on(); ?>
					</div><!-- .entry-meta -->

					<div class="entry-content">
<div id="eventwrap">
<?php
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');
$settings = allbook_get_settings();
?>
<?php the_content(); ?> 
<form action="<?php echo $url ?>/index.php?page_id=<?php echo $settings["allbook_pageid"]; ?>&pager=cart&action=add" method="post" id="cart">
<div id="eventleft">
<table>
	<tr>
		<td align=left valign=top style="padding:10px; background:#E3E3E3;">
			<strong>Single Month Demo</strong><br>
			Select Number of Days
			<select id="calTwoDays" onChange="$('#calTwo').data('days', $('#calTwoDays').val());">
				<option value="1">1</option><option value="2" SELECTED>2</option><option value="3">3</option>
				<option value="4">4</option><option value="5">5</option><option value="6">6</option>
				<option value="7">7</option><option value="8">8</option><option value="9">9</option>
				<option value="10">10</option><option value="11">11</option><option value="12">12</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align=left id="calTwo" valign=top style="padding:10px; background:#E3E3E3;">
			loading calendar...
		</td>
	</tr>
	<tr>
		<td align=left id="calTwoResult" valign=top style="padding:10px; background:#ffffff;"></td>
	</tr>
</table>
    </div><!-- eventleft -->
<div id="eventright">
<?php echo OptionsBook(); ?>
<input type="hidden" name="action" value="add" />
<input type="submit" name="Booking" value="Booking" />
    </div><!-- eventright -->
<div class="clearfloat">
 </div>
 </form>
    </div><!-- eventwrap -->

						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
					<div id="entry-author-info">
						<div id="author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
						</div><!-- #author-avatar -->
						<div id="author-description">
							<h2><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author() ); ?></h2>
							<?php the_author_meta( 'description' ); ?>
							<div id="author-link">
								<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
									<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author() ); ?>
								</a>
							</div><!-- #author-link	-->
						</div><!-- #author-description -->
					</div><!-- #entry-author-info -->
<?php endif; ?>

					<div class="entry-utility">
						<?php //jsgallery_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-utility -->
				</div><!-- #post-## -->

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
				</div><!-- #nav-below -->

				<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
