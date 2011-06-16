<?php
/**
 * Adding Menu
 */
/*function jsgallery_add_pages() {

    add_menu_page('main', 'Js Gallery', 8, 'settings','');
    add_submenu_page('settings', 'Js Gallery Settings', 'Settings', 8, 'settings', 'jsgallery_settings');
	add_submenu_page('settings', 'Js Gallery Orders', 'Orders', 8,'orders', 'jseccomerce_orders');
    add_submenu_page('settings', 'Js Gallery invoice', 'Invoice', 8, 'invoice', 'jseccomerce_invoice');
    add_submenu_page('settings', 'Js Upload Gallery', 'Upload', 8, 'upload_gallery', 'jseccomerce_upload');
}*/


/*						Register Menu Restuarant Custom Post Types
-----------------------------------------------------------------------------------
*/

function appointment_register() {

	register_post_type('appointment', array(
		'description' => 'Appointments',
		'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
		'menu_position' => 5,
		'menu_icon' => PLUGIN_URL_ALLBOOK . '/admin/realestate.png',
		'exclude_from_search' => false,
		'labels' => array(
			'name' => 'Appointment',
			'singular_name' => 'Appointment Item',
			'add_new' => 'New Appointment Item',
			'add_new_item' => 'Add Appointment Item',
			'edit' => 'Edit Appointment Item',
			'edit_item' => 'Edit Appointment Item',
			'new_item' => 'New Appointment Item',
			'view' => 'View Appointment Item',
			'view_item' => 'View Appointment Item',
			'search_items' => 'Search Appointment Items',
			'not_found' => 'No Appointment Items found',
			'not_found_in_trash' => 'No Appointment Items found in Trash',
			'parent' => 'Parent Appointment Item',
            'menu_name' => 'Appointment',
		),
		'supports' => array('title', 'editor', 'comments', 'excerpt', 'custom-fields', 'revisions', 'thumbnail', 'author', 'page-attributes'),
		'public' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
        'publicly_queryable' => true,
		'query_var' => true,
		//'rewrite' => true,
		'rewrite' => array('slug' => 'appointment/%appointments%', 'with_front' => false),
		'taxonomies' => array( 'post_tag' ),
		)
	);

	register_taxonomy( 'appointments', 'appointment', array( 'hierarchical' => true, 'label' => 'Appointments Categories', 'query_var' => true, 'rewrite' => array('slug' => 'appointments', 'with_front' => false), 'show_ui' => true, 'show_in_nav_menus' => true,  ) );

}

add_action('init', 'appointment_register');

// Edit Portfolio Custom Type Columns
add_filter("manage_edit-appointment_columns", "appointment_edit_columns");
add_action("manage_posts_custom_column",  "appointment_custom_columns");

function appointment_edit_columns($columns) {

	$columns = array(
		"cb"         => "<input type=\"checkbox\" />",
		"title"      => "Title",
		"author"     => "Author",
		"appointments" => "Appointments Category",
		"tags"       => "Tags",
		"comments"   => "<div class='vers'><img src=" . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . " alt='Comments'></div>",
		"date"       => "Date",
	);

	return $columns;
}

function appointment_custom_columns($column) {
	global $post;
	switch ($column)
	{
		case "appointments":
			echo get_the_term_list($post->ID, 'appointments', '', ', ', '');
			break;
	}
}
add_filter('wp_loaded','flushRules');

// Remember to flush_rules() when adding rules
function flushRules(){
	global $wp_rewrite;
   	$wp_rewrite->flush_rules();
}

add_filter('post_type_link', 'appointment_permalink', 10, 3);
// Adapted from get_permalink function in wp-includes/link-template.php
function appointment_permalink($permalink, $post_id, $leavename) {
	$post = get_post($post_id);
	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		$leavename? '' : '%postname%',
		'%post_id%',
		'%appointments%',
		'%author%',
		$leavename? '' : '%pagename%',
	);

	if ( '' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
		$unixtime = strtotime($post->post_date);

		$category = '';
		if ( strpos($permalink, '%appointments%') !== false ) {
		   $cats = wp_get_object_terms($post->ID, 'appointments');
			if ( $cats ) {
		   $category = $cats[0]->slug;
		} else {
          $category = "appointments";
        }
        }

		$author = '';
		if ( strpos($permalink, '%author%') !== false ) {
			$authordata = get_userdata($post->post_author);
			$author = $authordata->user_nicename;
		}

		$date = explode(" ",date('Y m d H i s', $unixtime));
		$rewritereplace =
		array(
			$date[0],
			$date[1],
			$date[2],
			$date[3],
			$date[4],
			$date[5],
			$post->post_name,
			$post->ID,
			$category,
			$author,
			$post->post_name,
		);
		$permalink = str_replace($rewritecode, $rewritereplace, $permalink);
	} else { // if they're not using the fancy permalink option
	}
	return $permalink;
}

function my_template_redirect() {
  global $wp;
  if ( $wp->query_vars['post_type'] == "appointment" ) {
    $template = PLUGIN_PATH_ALLBOOK . '/event.php';
    $templatetheme = ABSPATH . '/wp-content/themes/event/event.php';
    if (file_exists( $templatetheme )) {
    include($templatetheme);
    exit;
    } else if (file_exists( $template )) {
    include($template);
    exit;
    }
    }
    if ( $wp->query_vars['appointments'] ) {
    $settings = allbook_get_settings();
    $template1 = PLUGIN_PATH_ALLBOOK . '/category.php';

    $templatetheme1 = ABSPATH . '/wp-content/themes/event/category-event.php';

    $themegallery = $template1;

    if (file_exists( $templatetheme1 )) {
    include($templatetheme1);
    exit;
    } else
    if (file_exists( $themegallery )) {
    include($themegallery);
    exit;
  }
  }
}
add_action('template_redirect', 'my_template_redirect');

/* Add a new meta box to the admin menu. */
	add_action( 'admin_menu', 'hybrid_create_meta_box' );

/* Saves the meta box data. */
	add_action( 'save_post', 'hybrid_save_meta_data' );

/**
 * Function for adding meta boxes to the admin.
 * Separate the post and page meta boxes.
 *
 * @since 0.3
 */
function hybrid_create_meta_box() {
	global $theme_name;

    //add_meta_box( 'upload-meta-boxes', __('Image Files'), 'upload_meta_boxes', 'event', 'normal', 'high' );
    add_meta_box( 'post-meta-boxes', __('Appointment Details'), 'post_meta_boxes', 'appointment', 'normal', 'high' );
    //add_meta_box('custom_editor', 'Image description', 'blank', 'event', 'normal', 'low');
    //remove_meta_box( 'postcustom' , 'event' , 'normal' );
    //remove_meta_box( 'postexcerpt' , 'event' , 'normal' );
}

function blank(){}

add_action('admin_head','admin_head_hook');
function admin_head_hook()
{
	?><style type="text/css">
		#postdiv.postarea, #postdivrich.postarea { margin:0; }
		#post-status-info { line-height:1.4em; font-size:13px; }
		#custom_editor .inside { margin:2px 6px 6px 6px; }
		#ed_toolbar { display:none; }
		#postdiv #ed_toolbar, #postdivrich #ed_toolbar { display:block; }
	</style><?php
}

add_action('admin_footer','admin_footer_hook');
function admin_footer_hook()
{
	?><script type="text/javascript">
		jQuery('#postdiv, #postdivrich').prependTo('#custom_editor .inside');
	</script><?php
}

/**
 * Array of variables for post meta boxes.  Make the
 * function filterable to add options through child themes.
 *
 * @since 0.3
 * @return array $meta_boxes
 */
function hybrid_post_meta_boxes() {

	/* Array of the meta box options. */
	$meta_boxes = array(
		'groupid' => array( 'name' => 'groupid', 'title' => __('Appointment Type', 'hybrid'), 'type' => 'selectdate' ),
        'eventdesc' => array( 'name' => 'eventdesc', 'title' => __('Descriptions', 'hybrid'), 'type' => 'editor' ),
        //'shop' => array( 'name' => 'shop', 'title' => __('Shop available', 'hybrid'), 'type' => 'checkbox' ),
        //'price' => array( 'name' => 'price', 'title' => __('Price', 'hybrid'), 'type' => 'text' ),
        //'download' => array( 'name' => 'download', 'title' => __('Download available', 'hybrid'), 'type' => 'checkbox' ),
//        'tipologia' => array( 'name' => 'tipologia', 'title' => __('type', 'hybrid'), 'type' => 'select', 'options' => array( '0' => 'Farmhouse', '1' => 'Hotel', '2' => 'B&B', '3' => 'Apartments', '4' => 'Rental', '5' => 'Vacation Rentals', '6' => 'Chalets', '7' => 'Log cabin', '8' => 'Self catering apartments', '9' => 'Inns and restaurant', '10' => 'Camping and caravan', '11' => 'Bunkhouse and bothies', '12' => 'Exclusive use', '13' => 'Cottage', '13' => 'Other' ) ),
//        'stars' => array( 'name' => 'stars', 'title' => __('Stars grading', 'hybrid'), 'type' => 'select', 'options' => array( '0' => 'None', '1' => '1 star', '2' => '2 stars', '3' => '3 stars', '4' => '4 stars', '5' => '5 stars' ) ),
//        'featuredpro' => array( 'name' => 'featuredpro', 'title' => __('Featured', 'hybrid'), 'type' => 'selectadmin', 'options' => array( '0' => 'No', '1' => 'Yes' ) ),

	);

	return apply_filters( 'hybrid_post_meta_boxes', $meta_boxes );
}

/**
 * Array of variables for page meta boxes.  Make the
 * function filterable to add options through child themes.
 *
 * @since 0.3
 * @return array $meta_boxes
 */


function hybrid_upload_meta_boxes() {

	/* Array of the meta box options. */
	$meta_boxes = array(
		'gallery' => array( 'name' => 'gallery', 'title' => __('gallery', 'hybrid'), 'type' => 'upload' ),

	);

	return apply_filters( 'hybrid_upload_meta_boxes', $meta_boxes );
}


/**
 * Displays meta boxes on the Write Post panel.  Loops
 * through each meta box in the $meta_boxes variable.
 * Gets array from hybrid_post_meta_boxes().
 *
 * @since 0.3
 */
function post_meta_boxes() {
	global $post;
	$meta_boxes = hybrid_post_meta_boxes(); ?>

	<table class="form-table">
	<?php foreach ( $meta_boxes as $meta ) :

		$value = get_post_meta( $post->ID, $meta['name'], true );

		if ( $meta['type'] == 'text' )
			get_meta_text_input( $meta, $value );
		elseif ( $meta['type'] == 'textarea' )
			get_meta_textarea( $meta, $value );
		elseif ( $meta['type'] == 'select' )
			get_meta_select( $meta, $value );
        elseif ( $meta['type'] == 'editor' )
			get_meta_editor( $meta, $value );
        elseif ( $meta['type'] == 'selectadmin' )
			get_meta_selectadmin( $meta, $value );
        elseif ( $meta['type'] == 'checkbox' )
			get_meta_checkbox( $meta, $value );
        elseif ( $meta['type'] == 'selectdate' )
			get_meta_selectgrup( $meta, $value );

	endforeach; ?>
	</table>
<?php
}

/**
 * Displays meta boxes on the Write Page panel.  Loops
 * through each meta box in the $meta_boxes variable.
 * Gets array from hybrid_page_meta_boxes()
 *
 * @since 0.3
 */


function upload_meta_boxes() {
	global $post;
	$meta_boxes = hybrid_upload_meta_boxes(); ?>

	<table class="form-table">
	<?php foreach ( $meta_boxes as $meta ) :

		$value = stripslashes( get_post_meta( $post->ID, $meta['name'], true ) );

		if ( $meta['type'] == 'text' )
			get_meta_text_input( $meta, $value );
		elseif ( $meta['type'] == 'textarea' )
			get_meta_textarea( $meta, $value );
		elseif ( $meta['type'] == 'select' )
			get_meta_select( $meta, $value );
        elseif ( $meta['type'] == 'upload' )
			get_meta_upload( $meta, $value );

	endforeach; ?>
	</table>
<?php
}


/**
 * Outputs a text input box with arguments from the
 * parameters.  Used for both the post/page meta boxes.
 *
 * @since 0.3
 * @param array $args
 * @param array string|bool $value
 */
function get_meta_text_input( $args = array(), $value = false ) {

	extract( $args ); ?>

	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo wp_specialchars( $value, 1 ); ?>" size="30" tabindex="30" style="width: 97%;" />
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

function get_meta_textajax_input( $args = array(), $value = false ) {

	extract( $args ); ?>

            <p>
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			<input type="text" name="<?php echo $name; ?>[]" id="<?php echo $name; ?>" value="<?php echo wp_specialchars( $value, 1 ); ?>" />
			<input type="hidden" name="<?php echo $name; ?>_noncename[]" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
            Es. mm-dd-YYYY
            </p>
	<?php
}

/**
 * Outputs a select box with arguments from the
 * parameters.  Used for both the post/page meta boxes.
 *
 * @since 0.3
 * @param array $args
 * @param array string|bool $value
 */
function get_meta_select( $args = array(), $value = false ) {

	extract( $args ); ?>

	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
			<?php foreach ( $options as $option ) : ?>
				<option <?php if ( htmlentities( $value, ENT_QUOTES ) == $option ) echo ' selected="selected"'; ?>>
					<?php echo $option; ?>
				</option>
			<?php endforeach; ?>
			</select>
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

function get_meta_selectgrup( $args = array(), $value = false ) {

	extract( $args );

    $category = mysql_query("SELECT * FROM wp_resservation_cat");

    ?>

	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
			<?php
            while($row1 = mysql_fetch_array($category))
            {
            /*** create the options ***/
            ?><option value="<?php echo $row1['id'] ?>" <?php if ( htmlentities( $value, ENT_QUOTES ) == $row1['id'] ) echo ' selected="selected"'; ?>><?php echo $row1['name'] ?></option>
            <?php
            }
            ?>
			</select>
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

function get_meta_selectadmin( $args = array(), $value = false ) {

	extract( $args ); ?>

	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
            <?php if( current_user_can( 'publish_posts' ) ) { ?>
			<?php foreach ( $options as $option ) : ?>
				<option <?php if ( htmlentities( $value, ENT_QUOTES ) == $option ) echo ' selected="selected"'; ?>>
					<?php echo $option; ?>
				</option>
			<?php endforeach; ?>
            <?php } else { ?>
            <option>No</option>
            <?php } ?>
			</select>
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

function get_meta_checkbox( $args = array(), $value = false ) {

	extract( $args );
    ?>
	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>

            <input type="checkbox" name="<?php echo $name; ?>" value="1" <?php if ( htmlentities( $value, ENT_QUOTES ) == 1 ) echo ' checked="checked"'; ?>/>
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

/**
 * Outputs a textarea with arguments from the
 * parameters.  Used for both the post/page meta boxes.
 *
 * @since 0.3
 * @param array $args
 * @param array string|bool $value
 */
function get_meta_textarea( $args = array(), $value = false ) {

	extract( $args );

    ?>

	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo wp_specialchars( $value, 1 ); ?></textarea>
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

function get_meta_editor( $args = array(), $value = false ) {

	extract( $args );

    ?>
<script type="text/javascript">

            jQuery(document).ready( function () {
                jQuery("#<?php echo $name ?>").addClass("mceEditor");
                if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
                    tinyMCE.execCommand("mceAddControl", false, "<?php echo $name ?>");
                }
            });

            jQuery(document).ready(function($) {



	$('a.toggleVisual-<?php echo $name ?>').click(
		function() {
			tinyMCE.execCommand('mceAddControl', false, '<?php echo $name ?>');
		}
	);

	$('a.toggleHTML-<?php echo $name ?>').click(
		function() {
			tinyMCE.execCommand('mceRemoveControl', false, '<?php echo $name ?>');
		}
	);

});


</script>
	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
        <p align="right">
	<a class="button toggleVisual-<?php echo $name ?>">Visual</a>
	<a class="button toggleHTML-<?php echo $name ?>">HTML</a>
        </p>
			<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo wp_specialchars( $value, 1 ); ?></textarea>
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

function get_meta_date( $args = array(), $value = false ) {

	extract( $args ); ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#<?php echo $name ?>").datepicker({ dateFormat: 'yy-mm-dd' });
    });

</script>
	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo wp_specialchars( $value, 1 ); ?>" size="30" tabindex="30" style="width: 30%;" />
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

function get_meta_hidden( $args = array(), $value = false ) {

	extract( $args ); ?>

	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<input type="hidden" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo wp_specialchars( $value, 1 ); ?>" size="30" tabindex="30" style="width: 97%;" />
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

function get_meta_upload( $args = array(), $value = false ) {

	extract( $args );

    $settings = allbook_get_settings();
    $dir = ABSPATH.$settings['galleries_rootfolder'];
    $url = get_bloginfo('url')."/".$settings['galleries_rootfolder']."/originals/";
    ?>

<script type="text/javascript">
$(document).ready(function() {
  $('#<?php echo $name; ?>_file_upload').uploadify({
    'uploader'  : '<?php echo JSGALLERY_PLUGIN_URL ?>/lib/uploader/uploadify.swf',
    'script'    : '<?php echo JSGALLERY_PLUGIN_URL ?>/lib/uploader/uploadifysingle.php',
    'cancelImg' : '<?php echo JSGALLERY_PLUGIN_URL ?>/lib/uploader/cancel.png',
    'folder'    : '<?php echo $dir; ?>',
    'method'      : 'post',
    'scriptData'  : {'directory':'<?php echo $dir; ?>'},
    'multi'          : true,
  'auto'           : true,
  'fileExt'        : '*.jpg;*.gif;*.png',
  'fileDesc'       : 'Image Files(.JPG, .GIF, .PNG)',
  'queueID'        : 'custom-queue-<?php echo $name; ?>',
  'queueSizeLimit' : 1,
  'simUploadLimit' : 1,
  'removeCompleted': false,
  'onSelectOnce'   : function(event,data) {
      $('#status-message-<?php echo $name; ?>').text(data.filesSelected + ' files have been added to the queue.');
    },
  'onAllComplete'  : function(event,data) {
      $('#status-message-<?php echo $name; ?>').text(data.filesUploaded + ' files uploaded, ' + data.errors + ' errors.');
    },
   'onComplete'  : function(event, ID, fileObj, response, data) {
      //alert('There are ' + data.fileCount + fileObj.name + ' files remaining in the queue.');
      $('#<?php echo $name; ?>').val(fileObj.name);
      $('#thumbnails').append('<img src="<?php echo JSGALLERY_PLUGIN_URL ?>/includes/timthumb.php?src=<?php echo $url ?>' + fileObj.name + '&amp;h=150&amp;w=200&amp;zc=1" />');
      $('#preview').css("display", "none" );
    }

  });
});

</script>
	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo wp_specialchars( $value, 1 ); ?>" size="30" tabindex="30" style="width: 97%;" />
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
            <div class="demo-box">
            <div id="status-message-<?php echo $name; ?>">Select some files to upload:</div>
            <div id="custom-queue-<?php echo $name; ?>"></div>
            <input id="<?php echo $name; ?>_file_upload" type="file" name="Filedata" />        </div>
            <br /><div id="thumbnails"></div>
            <?php if (wp_specialchars( $value, 1 )) {  ?>
            <br /><div id="preview"><img src="<?php echo JSGALLERY_PLUGIN_URL ?>/includes/timthumb.php?src=<?php echo $url.wp_specialchars( $value, 1 ); ?>&amp;h=150&amp;w=200&amp;zc=1" /></div>
            <?php } ?>
		</td>
	</tr>
	<?php
}

/**
 * Loops through each meta box's set of variables.
 * Saves them to the database as custom fields.
 *
 * @since 0.3
 * @param int $post_id
 */
function hybrid_save_meta_data( $post_id ) {
	global $post;

		//$meta_boxes1 = array_merge( hybrid_page_meta_boxes() );
		$meta_boxes = array_merge( hybrid_post_meta_boxes() );
        $meta_boxes2 = array_merge( hybrid_upload_meta_boxes() );
        //$meta_boxes3 = array_merge( hybrid_services_meta_boxes() );
        //$meta_boxes5 = array_merge( hybrid_price_meta_boxes() );
        //$meta_boxes6 = array_merge( hybrid_location_meta_boxes() );
        //$meta_boxes7 = array_merge( hybrid_availability_meta_boxes() );

	foreach ( $meta_boxes as $meta_box ) :

		if ( !wp_verify_nonce( $_POST[$meta_box['name'] . '_noncename'], plugin_basename( __FILE__ ) ) )
			return $post_id;

		if ( 'page' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) )
			return $post_id;

		elseif ( 'post' == $_POST['post_type'] && !current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		$data = stripslashes( $_POST[$meta_box['name']] );

		if ( get_post_meta( $post_id, $meta_box['name'] ) == '' )
			add_post_meta( $post_id, $meta_box['name'], $data, true );

		elseif ( $data != get_post_meta( $post_id, $meta_box['name'], true ) )
			update_post_meta( $post_id, $meta_box['name'], $data );

		elseif ( $data == '' )
			delete_post_meta( $post_id, $meta_box['name'], get_post_meta( $post_id, $meta_box['name'], true ) );

	endforeach;

/*    foreach ( $meta_boxes1 as $meta_box ) :

		if ( !wp_verify_nonce( $_POST[$meta_box['name'] . '_noncename'], plugin_basename( __FILE__ ) ) )
			return $post_id;

		if ( 'page' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) )
			return $post_id;

		elseif ( 'post' == $_POST['post_type'] && !current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		$data = stripslashes( $_POST[$meta_box['name']] );

		if ( get_post_meta( $post_id, $meta_box['name'] ) == '' )
			add_post_meta( $post_id, $meta_box['name'], $data, true );

		elseif ( $data != get_post_meta( $post_id, $meta_box['name'], true ) )
			update_post_meta( $post_id, $meta_box['name'], $data );

		elseif ( $data == '' )
			delete_post_meta( $post_id, $meta_box['name'], get_post_meta( $post_id, $meta_box['name'], true ) );

	endforeach;*/

    foreach ( $meta_boxes2 as $meta_box ) :

		if ( !wp_verify_nonce( $_POST[$meta_box['name'] . '_noncename'], plugin_basename( __FILE__ ) ) )
			return $post_id;

		if ( 'page' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) )
			return $post_id;

		elseif ( 'post' == $_POST['post_type'] && !current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		$data = stripslashes( $_POST[$meta_box['name']] );

		if ( get_post_meta( $post_id, $meta_box['name'] ) == '' )
			add_post_meta( $post_id, $meta_box['name'], $data, true );

		elseif ( $data != get_post_meta( $post_id, $meta_box['name'], true ) )
			update_post_meta( $post_id, $meta_box['name'], $data );

		elseif ( $data == '' )
			delete_post_meta( $post_id, $meta_box['name'], get_post_meta( $post_id, $meta_box['name'], true ) );

	endforeach;


}

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) {
if ( current_user_can( 'edit_user' ) ) {
?>

<h3><?php _e("User Details", "blank"); ?></h3>

<table class="form-table">
<tr>
<th><label for="address"><?php _e("Address"); ?></label></th>
<td>
<input type="text" name="address" id="address" value="<?php echo esc_attr( get_the_author_meta( 'address', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Please enter your address."); ?></span>
</td>
</tr>
<tr>
<th><label for="city"><?php _e("City"); ?></label></th>
<td>
<input type="text" name="city" id="city" value="<?php echo esc_attr( get_the_author_meta( 'city', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Please enter your city."); ?></span>
</td>
</tr>
<tr>
<th><label for="province"><?php _e("Province"); ?></label></th>
<td>
<input type="text" name="province" id="province" value="<?php echo esc_attr( get_the_author_meta( 'province', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Please enter your province."); ?></span>
</td>
</tr>
<tr>
<th><label for="postalcode"><?php _e("Postal Code"); ?></label></th>
<td>
<input type="text" name="postalcode" id="postalcode" value="<?php echo esc_attr( get_the_author_meta( 'postalcode', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Please enter your postal code."); ?></span>
</td>
</tr>
<tr>
<th><label for="phone"><?php _e("Phone"); ?></label></th>
<td>
<input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Please enter your Phone."); ?></span>
</td>
</tr>
</table>
<?php }
 }
add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {

if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }

update_usermeta( $user_id, 'address', $_POST['address'] );
update_usermeta( $user_id, 'city', $_POST['city'] );
update_usermeta( $user_id, 'province', $_POST['province'] );
update_usermeta( $user_id, 'postalcode', $_POST['postalcode'] );
update_usermeta( $user_id, 'phone', $_POST['phone'] );
}

/**
 * Tyny_mce Button
 */

/*function jseccomerce_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;

   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_jseccomerce_tinymce_plugin");
     add_filter('mce_buttons', 'register_jseccomerce_button');
   }
}

function register_jseccomerce_button($buttons) {
   array_push($buttons, "separator", "jseccomerce");
   return $buttons;
}

// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_jseccomerce_tinymce_plugin($plugin_array) {
   $plugin_array['jseccomerce'] = JSGALLERY_PLUGIN_URL.'/includes/js/tinymce/editor_plugin.js';
   return $plugin_array;
}*/

/**
 * Retrieve list of templates.
 *
 */
function jseccomerce_get_templates() {

	$templates = array();

	$dir = JSGALLERY_PLUGIN_DIR."/templates/";

	$dh  = opendir($dir);
	while (false !== ($filename = readdir($dh))) {

                if (!(($filename == '.')||($filename == '..')))    $templates[] = $filename;
    }
		return $templates;

}

/**
 * Admin Settings - Orders.
 */
function jseccomerce_orders(){

?>

<div class="wrap wpckt">
<h2>Js Gallery - Orders</h2>

<?php

global $jsgallery_options;

//Bulk Delete

if ( ($_GET['action'] == 'delete') || ($_GET['action2'] == 'delete')){
$num = 0;
	foreach ($_GET['chk_ord'] as $id => $value ){

		if (jseccomerce_delete_order($id)){
			$num++;
		} else {
			echo "<div id='media-upload-error'>".jseccomerce_delete_order($_GET['id'])."</div>";
			break;
		}
	}
	if ($num > 0) {
		if ($num == 1) echo "<div class='updated'><p><strong>Order Deleted</strong></p></div>";
		else echo "<div class='updated'><p><strong>".$num." Orders Deleted</strong></p></div>";
	}
}



//Delete order

if ($_GET['action'] == 'delete-ord'){

		if (jseccomerce_delete_order($_GET['id'])){
			echo "<div class='updated'><p><strong>Selected Product Deleted</strong></p></div>";
		} else {
			echo "<div id='media-upload-error'>".jseccomerce_delete_order($_GET['id'])."</div>";
		}

}



$i=0;
$orders_list = jseccomerce_show_orders();

	if ($orders_list){
		?>
        <form method="get" action="" id="posts-filter">
        <input type="hidden" value="orders" name="page"/>
         <div class="tablenav">


        <div class="alignleft actions">
        <select name="action">
        <option selected="selected" value="">Bulk Actions</option>
        <option value="delete">Delete</option>
        </select>
        <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Apply"/>
        </div>

        <br class="clear"/>
        </div>

		<table cellspacing="0" class="widefat fixed">
		<thead>
		<tr class="thead">
			<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
			<th class="manage-column" id="username" scope="col">Date</th>
			<th class="manage-column" id="name" scope="col">Firstame</th>
			<th class="manage-column" id="email" scope="col">Lastname</th>
			<th class="manage-column" id="role" scope="col">Shipping Address</th>
			<th class="manage-column" id="posts" scope="col">Items</th>
            <th class="manage-column" id="posts" scope="col">Gross</th>
            <th class="manage-column" id="posts" scope="col">Status</th>
            <th class="manage-column num" id="posts" scope="col">Options</th>
		</tr>
		</thead>

		<tfoot>
		<tr class="thead">
			<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
			<th class="manage-column" id="username" scope="col">Date</th>
			<th class="manage-column" id="name" scope="col">Firstame</th>
			<th class="manage-column" id="email" scope="col">Lastname</th>
			<th class="manage-column" id="role" scope="col">Shipping Address</th>
			<th class="manage-column" id="posts" scope="col">Items</th>
            <th class="manage-column" id="posts" scope="col">Gross</th>
            <th class="manage-column" id="posts" scope="col">Status</th>
            <th class="manage-column num" id="posts" scope="col">Options</th>
		</tr>
		</tfoot>

		<tbody class="list:user user-list" id="users">
		<?php
		foreach ($orders_list as $order) {
		?>


                <tr class="<?php if ($i%2 == 0) echo "alternate";?>" id="prod-<?php echo $i;?>">
                <th class="check-column" scope="row">
                <input type="checkbox" value="1" id="check_<?php echo $i;?>" name="chk_ord[<?php echo $order['id'] ?>]"/>
                <input type="hidden" value="<?php echo $order['id'] ?>" name="order_id[]"/>
                </th>
                <td class="username column-username">
				<?php echo $order['time'] ?>
                </td>
                <td>
                <?php echo $order['firstname'] ?>
                </td>
                <td>
                <?php echo $order['lastname'] ?>
                </td>
                <td><?php echo $order['address'] ?></td>
                <td><?php echo $order['items'] ?></td>
                <td><?php echo $order['gross'] ?></td>
                <td><?php echo $order['status'] ?></td>
                <td class="posts column-posts num">
                <a onclick="document.getElementById('show_details_<?php echo $i;?>').style.display='block';return false;" href="#"><strong>Details</strong></a><br />
                <a onclick="document.getElementById('delete_alert_<?php echo $i;?>').style.display='block';return false;" href="#">Delete</a>
                </td>
                </tr>

                <tr>
                <td colspan="9" align="center" width="100%" class="alert">
                 <div id="delete_alert_<?php echo $i;?>" style="display:none;">
                    You are about to delete <strong><?php echo $order['filename'] ?></strong>.
                    <a href="?page=orders&action=delete-ord&id=<?php echo $order['id'] ?>">Continue</a>
                    <a onclick="this.parentNode.style.display='none';return false;" href="#">Cancel</a>
                 </div>

                 <div id="show_details_<?php echo $i;?>" style="display:none; text-align:left; padding-left:100px">
                 <a onclick="this.parentNode.style.display='none';return false;" href="#">Hide</a>
                	<?php echo $order['details'] ?>
                 <a onclick="this.parentNode.style.display='none';return false;" href="#">Hide</a>
                 </div>
                </td>
                </tr>

		<?php
		$i++;
		}

		?>



		</table>

		<div class="tablenav">


		<div class="alignleft actions">
		<select name="action2">
		<option selected="selected" value="">Bulk Actions</option>
		<option value="delete">Delete</option>
		</select>
		<input type="submit" class="button-secondary action" id="doaction2" name="doaction2" value="Apply"/>
		</div>

		<br class="clear"/>
		</div>

		<input id="save-all" class="button-primary savebutton" type="submit" value="Save all changes" name="save"/>

		</form>
     <?php
	} else {

	echo "<h3>There is no orders</h3>";
	echo "<p><a href='admin.php?page=settings'>Go to the Settings Page</a></p>";

	}
    ?>
<br />

<p align="center">Js Gallery - Version <?php echo $jsgallery_options['version'] ?></p>

</div>
<?php

}

function jseccomerce_invoice(){

?>

<div class="wrap wpckt">
<h2>Js Gallery - Invoice</h2>

<?php

global $jsgallery_options;

//Bulk Delete

if ( ($_GET['action'] == 'delete') || ($_GET['action2'] == 'delete')){
$num = 0;
	foreach ($_GET['chk_ord'] as $id => $value ){

		if (jseccomerce_delete_order($id)){
			$num++;
		} else {
			echo "<div id='media-upload-error'>".jseccomerce_delete_order($_GET['id'])."</div>";
			break;
		}
	}
	if ($num > 0) {
		if ($num == 1) echo "<div class='updated'><p><strong>Order Deleted</strong></p></div>";
		else echo "<div class='updated'><p><strong>".$num." Orders Deleted</strong></p></div>";
	}
}



//Delete order

if ($_GET['action'] == 'delete-ord'){

		if (jseccomerce_delete_order($_GET['id'])){
			echo "<div class='updated'><p><strong>Selected Product Deleted</strong></p></div>";
		} else {
			echo "<div id='media-upload-error'>".jseccomerce_delete_order($_GET['id'])."</div>";
		}

}



$i=0;
$orders_list = jseccomerce_show_orders();

	if ($orders_list){
		?>
        <form method="get" action="" id="posts-filter">
        <input type="hidden" value="orders" name="page"/>
         <div class="tablenav">


        <div class="alignleft actions">
        <select name="action">
        <option selected="selected" value="">Bulk Actions</option>
        <option value="delete">Delete</option>
        </select>
        <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Apply"/>
        </div>

        <br class="clear"/>
        </div>

		<table cellspacing="0" class="widefat fixed">
		<thead>
		<tr class="thead">
			<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
			<th class="manage-column" id="username" scope="col">Date</th>
			<th class="manage-column" id="name" scope="col">Firstame</th>
			<th class="manage-column" id="email" scope="col">Lastname</th>
			<th class="manage-column" id="role" scope="col">Shipping Address</th>
			<th class="manage-column" id="posts" scope="col">Items</th>
            <th class="manage-column" id="posts" scope="col">Gross</th>
            <th class="manage-column" id="posts" scope="col">Status</th>
            <th class="manage-column" id="posts" scope="col">Invoice</th>
            <th class="manage-column num" id="posts" scope="col">Options</th>
		</tr>
		</thead>

		<tfoot>
		<tr class="thead">
			<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"/></th>
			<th class="manage-column" id="username" scope="col">Date</th>
			<th class="manage-column" id="name" scope="col">Firstame</th>
			<th class="manage-column" id="email" scope="col">Lastname</th>
			<th class="manage-column" id="role" scope="col">Shipping Address</th>
			<th class="manage-column" id="posts" scope="col">Items</th>
            <th class="manage-column" id="posts" scope="col">Gross</th>
            <th class="manage-column" id="posts" scope="col">Status</th>
            <th class="manage-column" id="posts" scope="col">Invoice</th>
            <th class="manage-column num" id="posts" scope="col">Options</th>
		</tr>
		</tfoot>

		<tbody class="list:user user-list" id="users">
		<?php
		foreach ($orders_list as $order) {
		?>


                <tr class="<?php if ($i%2 == 0) echo "alternate";?>" id="prod-<?php echo $i;?>">
                <th class="check-column" scope="row">
                <input type="checkbox" value="1" id="check_<?php echo $i;?>" name="chk_ord[<?php echo $order['id'] ?>]"/>
                <input type="hidden" value="<?php echo $order['id'] ?>" name="order_id[]"/>
                </th>
                <td class="username column-username">
				<?php echo $order['time'] ?>
                </td>
                <td>
                <?php echo $order['firstname'] ?>
                </td>
                <td>
                <?php echo $order['lastname'] ?>
                </td>
                <td><?php echo $order['address'] ?></td>
                <td><?php echo $order['items'] ?></td>
                <td><?php echo $order['gross'] ?></td>
                <td><?php echo $order['status'] ?></td>
                <td><a href='<?php echo JSGALLERY_PLUGIN_URL ?>/templates/default/invoice.php?invoice=<?php echo $order['code'] ?>'><?php echo $order['code'] ?> </a></td>
                <td class="posts column-posts num">
                <a onclick="document.getElementById('show_details_<?php echo $i;?>').style.display='block';return false;" href="#"><strong>Details</strong></a><br />
                <a onclick="document.getElementById('delete_alert_<?php echo $i;?>').style.display='block';return false;" href="#">Delete</a>
                </td>
                </tr>

                <tr>
                <td colspan="9" align="center" width="100%" class="alert">
                 <div id="delete_alert_<?php echo $i;?>" style="display:none;">
                    You are about to delete <strong><?php echo $order['filename'] ?></strong>.
                    <a href="?page=orders&action=delete-ord&id=<?php echo $order['id'] ?>">Continue</a>
                    <a onclick="this.parentNode.style.display='none';return false;" href="#">Cancel</a>
                 </div>

                 <div id="show_details_<?php echo $i;?>" style="display:none; text-align:left; padding-left:100px">
                 <a onclick="this.parentNode.style.display='none';return false;" href="#">Hide</a>
                	<?php echo $order['details'] ?>
                 <a onclick="this.parentNode.style.display='none';return false;" href="#">Hide</a>
                 </div>
                </td>
                </tr>

		<?php
		$i++;
		}

		?>



		</table>

		<div class="tablenav">


		<div class="alignleft actions">
		<select name="action2">
		<option selected="selected" value="">Bulk Actions</option>
		<option value="delete">Delete</option>
		</select>
		<input type="submit" class="button-secondary action" id="doaction2" name="doaction2" value="Apply"/>
		</div>

		<br class="clear"/>
		</div>

		<input id="save-all" class="button-primary savebutton" type="submit" value="Save all changes" name="save"/>

		</form>
     <?php
	} else {

	echo "<h3>There is no orders</h3>";
	echo "<p><a href='admin.php?page=settings'>Go to the Settings Page</a></p>";

	}
    ?>
<br />

<p align="center">Js Gallery - Version <?php echo $jsgallery_options['version'] ?></p>

</div>
<?php

}


function jseccomerce_insert_order($order) {


   global $wpdb;

   $table_name = $wpdb->prefix . "jseccomerce_orders";

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

	  $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  time tinytext  NULL,
	  firstname tinytext  NULL,
	  lastname tinytext  NULL,
	  address text  NULL,
	  items text  NULL,
	  gross  decimal(10, 2) NULL,
	  status tinytext  NULL,
	  details text  NULL,
      code varchar NULL,
	  PRIMARY KEY  (id)
	  );";


	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	  dbDelta($sql);

    }

	//Display Adress
	$address .= "To: ".$order['address_name']."<br />";
	$address .= $order['address_street']."<br />";
	$address .= $order['address_city']."<br />";
	$address .= $order['address_state'].", ".$order['address_zip']."<br />";
	$address .= $order['address_country']."<br />";

	//Display Items
    $items = '';

	for ( $i = 1; $i <= $order['num_cart_items'] ; $i ++) {

		$items .= "<strong>".$order['quantity'.$i]."</strong> ".$order['item_name'.$i]."<br />";

		$option_name1 = $order['option_name1_'.$i];
		$option_selection1 = $order['option_selection1_'.$i];

		if (!empty($option_name1)) $items .= $option_name1.": ".$option_selection1."<br />";

	}

    $validcharscode = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $invoice  = "";
    $counters   = 0;
    $lengths = 10;

   while ($counters < $lengths) {
     $actChars = substr($validcharscode, rand(0, strlen($validcharscode)-1), 1);

     // All character must be different
     if (!strstr($invoice, $actChars)) {
        $invoice .= $actChars;
        $counters++;
     }
   }

    //Display Details
	$details = "<h3>PAYMENT INFO:</h3>";

	$details .= "<strong>payment_status: </strong>".$order['payment_status']."<br />";
	$details .= "<strong>payment_gross: </strong>".$order['payment_gross']."<br />";
	$details .= "<strong>payment_fee: </strong>".$order['payment_fee']."<br />";
	$details .= "<strong>payment_date: </strong>".$order['payment_date']."<br />";
	$details .= "<strong>payment_type: </strong>".$order['payment_type']."<br /><br />";

	$details .= "<strong>receiver_email: </strong>".$order['receiver_email']."<br />";
	$details .= "<strong>receiver_id: </strong>".$order['receiver_id']."<br />";
	$details .= "<strong>verify_sign: </strong>".$order['verify_sign']."<br />";
	$details .= "<strong>business: </strong>".$order['business']."<br />";
	$details .= "<strong>test_ipn: </strong>".$order['test_ipn']."<br />";
	$details .= "<strong>transaction_subject: </strong>".$order['transaction_subject']."<br />";
	$details .= "<strong>txn_type: </strong>".$order['txn_type']."<br />";
	$details .= "<strong>txn_id: </strong>".$order['txn_id']."<br />";
	$details .= "<strong>protection_eligibility: </strong>".$order['protection_eligibility']."<br />";
	$details .= "<strong>address_status: </strong>".$order['address_status']."<br />";
	$details .= "<strong>charset: </strong>".$order['charset']."<br />";


	$details .= "<h3>PAYER INFO:</h3>";

	$details .= "<strong>first_name: </strong>".$order['first_name']."<br />";
	$details .= "<strong>last_name: </strong>".$order['last_name']."<br />";
	$details .= "<strong>payer_email: </strong>".$order['payer_email']."<br />";
	$details .= "<strong>payer_id: </strong>".$order['payer_id']."<br />";
	$details .= "<strong>payer_status: </strong>".$order['payer_status']."<br /><br />";

	$details .= "<strong>address_name: </strong>".$order['address_name']."<br />";
	$details .= "<strong>address_street: </strong>".$order['address_street']."<br />";
	$details .= "<strong>address_city: </strong>".$order['address_city']."<br />";
	$details .= "<strong>address_state: </strong>".$order['address_state']."<br />";
	$details .= "<strong>address_zip: </strong>".$order['address_zip']."<br />";
	$details .= "<strong>address_country: </strong>".$order['address_country']."<br />";
	$details .= "<strong>address_country_code: </strong>".$order['address_country_code']."<br />";


	$details .= "<h3>CART INFO:</h3>";

	$details .= "<strong>mc_gross: </strong>".$order['mc_gross']."<br />";
	$details .= "<strong>mc_fee: </strong>".$order['mc_fee']."<br />";
	$details .= "<strong>mc_currency: </strong>".$order['mc_currency']."<br />";
	$details .= "<strong>mc_shipping: </strong>".$order['mc_shipping']."<br />";
	$details .= "<strong>num_cart_items: </strong>".$order['num_cart_items']."<br />";
    $details .= "<strong>Invoice Number: </strong><a href='". JSGALLERY_PLUGIN_URL ."/templates/default/invoice.php?invoice=".$invoice. "'>".$invoice."</a><br />";

	for ( $i = 1; $i <= $order['num_cart_items'] ; $i ++) {

		$details .= "<strong>ITEM ".$i.":</strong><br />";
		$details .= "<strong>item_number".$i.": </strong>".$order['item_number'.$i]."<br />";
		$details .= "<strong>item_name".$i.": </strong>".$order['item_name'.$i]."<br />";
		$details .= "<strong>mc_gross_".$i.": </strong>".$order['mc_gross_'.$i]."<br />";
		$details .= "<strong>mc_shipping".$i.": </strong>".$order['mc_shipping'.$i]."<br />";
		$details .= "<strong>quantity".$i.": </strong>".$order['quantity'.$i]."<br />";
		$details .= "<strong>quantity".$i.": </strong>".$order['quantity'.$i]."<br />";

		$option_name1 = $order['option_name1_'.$i];
		$option_selection1 = $order['option_selection1_'.$i];

		if (!empty($option_name1))
		$details .= "<strong>".$option_name1.": </strong>".$option_selection1."<br />";

	}

    $subject = 'Recieved Payment and order';
	$to = $order['payer_email'];
	$body =  "An instant payment notification was successfully recieved\n";
	$body .= "from ".get_option('blogname')." on ".date('m/d/Y');
	$body .= " at ".date('g:i A')."\n\nDetails:\n";
	$body .= "\n === Invoice Number <a href='". JSGALLERY_PLUGIN_URL ."/templates/default/invoice.php?invoice=".$invoice. "'>".$invoice."</a> === \n";
    for ( $p = 1; $p <= $order['num_cart_items'] ; $p ++) {
    $body .= "\n === Item Number ".$order['item_number'.$p]." === \n";
    $body .= "\n === Item Name ".$order['item_name'.$p]." === \n";
    //$tbl_name="wp_jseccomerce_products";
    //$where = "WHERE name = '".$order['item_name'.$p]."'";

    $string = $order['item_number'.$p];
    $sub = explode ( "_", $string);
    $idpost = $sub[1];

    $settings = get_jsgallery_options();
    $dir = ABSPATH.$settings['galleries_rootfolder'];
    $url = $settings['galleries_rootfolder']."/gallery/";

    $downloadfile = JSGALLERY_PLUGIN_URL."/includes/download.php";

    $downloadimage = get_post_meta($idpost, 'gallery', true);
    $enabledownload = get_post_meta($idpost, 'download', true);

    //if ($enabledownload == 1) {
    $body .= "\n === Download <a href=\"".$downloadfile.'?file='.base64_encode($url.replaceWhiteSpace($downloadimage))."\">Download</a> === \n";
    //}
    }
    mail($to, $subject, $body);
    $to1 = get_option('admin_email');
    mail($to1, $subject, $body);

	$insert = "INSERT INTO " . $table_name .
                " (
				time,
				firstname,
				lastname,
				address,
				items,
				gross,
				status,
				details,
                code
				) " .
                "VALUES (
				'" .$wpdb->escape($order['payment_date']). "',
				'" .$wpdb->escape($order['first_name']) . "',
				'" .$wpdb->escape($order['last_name']) . "',
				'" .$wpdb->escape($address) . "',
				'" .$wpdb->escape($items) . "',
				'" .$order['mc_gross']. "',
				'" .$wpdb->escape($order['payment_status']). "',
				'" .$wpdb->escape($details) . "',
                '" .$wpdb->escape($invoice) . "'
				);";

      echo $insert;
      $results = $wpdb->query( $insert );
	  if ($results == 1) return __( "New Order Added" );
	  else  return __( "Error Saving This Order" );
}


function jseccomerce_show_orders() {

	global $wpdb;

   	$table_name = $wpdb->prefix . "jseccomerce_orders";

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {

      $orders_row = "SELECT * FROM ". $table_name ." ORDER BY id DESC;";
      	if($results = $wpdb->get_results( $orders_row,"ARRAY_A")){
	  	return $results;
	  	} else {
	  	return false;
	  	}
	} else {
	  return false;
	}

	}

function jseccomerce_delete_order($id) {

	global $wpdb;

   	$table_name = $wpdb->prefix . "jseccomerce_orders";

		$delete = "DELETE FROM ". $table_name ." WHERE id = '".$id."';";
		$results = $wpdb->query($delete);
		if($results == 1){
			return true;
		} else {
			return __("OrderDelete Fail.");
		}

}


function jseccomerce_upload(){

?>

<div class="wrap wpckt">
<h2>Js Gallery - Upload</h2>

<?php

global $jsgallery_options;
$settings = get_jsgallery_options();

//Bulk Delete

if ( ($_REQUEST['action'] == 'insert')){

echo "<form name=\"save-content\" action=\"admin.php?page=upload_gallery&action=save\" method=\"POST\">";
echo "<table width='100%'>";

include(JSGALLERY_PLUGIN_DIR.'/lib/uploader/watemark_zip.php');

$act = 0;
$dir = ABSPATH.$settings['galleries_rootfolder'];

$filezip = $_REQUEST['file_download'];

if ( $_REQUEST['category']){
$cats = (int)$_REQUEST['category'];
} else if ( $_REQUEST['catnew']){
$cats = $_REQUEST['catnew'];
} else {
$cats = '';
}

$array_image1 = explode(".",$filezip);

$patimages = $dir."/".$array_image1[0];

	// Open the actual directory
	if ($handle = opendir($patimages)) {
		// Read all file from the actual directory
		while ($file = readdir($handle))  {
		  if (!is_dir($file)) {

$array_image = explode(".",$file);

$my_post = array(
'post_title' => $array_image[0],
'post_date' => '',
'post_content' => '',
'post_status' => 'publish',
'post_type' => 'gallery',
'post_author' => 1,
//'post_category' => array($cats),
);

//echo $cats;

$post_id = wp_insert_post($my_post);

add_post_meta($post_id, 'gallery', $file, true);
//add_post_meta($post_id, 'META-KEY-2', 'META_VALUE-2', true);

$cat_ids = array( $cats );
wp_set_object_terms(  $post_id, $cat_ids, 'gallerys' );

copy($patimages.'/'.$file, $dir."/originals/".$file);

$watermark = $settings['watermark'];

if ($watermark == 1) {

process_image_upload( $patimages.'/'.$file, $dir, $file );

} else {

copy($patimages.'/'.$file, $dir."/gallery/".$file);

}

echo "<tr><td>Image ".$act."<br />";
$url = get_bloginfo('url')."/".$settings['galleries_rootfolder']."/gallery/";
$gal = "<img src='" .JSGALLERY_PLUGIN_URL. "/includes/timthumb.php?src=". $url.$array_image[0] . ".jpg&amp;h=150&amp;w=200&amp;zc=1' /></td>";
$gal .= "<td>Title: <input name=\"title[".$post_id."]\" type=\"text\" value='".$array_image[0]."' /><br />";
$gal .= "Image descriptions: <br /><textarea name=\"postcontent[".$post_id."]\" rows=\"5\" cols=\"50\"></textarea></td>";
$gal .= "<td>Enable shop: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"shop[".$post_id."]\" value=\"1\" /><br />";
$gal .= "Price: <input name=\"price[".$post_id."]\" type=\"text\" value='' /><br />";
$gal .= "Enable download: <input type=\"checkbox\" name=\"download[".$post_id."]\" value=\"1\" /><br /></td></tr>";

echo $gal;
$act++;
}
}
}
echo "</table>";
echo "Upload complete<br />";
echo "<p align=\"right\"><input type=\"submit\" name=\"save\" value=\"save\" class=\"button-primary savebutton\" /></form></p>";

} else if ( ($_REQUEST['action'] == 'save')){

$title = $_REQUEST['title'];
$postcontent = $_REQUEST['postcontent'];
$shop = $_REQUEST['shop'];
$price = $_REQUEST['price'];
$download = $_REQUEST['download'];

foreach( $title as $key => $value){

$my_post = array(
'ID' => $key,
'post_title' => $value,
'post_content' => $postcontent[$key],
'post_date' => '',
'post_status' => 'publish',
'post_type' => 'gallery',
'post_author' => 1,
//'post_category' => array($cats),
);

//echo $cats;

$post_id = wp_insert_post($my_post);

add_post_meta($post_id, 'shop', $shop[$key], true);
add_post_meta($post_id, 'price', $price[$key], true);
add_post_meta($post_id, 'download', $download[$key], true);

}

echo 'Upload complete! Your new photo was added successfully';

} else {

		?>
        <form method="post" action="admin.php?page=upload_gallery&action=insert" id="posts-filter">
        <input type="hidden" value="upload" name="page"/>

		<table cellspacing="0" class="widefat fixed">
        <tr valign="top">
		<th scope="row"><?php _e('Category', 'jsgallery'); ?></th>
		<td>
        <?php
$args2 = array(
'show_option_all' =>  'Select category',
'show_option_none' => '',
'orderby' => 'ID',
'order' => 'ASC',
'show_last_update' => 0,
'show_count' => 0,
'hide_empty' => 0,
'child_of' => 0,
'exclude' => '',
'echo' => 1,
'selected' => 0,
'hierarchical' => 1,
'name' => 'category',
'id' => '',
'class' => 'postform',
'depth' => 0,
'tab_index' => 0,
'taxonomy' => 'gallerys',
'hide_if_empty' => false );



wp_dropdown_categories( $args2 );
?> Or Create category 	<input type="text" name="catnew" id="catnew" value="" />
</td>
</tr>
         <tr valign="top">
							<th scope="row"><?php _e('Upload Zip', 'jsgallery'); ?>
                            <br />You can upload a zip file with all your images and assign to an existing category or create a new one.
If you want to assign them to a new category, the system generate all the page for you and you need only to add the information to every page.
                            </th>
							<td>
								<fieldset>
                                    <label for="file_download">
										<input type="text" name="file_download" id="file_download" value="" />
                                        <div class="demo-box">
                                        <div id="status-message2">Select some files to upload:</div>
                                        <div id="custom-queue1"></div>
                                        <input id="custom_file_upload2" type="file" name="Filedata" />        </div>
										<?php _e('Upload Zip', 'jsgallery'); ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>

		</table>



		<input id="save-all" class="button-primary savebutton" type="submit" value="save" name="save"/>

		</form>
     <?php

	}
    ?>
<br />

<p align="center">Js Gallery - Version <?php echo $jsgallery_options['version'] ?></p>

</div>
<?php

}

?>