<?php
session_start();
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>
<style type="text/css">
/*<![CDATA[*/
tr.tr1 { background-color: #f9f9f9; }
tr.tr2 { background-color: #fff; }
/*]]>*/
</style>
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
<?php echo AppointmentType($post->ID); ?>
<form action="" method="post" id="cart-ajax">
<div id="eventleft">
<table>
    <tr>
		<td align=left valign=top style="background:#ffffff;">&nbsp;&nbsp;&nbsp;Select date and time</td>
	</tr>
	<tr>
		<td align=left id="calTwo" valign=top style="padding:10px; background:#f9f9f9;">
			loading calendar...
		</td>
	</tr>
	<tr>
		<td align=left id="calTwoResult" valign=top style="background:#ffffff;"></td>
	</tr>
</table>
<input type="submit" name="Booking" value="Book now" id="calbutton" />

    </div><!-- eventleft -->
    </form>
<div id="eventright">
<?php //echo OptionsBook(); ?>
<?php echo get_post_meta($post->ID, 'eventdesc', true); ?>
<div id="result"></div>
<script>
$("form#cart-ajax").submit(function(){
  $.post("<?php echo PLUGIN_URL_ALLBOOK ?>/cart-ajax.php",{
        id: $("input[name=id]:checked").val(),
        action: "add"
      },
      function( data ) {
      $( "#result" ).html( data );
  });
  return false;
});
</script>
    </div><!-- eventright -->
<div class="clearfloat">
 </div>
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
