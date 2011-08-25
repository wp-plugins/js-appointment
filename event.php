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


				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>


					<div class="entry-content">
<div id="eventwrap">
<?php
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');
$settings = allbook_get_settings();
?>
<?php $groupid = get_post_meta($post->ID, 'groupid', true); ?>
<?php echo AppointmentType($groupid); ?>
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
                    </div>




				<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
