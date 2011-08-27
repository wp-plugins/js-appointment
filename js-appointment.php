<?php
/*
Plugin Name: js-appointment
Plugin URI: http://www.joomlaskin.it
Description: Wordpress booking online Manage, appointments, your Hotel, Bed and Breakfast or Motel directly in Wordpress! Activate the plugin and create a page which includes the text {ALLBOOK}
Version: 1.5
Author: Joomlaskin
Author URI: http://www.joomlaskin.it
*/

/*  Copyright 2010 Joomlaskin

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

error_reporting(0);

//print_r($_REQUEST);

//session_start();

foreach($_REQUEST as $key => $value){
	if(get_magic_quotes_gpc()){$value = stripslashes($value);}
	if (is_array($value)) {
		foreach($value as $key2 => $value2){
			$_REQUEST[$key] = mysql_real_escape_string(trim($value2));
		} } }



define ("PLUGIN_DIR_ALLBOOK", basename(dirname(__FILE__)));
define ("PLUGIN_URL_ALLBOOK", get_settings("siteurl")."/wp-content/plugins/".PLUGIN_DIR_ALLBOOK);
define ("PLUGIN_PATH_ALLBOOK",ABSPATH."wp-content/plugins/".PLUGIN_DIR_ALLBOOK);
add_action("admin_init", "allbook_admin_init");
add_action("admin_menu", "allbook_menu");
add_action("admin_head", "allbook_add_admin" );
add_action("wp_head", "allbook_add_head" );
add_filter("the_content","allbook_insert");
add_filter("plugin_action_links", "allbook_links", 10, 2 );
register_activation_hook(__FILE__,'allbook_activate');

$allbook_file_plugin = "js-appointment/js-appointment.php";
add_action("deactivate_" . $allbook_file_plugin, "allbook_deactivate");
add_action("activate_" . $allbook_file_plugin,  "allbook_activate");

$langplugin = get_bloginfo('language');
$filelang = PLUGIN_PATH_ALLBOOK."/lang/".$langplugin.".php";

if (file_exists($filelang)) {
    include($filelang);
} else {
    include(PLUGIN_PATH_ALLBOOK."/lang/en-US.php");
}

//require_once PLUGIN_PATH_ALLBOOK.'/admin/jsevent_admin.php';


//$GLOBALS["version"] = "free";
//if (file_exists(PLUGIN_PATH. '/wp-res-pro.php')) { require_once(PLUGIN_PATH. "/wp-res-pro.php" );  $GLOBALS["version"] = "pro";}
//
//function allbook_adm_init() {
//	if ($_REQUEST['page']=="resources")
//	wp_enqueue_script('jquery-form');
//	wp_enqueue_script('jqtreetable', PLUGIN_URL.'/js/jQTreeTable/jqtreetable.js');
//	wp_enqueue_script('jquery-ui-dialog');
//	wp_enqueue_script('jquery-ui-resizable');
//
//
//	//<script src="'.PLUGIN_URL.'/js/jQTreeTable/jqtreetable.js" type="text/javascript"></script>';
//
//}


function AllbookresLocale($locale = "") {
	global $locale;

		$mofile = PLUGIN_PATH_ALLBOOK  .'/lang/'.PLUGIN_DIR_ALLBOOK.'-'.$locale.'.mo';
		return load_textdomain(PLUGIN_DIR_ALLBOOK, $mofile);

	if ( empty( $locale ) ) $locale = get_locale();
	if ( !empty( $locale ) ) {

		$mofile = PLUGIN_PATH_ALLBOOK  .'/lang/'.PLUGIN_DIR_ALLBOOK.'-'.$locale.'.mo';

		if (file_exists($mofile))    return load_textdomain(PLUGIN_DIR_ALLBOOK, $mofile);
		else                        return false;
	} return
	false;
}


if ( !function_exists('wp_sanitize_redirect') ) :
function wp_sanitize_redirect($location) {
	$location = preg_replace('|^a-z0-9-~+_.?#=&;,/:%!|i', '', $location);
	$location = wp_kses_no_null($location);


	$strip = array('%0d', '%0a', '%0D', '%0A');
	$location = _deep_replace($strip, $location);
	return $location;
}
endif;

function allbook_links($links, $file){

	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(dirname(__FILE__).'/wp-allbooking.php');

	if ($file == $this_plugin){
		$settings_link1 = '<a href="admin.php?page='.$this_plugin.'settings">' . __('Settings', 'wp-allbooking') . '</a>';
		//$settings_link2 = '<a href="http://reservation.isaev.asia/donate">' . __('Donate!', 'wp-allbooking') . '</a>';
		array_unshift( $links, $settings_link2 );
		//array_unshift( $links, $settings_link1 );
	}
	return $links;
}


function allbook_add_head()
{

AllbookresLocale() ;

	echo '
<link type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/assets/css/smoothness/jquery-ui-1.8.9.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/css/style.css" />
<link href="'.PLUGIN_URL_ALLBOOK.'/css/style/style.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="'.PLUGIN_URL_ALLBOOK.'/css/style/ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<link rel="stylesheet" href="'.PLUGIN_URL_ALLBOOK.'/assets/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
    <script language="JavaScript" type="text/javascript">
    /*<![CDATA[*/
    $(document).ready(function(){
    $(\'#date\').datepicker({ dateFormat: \'yy-mm-dd\' });
	$(".cart").fancybox({
		 \'width\' : 700,
		 \'height\' : 300,
		 \'autoScale\' : false,
		 \'transitionIn\' : \'none\',
		 \'transitionOut\' : \'none\',
		 \'type\' : \'iframe\' ,
         \'onComplete\': function() {
        $("#fancybox-wrap, #fancybox-overlay").delay(10000).fadeOut();
        }
	 });

	 });
     $("#fancybox-close").click(function() {
     $(\'#fancybox-overlay\').stop();
     $(\'#fancybox-wrap\').stop();
     });
    /*]]>*/
    </script>
<script language="JavaScript" type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/jcal/jquery.color.js"></script>
<script language="JavaScript" type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/jcal/jquery.animate.clip.js"></script>
<script language="JavaScript" type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/jcal/jCal.js"></script>
<link rel="stylesheet" type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/assets/jcal/jCal.css">
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/jquery.validate.js"></script>
<script>
  $(document).ready(function(){
    $("#bookingsave").validate();
  });
  </script>
';
}

function allbook_add_admin()
{

AllbookresLocale();
if ($_REQUEST["page"]=="allbooking") {
 echo '<link href="'.PLUGIN_URL_ALLBOOK.'/css/res/admin.css" rel="stylesheet" type="text/css" /> ';
 echo '
<link type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/assets/css/smoothness/jquery-ui-1.8.9.custom.css" rel="Stylesheet" />
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/js/jquery-ui-1.8.9.custom.min.js"></script>
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/timepicker/jquery.ui.timepicker.js"></script>
<link rel="Stylesheet" type="text/css" href="'.PLUGIN_URL_ALLBOOK.'/assets/timepicker/jquery-ui-timepicker.css" />
';
echo '<script>
	$(function() {
		$( "#tabs" ).tabs();
        $(\'#date\').datepicker({ dateFormat: \'yy-mm-dd\' });
        $(\'#date1\').datepicker({ dateFormat: \'yy-mm-dd\' });
        $(\'#date2\').datepicker({ dateFormat: \'yy-mm-dd\' });
        $(\'#date5\').datepicker({ dateFormat: \'yy-mm-dd\' });
	});
	</script>
';
echo '
<script type="text/javascript" src="'.PLUGIN_URL_ALLBOOK.'/assets/jquery.validate.js"></script>
<script>
  $(document).ready(function(){
    $("#rangetime").validate();
    $("#createdate").validate();
    $("#optionsform").validate();
  });
  </script>
';
}
}

add_filter('admin_head', 'add_js_editor');
 function add_js_editor(){
     wp_enqueue_script('common');
     wp_print_scripts('editor');
     if (function_exists('add_thickbox')) add_thickbox();
     wp_print_scripts('media-upload');
     if (function_exists('wp_tiny_mce')) wp_tiny_mce();
     wp_enqueue_script('utils');
     do_action("admin_print_styles-post-php");
     do_action('admin_print_styles');
 }



function allbook_insert($content)
{
	if (preg_match('{ALLBOOK}',$content))
	{
		$allbook_output = allbook_task();
		$content = str_replace('{ALLBOOK}',$allbook_output,$content);
	}
    else if (preg_match('#{CATBOOK (.*?)}#s',$content))
    {
    $regex = '#{CATBOOK (.*?)}#s';
	preg_match_all( $regex, $content, $matches );
	for($x=0; $x<count($matches[0]); $x++)
	{
		$parts = explode(" ", $matches[1][$x]);
		if(count($parts) > 0)
		{
			//$vid= explode('=',$parts[0]);
			$catid = $parts[0];

//session_start();
//require_once(PLUGIN_PATH_ALLBOOK."/calendar.class.php");
$url=get_bloginfo('wpurl');
$settings = allbook_get_settings();

$replace = '
<div id="eventwrap">
'.AppointmentTypePost($catid).'
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
'.AppointmentDesc($catid).'
<div id="result"></div>
<script>
$("form#cart-ajax").submit(function(){
  $.post("'.PLUGIN_URL_ALLBOOK.'/cart-ajax.php",{
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
';
		}

		$content = str_replace($matches[0][$x], $replace, $content);
	}
    }
	return $content;
}



function allbook_task()
{
switch ($_REQUEST['pager']) {
	case 'start': echo allbook_page();        break ;
    case 'checkout': echo allbook_user_page();        break ;
    case 'conf': echo allbook_pageconf();        break ;
    case 'done': echo allbook_pagedone();        break ;
    case 'cancel': echo allbook_pagecancell();        break ;
    case 'sellaok': echo allbook_pagedonesella();        break ;
    case 'sellano': echo allbook_pagedonesellano();        break ;
    case 'dettails': echo allbook_dettails();        break ;
    case 'home': echo allbook_home();        break ;
    case 'cart': echo allbook_cart();        break ;
    case 'options': echo allbook_opt();        break ;
    default:  echo allbook_cart();        break;
	}
}

function allbook_page()
{
session_start();
//require_once(PLUGIN_PATH_ALLBOOK."/calendar.class.php");
$action=isset($_REQUEST['action'])?$_REQUEST['action']:"";
$view_type=isset($_REQUEST['view_type'])?$_REQUEST['view_type']:"";
$year=isset($_REQUEST['year'])?$_REQUEST['year']:"";
$month=isset($_GET['month'])?$_GET['month']:"";
$day=isset($_GET['day'])?$_GET['day']:"";
$settings = allbook_get_settings();
$allbook_width = $settings['allbook_width'];
$allbook_height = $settings['allbook_height'];
$cat=isset($_REQUEST['cat'])?$_REQUEST['cat']:"1";


	?>

<?php

}

function allbook_user_page()
{

global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');

$checkout = $_REQUEST["checkout"];
if ($checkout == "checkout") {
include PLUGIN_PATH_ALLBOOK.'/checkout.php';
?>

<? } else {
 include PLUGIN_PATH_ALLBOOK.'/checkout.php';
}
?>
<?php

}

function allbook_opt()
{

global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');

$checkout = $_REQUEST["checkout"];
if ($checkout == "checkout") {
include PLUGIN_PATH_ALLBOOK.'/options.php';
?>

<? } else {
 include PLUGIN_PATH_ALLBOOK.'/options.php';
}
?>
<?php

}

function allbook_pageconf()
{
//session_start();

global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');
//ob_start();

?>
<?
$action = $_REQUEST["action"];
    if ($action == "book") {
include PLUGIN_PATH_ALLBOOK.'/bookingsave.php';
   ?>
                <?php
                $filenamepp = PLUGIN_PATH_ALLBOOK.'/booking/payment_processing.php';
                ?>
        	<? if ($paypal == 1 && $paypal_email!="" && $total!="" && file_exists($filenamepp)) {
				echo "Full payment:&nbsp;&nbsp;<b>&euro;".$total."</b><br/><br/>";
			?>
				<form  action="https://www.paypal.com/cgi-bin/webscr" method="post" name="payform" target="_blank">
					<input type="hidden" name="cmd" value="_xclick">
					<input type="hidden" name="custom" value="<?= $invoice ?>">
					<input type="hidden" name="business" value="<?= $paypal_email() ?>">
					<input type="hidden" name="lc" value="US">
					<input type="hidden" name="item_name" value="Booking Payment">
					<input type="hidden" name="amount" value="<?= $total;?>">
					<input type="hidden" name="currency_code" value="USD">
					<input type="hidden" name="cn" value="Add special instructions to the seller">
					<input type="hidden" name="no_shipping" value="1">
					<input type="hidden" name="rm" value="1">
					<input type="hidden" name="notifiy" value="<?php echo PLUGIN_DIR_ALLBOOK_ALLBOOK ?>/booking/payment_processing.php">
					<input type="hidden" name="return" value="<?php echo get_settings("siteurl") ?>/<?php echo 'index.php?page_id='.$post_id ?>&pager=done">
					<input type="hidden" name="cancel_return" value="<?php echo get_settings("siteurl") ?>/<?php echo 'index.php?page_id='.$post_id ?>&pager=cancel">
					<input type="hidden" name="bn" value="PP-BuyNowBF">

					<input style='cursor:pointer;' type="image"  src="https://www.paypal.com/en_US/i/bnr/horizontal_solution_PPeCheck.gif" border="0" name="submit" alt="">
					<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
                <? if ($bancasella == 1) {   ?>
                <?php
                $filename = PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php';
                if (file_exists($filename)) {
                 include PLUGIN_PATH_ALLBOOK.'/booking/bancasella/request_tot.php';
                 }
                 }
                ?>
            <? } else if ($bancasella == 1 && file_exists(PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php')) {  ?>
                            <?php
                $filename = PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php';
                if (file_exists($filename)) {
                 include PLUGIN_PATH_ALLBOOK.'/booking/bancasella/request_tot.php';
                 }
                 ?>
			<? } ?>

<? } else {
  echo "<script>document.location.href='index.php'</script>";
}
?>
<?php

}

function allbook_pagedone()
{
session_start();
?>
<?php echo CONFIRM ?>
<?php

$_SESSION=array(); // Desetta tutte le variabili di sessione.
session_destroy(); //DISTRUGGE la sessione.

?>
<?php
}

function allbook_pagecancell()
{
session_start();
?>
<?php echo CANCELLED ?>
<?php

$_SESSION=array(); // Desetta tutte le variabili di sessione.
session_destroy(); //DISTRUGGE la sessione.

?>

<?php
}

function allbook_pagedonesella()
{
$filename = PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php';
                if (file_exists($filename)) {
                 include PLUGIN_PATH_ALLBOOK.'/booking/bancasella/response.php';
                 }
}

function allbook_pagedonesellano()
{
$filename = PLUGIN_PATH_ALLBOOK.'/booking/bancasella/GestPayCrypt.inc.php';
                if (file_exists($filename)) {
                 include PLUGIN_PATH_ALLBOOK.'/booking/bancasella/cancel.php';
                 }
}


function allbook_dettails()
{

}

function allbook_home()
{

}

function allbook_cart()
{
include PLUGIN_PATH_ALLBOOK.'/cart.php';
}

function allbook_menu() {

global $submenu, $menu;
AllbookresLocale() ;

if ( strpos($_SERVER['HTTP_HOST'],'isaev.asia') !== FALSE ) {$user_role_plugin = $user_role_settings  = 0 ; } else { $user_role_plugin = get_option("allbook_security_plugin") ; $user_role_settings = get_option("allbook_security_settings") ;}




	add_menu_page(__("Reservation","js-appointment"), __("Js Appointment","js-appointment"), $user_role_plugin,"allbooking" , 'allbook_options',PLUGIN_URL_ALLBOOK."/img/ico16x16.png");
//	add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Configuration","wp-allbooking"), $user_role_plugin,"conf", 'allbook_options');
//	add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Site config","wp-allbooking"), $user_role_plugin, "siteconf", 'allbook_options');
//	add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Hotel","wp-allbooking"), $user_role_plugin, "hotel", 'allbook_options');
//	add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Room Types","wp-allbooking"), $user_role_settings, "room", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Seasons","wp-allbooking"), $user_role_settings, "seasons", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Bookings","wp-allbooking"), $user_role_settings, "bookings", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Availability Rooms","wp-allbooking"), $user_role_settings, "availability", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Bookings history","wp-allbooking"), $user_role_settings, "history", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Bookings stats","wp-allbooking"), $user_role_settings, "stats", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Language","wp-allbooking"), $user_role_settings, "lang", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Email template","wp-allbooking"), $user_role_settings, "tempemail", 'allbook_options');
//    add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Constants","wp-allbooking"), $user_role_settings, "constants", 'allbook_options');
//    //add_submenu_page("settings", __("Reservation","wp-allbooking"), __("Menu","wp-allbooking"), $user_role_plugin, "menumanager", 'allbook_options');
//	$submenu[plugin_basename( "settings" )][0][0] = __("Reservation","wp-allbooking");



	}




function allbook_options() {

echo '<div class="wrap">';
echo allbook_menu_admin();


	if(isset($_REQUEST['page']))  switch ($_REQUEST['page']) {

		//case "resources" : 	adm_resources(); break;
		case "allbooking"  :  allbook_adm_settings(); break;
        case "conf"  :  allbook_adm_configuration(); break;
        case "menumanager"  :  allbook_adm_menumanager(); break;
        case "hotel"  :  allbook_adm_hotel(); break;
        case "room"  :  allbook_adm_roomtype(); break;
        case "seasons"  :  allbook_adm_seasons(); break;
        case "bookings"  :  allbook_adm_booking(); break;
        case "availability"  :  allbook_adm_avv(); break;
        case "history"  :  allbook_adm_history(); break;
        case "stats"  :  allbook_adm_stat(); break;
        case "lang"  :  allbook_adm_lang(); break;
        case "tempemail"  :  allbook_adm_tempemail(); break;
        case "constants"  :  allbook_adm_constants(); break;
        case "siteconf"  :  allbook_adm_siteconf(); break;

	}
	else
		allbook_adm_settings();

echo '</div>';



}

function allbook_activate()
{

	global $wpdb;

    $table_name = "wp_resservation_book";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cognome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `indirizzo` text COLLATE utf8_unicode_ci NOT NULL,
  `cap` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `citta` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `prov` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nazione` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `telefono` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data_book` datetime NOT NULL,
  `data` datetime NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `note` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `code` text COLLATE utf8_unicode_ci NOT NULL,
  `id_book` int(10) NOT NULL,
  `price` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `invoice` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `options` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
	}


    $table_name = "wp_resservation_cat";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_start_cat` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time_end_cat` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rangetime` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql = "INSERT INTO $table_name (`id`, name`, `time_start_cat`, `time_end_cat`, `rangetime`) VALUES
(1, 'Dinner', '20:00', '20:00', '60'),
(7, 'Lunch', '12:00', '12:00', '60'),
(6, 'Breakfast', '08:00', '08:00', '60');
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);   }


    $table_name = "wp_resservation_disp";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
   `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `max` text COLLATE utf8_unicode_ci NOT NULL,
  `price` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `category` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=184;
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

$sql = "INSERT INTO $table_name (`id`, `date`, `time_start, `time_end`, `max`, `price`, `status`, `description`, `category`) VALUES
(183, '2011-04-21', '09:00:00', '10:00:00', '7', '9', 1, 'Breakfast', 6),
(182, '2011-04-21', '08:00:00', '09:00:00', '7', '9', 1, 'Breakfast', 6),
(181, '2011-03-23', '13:00:00', '14:00:00', '8', '6', 1, 'Lunch', 7),
(180, '2011-03-23', '12:00:00', '13:00:00', '8', '6', 1, 'Lunch', 7),
(179, '2011-03-31', '21:00:00', '22:00:00', '7', '56', 1, 'Dinner', 1),
(178, '2011-03-31', '20:00:00', '21:00:00', '7', '56', 1, 'Dinner', 1),
(177, '2011-03-30', '09:00:00', '10:00:00', '3', '10', 1, 'Breakfast', 6),
(176, '2011-03-30', '08:00:00', '09:00:00', '3', '10', 1, 'Breakfast', 6),
(174, '2011-03-26', '20:00:00', '21:00:00', '9', '12', 1, 'Dinner', 1),
(173, '2011-03-16', '09:00:00', '10:00:00', '4', '5', 1, 'Breakfast', 6),
(172, '2011-03-16', '08:00:00', '09:00:00', '4', '5', 1, 'Breakfast', 6),
(171, '2011-03-15', '13:00:00', '14:00:00', '5', '34', 1, 'Lunch', 7),
(170, '2011-03-15', '12:00:00', '13:00:00', '4', '34', 1, 'Lunch', 7),
(169, '2011-03-14', '09:00:00', '10:00:00', '3', '43', 1, 'Breakfast', 6),
(168, '2011-03-14', '08:00:00', '09:00:00', '3', '43', 1, 'Breakfast', 6),
(167, '2011-03-13', '20:00:00', '21:00:00', '0', '21', 1, 'Dinner', 1);
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);   }

$table_name = "wp_resservation_invoice";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
   `id` int(10) NOT NULL AUTO_INCREMENT,
  `book_id` text COLLATE utf8_unicode_ci NOT NULL,
  `invocie` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(1) NOT NULL,
  `qty` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `trans_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `trans_date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
";
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);   }

    $table_name = "wp_resservation_opt";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);   }

    $table_name = "wp_resservation";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
    $sql = "CREATE TABLE $table_name (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `groupid` text COLLATE utf8_unicode_ci NOT NULL,
  `eventdesc` text COLLATE utf8_unicode_ci NOT NULL,
  `gallery` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);   }

add_option("allbook_db_version", "1.0");
add_option("allbook_calendar_color","gold");
add_option("allbook_security_plugin","7");
add_option("allbook_security_settings","10");

add_option("allbook_paypal","paypal@email.com");
add_option("allbook_paypal_active","1");
add_option("allbook_datastart","9:30");
add_option("allbook_dataend","17:30");
add_option("allbook_datarange","30 mins");
add_option("allbook_cash_active","1");
add_option("allbook_customemail","Text");
add_option("allbook_width","500px");
add_option("allbook_height","500px");
add_option("allbook_invoice","Text");
add_option("allbook_pageid","Text");
add_option("allbook_valute","USD");
add_option("allbook_catinit","1");
add_option("allbook_indirizzo","1");
add_option("allbook_cap","1");
add_option("allbook_citta","1");
add_option("allbook_provincia","1");
add_option("allbook_nazione","1");
add_option("allbook_telefono","1");
add_option("allbook_valute_simbolo","$");

$wpdb->query($sql);

update_option("allbook_db_version", "1.0");
}


function allbook_deactivate()
{


}

function allbook_get_settings() {

	$AllbookSettingsArray=array(

		'allbook_db_version'						=> get_option('allbook_db_version'),

		'allbook_security_plugin'					=> get_option('allbook_security_plugin'),
		'allbook_security_settings'						=> get_option('allbook_security_settings'),
		'allbook_paypal'				=> get_option('allbook_paypal'),
        'allbook_paypal_active'				=> get_option('allbook_paypal_active'),
        'allbook_datastart'				=> get_option('allbook_datastart'),
        'allbook_dataend'				=> get_option('allbook_dataend'),
        'allbook_datarange'				=> get_option('allbook_datarange'),
        'allbook_cash_active'				=> get_option('allbook_cash_active'),
        'allbook_customemail'				=> get_option('allbook_customemail'),
        'allbook_width'				=> get_option('allbook_width'),
        'allbook_height'				=> get_option('allbook_height'),
        'allbook_invoice'				=> get_option('allbook_invoice'),
        'allbook_pageid'				=> get_option('allbook_pageid'),
        'allbook_valute'				=> get_option('allbook_valute'),
        'allbook_catinit'				=> get_option('allbook_catinit'),
        'allbook_indirizzo'				=> get_option('allbook_indirizzo'),
        'allbook_cap'				=> get_option('allbook_cap'),
        'allbook_citta'				=> get_option('allbook_citta'),
        'allbook_provincia'				=> get_option('allbook_provincia'),
        'allbook_nazione'				=> get_option('allbook_nazione'),
        'allbook_telefono'				=> get_option('allbook_telefono'),
        'allbook_valute_simbolo'				=> get_option('allbook_valute_simbolo'),

		'allbook_uninstall'							=> get_option('allbook_uninstall')

	);

	return $AllbookSettingsArray;

}

function allbook_admin_init() {

		register_setting('allbook-options', 'allbook_paypal');
        register_setting('allbook-options', 'allbook_paypal_active');
        register_setting('allbook-options', 'allbook_datastart');
        register_setting('allbook-options', 'allbook_dataend');
        register_setting('allbook-options', 'allbook_datarange');
        register_setting('allbook-options', 'allbook_cash_active');
        register_setting('allbook-options', 'allbook_customemail');
        register_setting('allbook-options', 'allbook_width');
        register_setting('allbook-options', 'allbook_height');
        register_setting('allbook-options', 'allbook_invoice');
        register_setting('allbook-options', 'allbook_pageid');
        register_setting('allbook-options', 'allbook_valute');
        register_setting('allbook-options', 'allbook_catinit');
        register_setting('allbook-options', 'allbook_indirizzo');
        register_setting('allbook-options', 'allbook_cap');
        register_setting('allbook-options', 'allbook_citta');
        register_setting('allbook-options', 'allbook_provincia');
        register_setting('allbook-options', 'allbook_nazione');
        register_setting('allbook-options', 'allbook_telefono');
        register_setting('allbook-options', 'allbook_valute_simbolo');




}

function allbook_pass ()
{

	$fp = file ("pass.txt",1);
	return (trim($fp[rand(0,count($fp)-1)]));

}




/////////////////////////////////////////////////////////////////////////////
//					Admin page section						//
/////////////////////////////////////////////////////////////////////////////


function allbook_menu_admin () {
	global $wpdb;

    //require (PLUGIN_PATH_ALLBOOK."/booking/admin/hometext.php");

	$pageadr=$_REQUEST['page'];
	if ($_REQUEST["page"]) $divid=$_REQUEST["page"]; else $divid="allbooking";
	if ($_REQUEST["page"]=="suborder" || $_REQUEST["page"]=="mail") $divid="allbooking";
	if ($_REQUEST["page"]=="makeorder1" || $_REQUEST["page"]=="makeorder2")  $divid="makeorder";

?>
	<br>
    <div id="tabs">
	<ul>
	<li><a href="#settings"><?php echo CONFIGURATION ?></a></li>
    <li><a href="#appointment">Appointment</a></li>
    <li><a href="#dates"><?php echo LISTDATE ?></a></li>
    <li><a href="#create"><?php echo CREATEAVAILABILITY ?></a></li>
    <li><a href="#lisbook"><?php ECHO HISTORY ?></a></li>
    <li><a href="#category"><?php echo CATEGORY ?></a></li>
	</ul>
    <div id="settings">
    <form method="post" action="options.php" id="options">

    <?php

		wp_nonce_field('update-options');
		settings_fields('allbook-options');
        $settings = allbook_get_settings();
		?>

        <table class="form-table" style="clear:none;">
					<tbody>
                    <tr valign="top">
							<th scope="row">currency symbol</th>
							<td>
								<fieldset>

									<label for="allbook_valute_simbolo">
										<input type="text" name="allbook_valute_simbolo" id="allbook_valute_simbolo" value="<?php echo $settings['allbook_valute_simbolo']; ?>" />
										currency symbol
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo EMAILCUSTOM ?></th>
							<td>
								<fieldset>

									<label for="allbook_customemail">
                                    <div id="poststuff">
                                    <div id="postdivrich">
                                    <?php the_editor($settings['allbook_customemail'], $id = 'allbook_customemail', $prev_id = 'title', $media_buttons = true, $tab_index = 2); ?>
                                    </div>
                                    </div>
										<?php echo DEFAULTEMAIL ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo PAGEID ?></th>
							<td>
								<fieldset>

									<label for="allbook_pageid">
										<input type="text" name="allbook_pageid" id="allbook_pageid" value="<?php echo $settings['allbook_pageid']; ?>" />
										<?php echo DEFAULTPAGEID ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo CATEGORYSTART ?></th>
							<td>
								<fieldset>

									<label for="allbook_catinit">
										<input type="text" name="allbook_catinit" id="allbook_catinit" value="<?php echo $settings['allbook_catinit']; ?>" />
										<?php echo DEFAULTCATEGORY ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATEADRESS ?></th>
							<td>
								<fieldset>

									<label for="allbook_catinit">
										<p><input name="allbook_indirizzo" type="radio" value="1" <?php if ($settings['allbook_indirizzo'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_indirizzo" type="radio" value="0" <?php if ($settings['allbook_indirizzo'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTADRESS ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATEAZIP ?></th>
							<td>
								<fieldset>

									<label for="allbook_cap">
										<p><input name="allbook_cap" type="radio" value="1" <?php if ($settings['allbook_cap'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_cap" type="radio" value="0" <?php if ($settings['allbook_cap'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTZIP ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATECITY ?></th>
							<td>
								<fieldset>

									<label for="allbook_citta">
										<p><input name="allbook_citta" type="radio" value="1" <?php if ($settings['allbook_citta'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_citta" type="radio" value="0" <?php if ($settings['allbook_citta'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTCITY ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATEPROVINCE ?></th>
							<td>
								<fieldset>

									<label for="allbook_provincia">
										<p><input name="allbook_provincia" type="radio" value="1" <?php if ($settings['allbook_provincia'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_provincia" type="radio" value="0" <?php if ($settings['allbook_provincia'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTPROVINCE ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATECOUNTRY ?></th>
							<td>
								<fieldset>

									<label for="allbook_nazione">
										<p><input name="allbook_nazione" type="radio" value="1" <?php if ($settings['allbook_nazione'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_nazione" type="radio" value="0" <?php if ($settings['allbook_nazione'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTCOUNTRY ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>
                        <tr valign="top">
							<th scope="row"><?php echo VALIDATEPHONE ?></th>
							<td>
								<fieldset>

									<label for="allbook_telefono">
										<p><input name="allbook_telefono" type="radio" value="1" <?php if ($settings['allbook_telefono'] == "1" ) echo checked ?>  /> <strong><?php echo YES ?></strong></p>
                                        <p><input name="allbook_telefono" type="radio" value="0" <?php if ($settings['allbook_telefono'] == "0" ) echo checked ?> /> <strong><?php echo NO ?></strong></p>
										<?php echo DEFAULTPHONE ?>
									</label><br /><br />
                                    </fieldset>
							</td>
						</tr>

					</tbody>
				</table>
                <p class="submit" style="text-align:center;">
			<input type="submit" name="Submit" class="button-primary" value="<?php echo SAVECHANGES ?>" />
		</p>
    </form>
</div>
<div id="appointment">
<?php
$action = $_GET['appointment'];
switch ($action) {
case 'list':
default:
?>
<input type=button onClick="location.href='admin.php?page=allbooking&appointment=new#appointment'" value='New appointment'>
<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
function CheckAll2(chk)
{
for (i = 0; i < chk.length; i++)
chk[i].checked = true ;
}

function UnCheckAll2(chk)
{
for (i = 0; i < chk.length; i++)
chk[i].checked = false ;
}
/*]]>*/
</script>

<?php
	$tbl_name="wp_resservation";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&appointment=list"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$pagelist = $_GET['pagelist'];
	if($pagelist)
		$start = ($pagelist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, title, groupid, eventdesc FROM $tbl_name $where LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($pagelist == 0) $pagelist = 1;					//if no page var is given, default to 1.
	$prev = $pagelist - 1;							//previous page is page - 1
	$next = $pagelist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($pagelist > 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$prev#appointment\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $pagelist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&pagelist=$counter#appointment\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($pagelist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#appointment\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#appointment\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#appointment\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $pagelist && $pagelist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#appointment\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#appointment\">2</a>";
				$pagination.= "...";
				for ($counter = $pagelist - $adjacents; $counter <= $pagelist + $adjacents; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#appointment\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#appointment\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#appointment\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#appointment\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#appointment\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#appointment\">$counter</a>";
				}
			}
		}

		//next button
		if ($pagelist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$next#appointment\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
<form name="listapp" action="admin.php?page=allbooking&appointment=deleteall#appointment" method="post">
<div style='float:right; margin-right:680px; margin-top:-39px;'>
<input type="button" name="Check_All2" value="Check All"
onClick="CheckAll2(document.listapp['check_listapp[]'])">
<input type="button" name="Un_CheckAll2" value="Uncheck All"
onClick="UnCheckAll2(document.listapp['check_listapp[]'])">
<input type="submit" name="delete" value="delete" />
</div>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo appointment ?></td>
 <td class='resint'><?php echo CATEGORY ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 <td class='resint'><input type="hidden" name="check_listapp[]" value=''></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{

        $categoryrow = mysql_query("SELECT * FROM wp_resservation_cat WHERE id=".$row['groupid']."");
        $rowcategory = mysql_fetch_array( $categoryrow );

        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['title']."</td>";
        echo "<td class='resint'>".$rowcategory['name']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&appointment=edit&id=".$row['id']."#appointment\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&appointment=delete&del=yes&id=".$row['id']."#appointment\">".DELETE."</a></td>";
        echo "<td class='resint'><input type=\"checkbox\" name=\"check_listapp[]\" value='".$row['id']."'></td>";

        echo "</tr>";
		}
	?>
</table>
<div style='float:right; margin-right:680px;'>
<input type="button" name="Check_All" value="Check All"
onClick="CheckAll(document.listcategory['check_list[]'])">
<input type="button" name="Un_CheckAll" value="Uncheck All"
onClick="UnCheckAll(document.listcategory['check_list[]'])">
<input type="submit" name="delete" value="delete" />
</div>
</form>
<?=$pagination?> <br />
<?php
break;
case 'new':
$settings = allbook_get_settings();
$category = mysql_query("SELECT * FROM wp_resservation_cat");
?>
<script type="text/javascript">

            jQuery(document).ready( function () {
                jQuery("#descriptions").addClass("mceEditor");
                if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
                    tinyMCE.execCommand("mceAddControl", false, "descriptions");
                }
            });

            jQuery(document).ready(function($) {



	$('a.toggleVisual-descriptions').click(
		function() {
			tinyMCE.execCommand('mceAddControl', false, 'descriptions');
		}
	);

	$('a.toggleHTML-descriptions').click(
		function() {
			tinyMCE.execCommand('mceRemoveControl', false, 'descriptions');
		}
	);

});


</script>
<form name="appointment" action="admin.php?page=allbooking&appointment=savenew#appointment" method="POST">
<table>
  <tr>
    <td width='100'>Title</td>
    <td><input name="title" type="text" id="title" /></td>
  </tr>
  <tr>
    <td width='100'>Descriptions:</td>
    <td><textarea name="descriptions" id="descriptions" rows="5" coll="10"></textarea>  </td>
  </tr>
    <tr>
    <td width='100'><?php echo CATEGORY ?></td>
    <td><select name="category">
<?php
while($row1 = mysql_fetch_array($category))
        {
            /*** create the options ***/
            echo '<option value="'.$row1['id'].'"';
            echo '>'. $row1['name'] . '</option>'."\n";
        }

?>
         </select></td>
  </tr>
</table>
<input type="submit" name="new" value="new" />
</form>
<?php
break;
case 'edit':
$id = $_REQUEST["id"];
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, title, groupid, eventdesc FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);
?>
 <table>
 <script type="text/javascript">

            jQuery(document).ready( function () {
                jQuery("#eventdesc").addClass("mceEditor");
                if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
                    tinyMCE.execCommand("mceAddControl", false, "eventdesc");
                }
            });

            jQuery(document).ready(function($) {



	$('a.toggleVisual-eventdesc').click(
		function() {
			tinyMCE.execCommand('mceAddControl', false, 'eventdesc');
		}
	);

	$('a.toggleHTML-eventdesc').click(
		function() {
			tinyMCE.execCommand('mceRemoveControl', false, 'eventdesc');
		}
	);

});


</script>
	<?php
        $category = mysql_query("SELECT * FROM wp_resservation_cat");

		while($row = mysql_fetch_array($result))
		{
		echo '<form name="edit" action="admin.php?page=allbooking&appointment=save#appointment" method="post">';
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>ID</td>";
        echo "<td class='resint'><input name=\"id\" type=\"hidden\" value=\"".$row['id']."\" />".$row['id']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>Title</td>";
        echo "<td class='resint'><input name=\"title\" type=\"text\" value=\"".$row['title']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".CATEGORY."</td>";
        echo "<td class='resint'>
        <select name=\"groupid\">";
        while($row1 = mysql_fetch_array($category))
        {
            /*** create the options ***/
            echo '<option value="'.$row1['id'].'"'; if ($row1['id'] == $row['groupid']) { echo 'selected="selected"'; };
            echo '>'. $row1['name'] . '</option>'."\n";
        }
        echo "</select>
        </td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".DESCRIPTION."</td>";
        echo "<td class='resint'><textarea name=\"eventdesc\" id=\"eventdesc\" rows=\"5\" coll=\"10\">".$row['eventdesc']."</textarea></td>";

        echo "</tr>";
        echo "<tr><td></td>";
        echo '<td class="resint"><input type="submit" name="update" value="save" /></td></tr>
             </form>';
		}
	?>
</table>
<?php
break;
case 'save':
$update = $_REQUEST["update"] == "save";
    if ($update) {
$id = $_REQUEST["id"];
    if ($id) {
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    $title = $_REQUEST["title"];
    $groupid = $_REQUEST["groupid"];
    $eventdesc = $_REQUEST["eventdesc"];
	/* Get data. */
	$sql = "UPDATE  $tbl_name SET title = '".$title."', groupid = '".$groupid."', eventdesc = '".$eventdesc."' $where";
	$result = mysql_query($sql);
    //echo $sql;
}
}
?>
<?php echo RECORDSAVED ?>
<?php
break;
case 'savenew':

$title = $_REQUEST["title"];
$groupid = $_REQUEST["category"];
$eventdesc = $_REQUEST["descriptions"];

mysql_query("INSERT INTO wp_resservation (id, title, groupid, eventdesc) VALUES('', '".$title."', '".$groupid."','".$eventdesc."') ") or die(mysql_error());

?>
<?php echo RECORDSAVED ?>
<?php
break;
case 'delete':
$delete = $_REQUEST["del"] == "yes";
    if ($delete) {
$id = $_REQUEST["id"];
    if ($id) {
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation";		//your table name

	/* Get data. */
	$sql = "DELETE FROM $tbl_name $where";
	$result = mysql_query($sql);
}
}
?>
<?php echo RECORDDELETE ?>
<?php
break;
case 'deleteall':
$deleteall = $_REQUEST["delete"] == "delete";
    if ($deleteall) {
$id = $_REQUEST["check_list"];
    if ($id) {

	$tbl_name="wp_resservation";		//your table name

    foreach ($id as $ids) {
	$sql = "DELETE FROM $tbl_name WHERE id = ".$ids."";
	$result = mysql_query($sql);
    }
?>
Records delete
<input type=button onClick="location.href='admin.php?page=allbooking&appointment=list#appointment'" value='Back to List'>
<?php
}
}
break;
}
?>
</div>
<div id="dates">
  <style type="text/css">
  /*<![CDATA[*/
  div.pagination {
	padding: 3px;
	margin: 3px;
}

div.pagination a {
	padding: 2px 5px 2px 5px;
	margin: 2px;
	border: 1px solid #AAAADD;

	text-decoration: none; /* no underline */
	color: #000099;
}
div.pagination a:hover, div.pagination a:active {
	border: 1px solid #000099;

	color: #000;
}
div.pagination span.current {
	padding: 2px 5px 2px 5px;
	margin: 2px;
		border: 1px solid #000099;

		font-weight: bold;
		background-color: #000099;
		color: #FFF;
	}
	div.pagination span.disabled {
		padding: 2px 5px 2px 5px;
		margin: 2px;
		border: 1px solid #EEE;

		color: #DDD;
	}

  /*]]>*/
  </style>
<script type="text/javascript">
            $(document).ready(function() {
                $('#time').timepicker({
                    showPeriod: false,
                    showLeadingZero: false
                });
            });
        </script>
<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
function CheckAll(chk)
{
for (i = 0; i < chk.length; i++)
chk[i].checked = true ;
}

function UnCheckAll(chk)
{
for (i = 0; i < chk.length; i++)
chk[i].checked = false ;
}
/*]]>*/
</script>

<form name="rangetime" action="admin.php?page=allbooking#dates" method="POST">
<table>
  <tr>
    <td width='100'><? echo DATES ?></td>
    <td><input name="date" type="text" id="date" /></td>
  </tr>
  <tr>
    <td width='100'>Times:</td>
    <td><input type="text" id="time" value="" name="time" /></td>
  </tr>
</table>
<input type="submit" name="Search" value="search" />
</form>
<?php
$action = $_GET['action'];
switch ($action) {
case 'list':
default:
    $date = $_REQUEST["date"];
    $time = $_REQUEST["time"];

    if ($date && $time) {
      $where = "WHERE date = '".$date."' AND time_start = '".$time."'";
    } else
    if ($date) {
    $where = "WHERE date = '".$date."'";
    }
    else if ($time) {
    $where = "WHERE time_start = '".$time."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$pagelist = $_GET['pagelist'];
	if($pagelist)
		$start = ($pagelist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status,description FROM $tbl_name $where LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($pagelist == 0) $pagelist = 1;					//if no page var is given, default to 1.
	$prev = $pagelist - 1;							//previous page is page - 1
	$next = $pagelist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($pagelist > 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$prev#dates\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $pagelist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($pagelist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $pagelist && $pagelist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $pagelist - $adjacents; $counter <= $pagelist + $adjacents; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
			}
		}

		//next button
		if ($pagelist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$next#dates\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
<form name="listcategory" action="admin.php?page=allbooking&action=deleteall#dates" method="post">
<div style='float:right; margin-right:250px; margin-top:-39px;'>
<input type="button" name="Check_All" value="Check All"
onClick="CheckAll(document.listcategory['check_list[]'])">
<input type="button" name="Un_CheckAll" value="Uncheck All"
onClick="UnCheckAll(document.listcategory['check_list[]'])">
<input type="submit" name="delete" value="delete" />
</div>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo MAXBOOKING ?></td>
 <td class='resint'><?php echo PRICE ?></td>
 <td class='resint'><?php echo STATUS ?></td>
 <td class='resint'><?php echo DESCRIPTION ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 <td class='resint'></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not available";
		    break;
		    case 1:
            $status = "available";
		    break;
		    case 2:
            $status = "Resserved";
		    break;
		    default:
            $status = "available";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['date']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'>".$row['max']."</td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'>".$status."</td>";
        echo "<td class='resint'>".$row['description']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=edit&id=".$row['id']."#dates\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=delete&del=yes&id=".$row['id']."#dates\">".DELETE."</a></td>";
        echo "<td class='resint'><input type=\"checkbox\" name=\"check_list[]\" value='".$row['id']."'></td>";

        echo "</tr>";
		}
	?>
</table>
<div style='float:right; margin-right:250px;'>
<input type="button" name="Check_All" value="Check All"
onClick="CheckAll(document.listcategory['check_list[]'])">
<input type="button" name="Un_CheckAll" value="Uncheck All"
onClick="UnCheckAll(document.listcategory['check_list[]'])">
<input type="submit" name="delete" value="delete" />
</div>
</form>
<?=$pagination?>
<?php
break;
case 'edit':
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$id = $_REQUEST["id"];
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status,description FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);
?>
 <table>
	<?php
		while($row = mysql_fetch_array($result))
		{
		echo '<form name="edit" action="admin.php?page=allbooking&action=save#dates" method="post">';
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>ID</td>";
        echo "<td class='resint'><input name=\"id\" type=\"hidden\" value=\"".$row['id']."\" />".$row['id']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".DATES."</td>";
        echo "<td class='resint'>".$row['date']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMESTART."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMEEND."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".MAXBOOKING."</td>";
        echo "<td class='resint'><input name=\"max\" type=\"text\" value=\"".$row['max']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".PRICE."</td>";
        echo "<td class='resint'><input name=\"price\" type=\"text\" value=\"".$row['price']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".STATUS."</td>";
        echo "<td class='resint'><input name=\"status\" type=\"text\" value=\"".$row['status']."\" /></td>";

        echo "</tr>";
        echo "<td class='resint'>".DESCRIPTION."</td>";
        echo "<td class='resint'><input name=\"description\" type=\"text\" value=\"".$row['description']."\" /></td>";

        echo "</tr>";
        echo "<tr><td></td>";
        echo '<td class="resint"><input type="submit" name="update" value="save" /></td></tr>
             </form>';
		}
	?>
</table>
<?php
break;
case 'save':
$update = $_REQUEST["update"] == "save";
    if ($update) {
$id = $_REQUEST["id"];
    if ($id) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    $max = $_REQUEST["max"];
    $price = $_REQUEST["price"];
    $status = $_REQUEST["status"];
    $description = $_REQUEST["description"];
	/* Get data. */
	$sql = "UPDATE  $tbl_name SET max = '".$max."', price = '".$price."', status = '".$status."', description = '".$description."' $where";
	$result = mysql_query($sql);

?>
<?php echo RECORDSAVED ?>
<?php
$date = $_REQUEST["date"];
    if ($date) {
    $where1 = "WHERE date = '".$date."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where1";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$pagelist = $_GET['pagelist'];
	if($pagelist)
		$start = ($pagelist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status,description FROM $tbl_name $where1 LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($pagelist == 0) $pagelist = 1;					//if no page var is given, default to 1.
	$prev = $pagelist - 1;							//previous page is page - 1
	$next = $pagelist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($pagelist > 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$prev#dates\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $pagelist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($pagelist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $pagelist && $pagelist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $pagelist - $adjacents; $counter <= $pagelist + $adjacents; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
			}
		}

		//next button
		if ($pagelist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$next#dates\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo MAXBOOKING ?></td>
 <td class='resint'><?php echo PRICE ?></td>
 <td class='resint'><?php echo STATUS ?></td>
 <td class='resint'><?php echo DESCRIPTION ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not available";
		    break;
		    case 1:
            $status = "available";
		    break;
		    case 2:
            $status = "Resserved";
		    break;
		    default:
            $status = "available";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['date']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'>".$row['max']."</td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'>".$status."</td>";
        echo "<td class='resint'>".$row['description']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=edit&id=".$row['id']."#dates\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=delete&del=yes&id=".$row['id']."#dates\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>

<?php } ?>
<?php }

break;
case 'delete':
$delete = $_REQUEST["del"] == "yes";
    if ($delete) {
$id = $_REQUEST["id"];
    if ($id) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    $max = $_REQUEST["max"];
    $price = $_REQUEST["price"];
    $status = $_REQUEST["status"];
    $description = $_REQUEST["description"];
	/* Get data. */
	$sql = "DELETE FROM $tbl_name $where";
	$result = mysql_query($sql);

?>
<?php echo RECORDDELETE ?>
<?php
$date = $_REQUEST["date"];
    if ($date) {
    $where1 = "WHERE date = '".$date."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where1";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$pagelist = $_GET['pagelist'];
	if($pagelist)
		$start = ($pagelist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status,description FROM $tbl_name $where1 LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($pagelist == 0) $pagelist = 1;					//if no page var is given, default to 1.
	$prev = $pagelist - 1;							//previous page is page - 1
	$next = $pagelist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($pagelist > 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$prev#dates\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $pagelist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($pagelist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $pagelist && $pagelist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $pagelist - $adjacents; $counter <= $pagelist + $adjacents; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&pagelist=$lpm1#dates\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=$lastpage#dates\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&pagelist=1#dates\">1</a>";
				$pagination.= "<a href=\"$targetpage&pagelist=2#dates\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $pagelist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&pagelist=$counter#dates\">$counter</a>";
				}
			}
		}

		//next button
		if ($pagelist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&pagelist=$next#dates\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo MAXBOOKING ?></td>
 <td class='resint'><?php echo PRICE ?></td>
 <td class='resint'><?php echo STATUS ?></td>
 <td class='resint'><?php echo DESCRIPTION ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not available";
		    break;
		    case 1:
            $status = "available";
		    break;
		    case 2:
            $status = "Resserved";
		    break;
		    default:
            $status = "available";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['date']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'>".$row['max']."</td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'>".$status."</td>";
        echo "<td class='resint'>".$row['description']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=edit&id=".$row['id']."#dates\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&action=delete&del=yes&id=".$row['id']."#dates\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>

<?php } ?>
<?php }

break;
case 'deleteall':
$deleteall = $_REQUEST["delete"] == "delete";
    if ($deleteall) {
$id = $_REQUEST["check_list"];
    if ($id) {

	$tbl_name="wp_resservation_disp";		//your table name

    foreach ($id as $ids) {
	$sql = "DELETE FROM $tbl_name WHERE id = ".$ids."";
	$result = mysql_query($sql);
    }
?>
Records delete
<input type=button onClick="location.href='admin.php?page=allbooking#dates'" value='Back to List'>
<?php
}
}
break;
}
?>
</div>
<div id="create">
<?php
$category = mysql_query("SELECT * FROM wp_resservation_cat");
//$rowcat = mysql_fetch_array($category)
?>
<form name="rangetime" id="createdate" action="admin.php?page=allbooking#create" method="POST">
<table>
  <tr>
    <td width='100'><?php echo DATES ?> start</td>
    <td><input name="date1" type="text" id="date1" class="required" /></td>
  </tr>
  <tr>
    <td width='100'><?php echo DATES ?> end</td>
    <td><input name="date5" type="text" id="date5" class="required" /></td>
  </tr>
  <tr>
    <td width='100'><?php echo MAXPLACE ?></td>
    <td><input name="max" type="text" id="max" class="required" /></td>
  </tr>
  <tr>
    <td width='100'><?php echo DESCRIPTION ?></td>
    <td><input name="description" type="text" id="description" /></td>
  </tr>
  <tr>
    <td width='100'><?php echo PRICE ?></td>
    <td><input name="price" type="text" id="price" class="required" /></td>
  </tr>
  <tr>
    <td width='100'><?php echo CATEGORY ?></td>
    <td><select name="category">
<?php
while($row1 = mysql_fetch_array($category))
        {
            /*** create the options ***/
            echo '<option value="'.$row1['id'].'"';
            echo '>'. $row1['name'] . '</option>'."\n";
        }

?>
         </select></td>
  </tr>
</table><br /><br />
<input type="hidden" name="createdate" value="createdate" />
<input type="submit" name="Crea" value="create" />
</form>
<?php
if ($_POST["createdate"] == "createdate") {
  if ($_POST["date1"]) {
  require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$date1 = $_REQUEST["date1"];
$date5 = $_REQUEST["date5"];
$max = $_REQUEST["max"];
$description = $_REQUEST["description"];
$category = $_REQUEST["category"];

$rangedate = dateRange( $date1, $date5);

$settings = allbook_get_settings();
foreach( $rangedate as $key => $value){
$categoryquery = mysql_query("SELECT * FROM wp_resservation_cat WHERE id= '".$category."'");
$rowcategory = mysql_fetch_array( $categoryquery );

$rgtime = $rowcategory['rangetime']." mins";
$rgtimedif = "+".$rowcategory['rangetime']." minutes";

$times = create_time_range($rowcategory['time_start_cat'], $rowcategory['time_end_cat'], $rgtime);
// esegue la query settando un errore personale in caso di fallimento

$ceck = mysql_query("SELECT * FROM wp_resservation_disp WHERE date= '".$value."' AND category= ".$category."");
if(@mysql_num_rows($ceck) != 0)
	{
echo $value. ' '. DATEEXIST.'<br />';
} else {
print "<table>";
foreach ($times as $key => $time) {

$time_start = $times[$key] = date('H:i:s', $time);
$time_hours = $times[$key] = date("H:i",strtotime($rgtimedif,$time));
$time_min = $times[$key] = date("s",$time);
$time_end = $time_hours.":".$time_min;
$price = $_REQUEST["price"];
$status = "1";

    print "<tr>";
    echo "<td class='resint'>".$value." - ".$times[$key] = date('H:i:s', $time)." - ".$time_end."<td>";
    echo "<td class='resint'>".$max." ".$avv." ".INSERT."</td>";
    print "</tr>";

    mysql_query("INSERT INTO wp_resservation_disp (id, date,time_start,time_end,max,price,status,description,category) VALUES('', '".$value."', '".$time_start."','".$time_end."','".$max."','".$price."','".$status."','".$description."','".$category."') ") or die(mysql_error());
}
print "</table>";
}
}
}
}
?>
</div>
<div id="lisbook">
<script type="text/javascript">
            $(document).ready(function() {
                $('#time1').timepicker({
                    showPeriod: false,
                    showLeadingZero: false
                });
            });
        </script>
<form name="rangetime" action="admin.php?page=allbooking&pagebook=listbook#lisbook" method="POST">
<table>
<tr>
    <td width='100'><?php echo DATES ?></td>
    <td><input name="date2" type="text" id="date2" /></td>
  </tr>
<tr>
    <td width='100'>Times:</td>
    <td><input type="text" id="time1" value="" name="time" /></td>
  </tr>
</table>
<input type="submit" name="Search" value="search" />
</form>
<?php
$pagebook = $_GET['pagebook'];
switch ($pagebook) {
case 'listbook':
default:
    $date2 = $_REQUEST["date2"];
    $time = $_REQUEST["time"];
    if ($date1 && $time) {
      $where = "WHERE data = '".$date1."' AND time_start = '".$time."'";
    } else
    if ($date2) {
    $where = "WHERE data = '".$date2."'";
    } else if ($time) {
    $where = "WHERE time_start = '".$time."'";
    }

	$tbl_name="wp_resservation_book";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagebook=listbook"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$booklist = $_GET['booklist'];
	if($booklist)
		$start = ($booklist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($booklist == 0) $booklist = 1;					//if no page var is given, default to 1.
	$prev = $booklist - 1;							//previous page is page - 1
	$next = $booklist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($booklist > 1)
			$pagination.= "<a href=\"$targetpage&booklist=$prev#lisbook\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $booklist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($booklist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&booklist=$lpm1#lisbook\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=$lastpage#lisbook\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $booklist && $booklist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&booklist=1#lisbook\">1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=2#lisbook\">2</a>";
				$pagination.= "...";
				for ($counter = $booklist - $adjacents; $counter <= $booklist + $adjacents; $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&booklist=$lpm1#lisbook\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=$lastpage#lisbook\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&booklist=1#lisbook\">1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=2#lisbook\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
			}
		}

		//next button
		if ($booklist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&booklist=$next#lisbook\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo SURNAME ?></td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo NUMBER ?></td>
 <td class='resint'><?php echo PRICE ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not paid";
		    break;
		    case 1:
            $status = "Cash";
		    break;
		    case 2:
            $status = "Paid paypal";
		    break;
		    default:
            $status = "not paid";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['nome']."</td>";
        echo "<td class='resint'>".$row['cognome']."</td>";
        echo "<td class='resint'>".$row['data']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'><a href='".PLUGIN_URL_ALLBOOK."/tiket.php?tiket=".$row['code']."' target='_blank'>".$row['code']."</a></td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagebook=edit&id=".$row['id']."#lisbook\">".EDIT."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
break;
case 'edit':
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$id = $_REQUEST["id"];
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_book";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);
?>
 <table>
	<?php
		while($row = mysql_fetch_array($result))
		{
		echo '<form name="edit" action="admin.php?page=allbooking&pagebook=save#lisbook" method="post">';
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>ID</td>";
        echo "<td class='resint'><input name=\"id\" type=\"hidden\" value=\"".$row['id']."\" />".$row['id']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".DATES."</td>";
        echo "<td class='resint'>".$row['data']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMESTART."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMEEND."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".NUMBER."</td>";
        echo "<td class='resint'>".$row['code']."</td>";
        echo "</tr>";
        echo "<tr><td></td>";
        echo '<td class="resint"><input type="hidden" name="code" value="'.$row['code'].'" /><input type="submit" name="updatebook" value="save" /></td></tr>
             </form>';
		}
	?>
</table>
<?php
break;
case 'save':
$updatebook = $_REQUEST["updatebook"] == "save";
    if ($updatebook) {
$idupdatebook = $_REQUEST["id"];
    if ($idupdatebook) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($idupdatebook) {
    $wherebook = "WHERE id = '".$idupdatebook."'";
    }

	$tbl_name="wp_resservation_book";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    $status = $_REQUEST["status"];
    //$price = $_REQUEST["price"];
    //$status = $_REQUEST["status"];
	/* Get data. */
	$sql = "UPDATE  $tbl_name SET status = '".$status."' $wherebook";
	$result = mysql_query($sql);

?>
<?php echo RECORDSAVED ?>
<?php
$date2 = $_REQUEST["date2"];
    if ($date2) {
    $where = "WHERE data = '".$date2."'";
    }

	$tbl_name="wp_resservation_book";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name $where";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagebook=listbook"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$booklist = $_GET['booklist'];
	if($booklist)
		$start = ($booklist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($booklist == 0) $booklist = 1;					//if no page var is given, default to 1.
	$prev = $booklist - 1;							//previous page is page - 1
	$next = $booklist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($booklist > 1)
			$pagination.= "<a href=\"$targetpage&booklist=$prev#lisbook\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $booklist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($booklist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&booklist=$lpm1#lisbook\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=$lastpage#lisbook\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $booklist && $booklist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&booklist=1#lisbook\">1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=2#lisbook\">2</a>";
				$pagination.= "...";
				for ($counter = $booklist - $adjacents; $counter <= $booklist + $adjacents; $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&booklist=$lpm1#lisbook\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=$lastpage#lisbook\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&booklist=1#lisbook\">1</a>";
				$pagination.= "<a href=\"$targetpage&booklist=2#lisbook\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $booklist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&booklist=$counter#lisbook\">$counter</a>";
				}
			}
		}

		//next button
		if ($booklist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&booklist=$next#lisbook\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}
    //echo $sql;
?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo SURNAME ?></td>
 <td class='resint'><?php echo DATES ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND ?></td>
 <td class='resint'><?php echo NUMBER ?></td>
 <td class='resint'><?php echo PRICE ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
		  switch ($row['status']) {
		    case 0:
            $status = "not paid";
		    break;
		    case 1:
            $status = "Cash";
		    break;
		    case 2:
            $status = "Paid paypal";
		    break;
		    default:
            $status = "not paid";
            break;
		  }
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['nome']."</td>";
        echo "<td class='resint'>".$row['cognome']."</td>";
        echo "<td class='resint'>".$row['data']."</td>";
        echo "<td class='resint'>".$row['time_start']."</td>";
        echo "<td class='resint'>".$row['time_end']."</td>";
        echo "<td class='resint'><a href='".PLUGIN_URL_ALLBOOK."/tiket.php?tiket=".$row['code']."' target='_blank'>".$row['code']."</a></td>";
        echo "<td class='resint'>".$row['price']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagebook=edit&id=".$row['id']."#lisbook\">".EDIT."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
}
}
break;
}
?>
</div>
<div id="category">
<?php echo NEWCATEGORY ?>
<form name="rangetime" id="rangetime" action="admin.php?page=allbooking&pagecat=create#category" method="POST">
<table>
  <tr>
    <td width='100'><?php echo NAME ?> </td>
    <td><input name="name" type="text" id="name" class="required" /></td>
  </tr>
  <tr>
    <td width='100'><?php echo TIMESTART ?></td>
    <td><input name="time_start_cat" type="text" id="time_start_cat" class="required" /></td>
  </tr>
  <tr>
    <td width='100'><?php echo TIMEEND ?></td>
    <td><input name="time_end_cat" type="text" id="time_end_cat" class="required" /></td>
  </tr>
  <tr>
    <td width='100'><?php echo RANGECAT ?></td>
    <td><input name="range" type="text" id="range" class="required" /></td>
  </tr>
</table>
<input type="submit" name="category" value="create" />
</form>
<?php
$pagecat = $_GET['pagecat'];
switch ($pagecat) {
case 'listcat':
default:

	$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagecat=listcat"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$categorylist = $_GET['categorylist'];
	if($categorylist)
		$start = ($categorylist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($categorylist == 0) $categorylist = 1;					//if no page var is given, default to 1.
	$prev = $categorylist - 1;							//previous page is page - 1
	$next = $categorylist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($categorylist > 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$prev#category\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $categorylist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($categorylist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $categorylist && $categorylist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $categorylist - $adjacents; $counter <= $categorylist + $adjacents; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
			}
		}

		//next button
		if ($categorylist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$next#category\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}

?>
<?php echo LISTCAT ?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND  ?></td>
 <td class='resint'><?php echo RANGECAT ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['name']."</td>";
        echo "<td class='resint'>".$row['time_start_cat']."</td>";
        echo "<td class='resint'>".$row['time_end_cat']."</td>";
        echo "<td class='resint'>".$row['rangetime']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=edit&id=".$row['id']."#category\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=delete&del=yes&id=".$row['id']."#category\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php

break;

case 'create':
if ($_POST["category"] == "create") {
  if ($_POST["name"]) {
  require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$name = $_REQUEST["name"];
$time_start_cat = $_REQUEST["time_start_cat"];
$time_end_cat = $_REQUEST["time_end_cat"];
$range = $_REQUEST["range"];

$settings = allbook_get_settings();

// esegue la query settando un errore personale in caso di fallimento
$ceck = mysql_query("SELECT * FROM wp_resservation_cat WHERE name = '".$name."'");
if(@mysql_num_rows($ceck) != 0)
	{
echo "Data esistente";
} else {
print "<table>";

    print "<tr>";
    echo "<td class='resint'>".$name." - ".$time_start_cat." - ".$time_end_cat."<td>";
    echo "<td class='resint'>".$range." Inserito</td>";
    print "</tr>";

    //echo "INSERT INTO wp_resservation_cat (id, name,time_start_cat,time_end_cat,range) VALUES('', '".$name."', '".$time_start_cat."','".$time_end_cat."','".$range."'";

    mysql_query("INSERT INTO wp_resservation_cat (id, name,time_start_cat,time_end_cat,rangetime) VALUES('', '".$name."', '".$time_start_cat."','".$time_end_cat."','".$range."') ") or die(mysql_error());

print "</table>";
}
}
$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagecat=listcat"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$categorylist = $_GET['categorylist'];
	if($categorylist)
		$start = ($categorylist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($categorylist == 0) $categorylist = 1;					//if no page var is given, default to 1.
	$prev = $categorylist - 1;							//previous page is page - 1
	$next = $categorylist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($categorylist > 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$prev#category\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $categorylist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($categorylist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $categorylist && $categorylist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $categorylist - $adjacents; $counter <= $categorylist + $adjacents; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
			}
		}

		//next button
		if ($categorylist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$next#category\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}

?>
<?php echo LISTCAT ?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND  ?></td>
 <td class='resint'><?php echo RANGECAT ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['name']."</td>";
        echo "<td class='resint'>".$row['time_start_cat']."</td>";
        echo "<td class='resint'>".$row['time_end_cat']."</td>";
        echo "<td class='resint'>".$row['rangetime']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=edit&id=".$row['id']."#category\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=delete&del=yes&id=".$row['id']."#category\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
}
break;
case 'edit':
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
$id = $_REQUEST["id"];
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);
?>
 <table>
	<?php
		while($row = mysql_fetch_array($result))
		{
		echo '<form name="edit" action="admin.php?page=allbooking&pagecat=save#category" method="post">';
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>ID</td>";
        echo "<td class='resint'><input name=\"id\" type=\"hidden\" value=\"".$row['id']."\" />".$row['id']."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".NAME."</td>";
        echo "<td class='resint'><input name=\"name\" type=\"text\" value=\"".$row['name']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMESTART."</td>";
        echo "<td class='resint'><input name=\"time_start_cat\" type=\"text\" value=\"".$row['time_start_cat']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".TIMEEND."</td>";
        echo "<td class='resint'><input name=\"time_end_cat\" type=\"text\" value=\"".$row['time_end_cat']."\" /></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td class='resint'>".RANGECAT."</td>";
        echo "<td class='resint'><input name=\"rangetime\" type=\"text\" value=\"".$row['rangetime']."\" /></td>";

        echo "</tr>";
        echo "<tr><td></td>";
        echo '<td class="resint"><input type="submit" name="updatecategory" value="save" /></td></tr>
             </form>';
		}
	?>
</table>
<?php
break;
case 'save':
$updatecat = $_REQUEST["updatecategory"] == "save";
    if ($updatecat) {
$idupdatecat = $_REQUEST["id"];
    if ($idupdatecat) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($idupdatecat) {
    $wherecat = "WHERE id = '".$idupdatecat."'";
    }

	$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    $name = $_REQUEST["name"];
    $time_start_cat = $_REQUEST["time_start_cat"];
    $time_end_cat = $_REQUEST["time_end_cat"];
    $range = $_REQUEST["rangetime"];
	/* Get data. */
	$sql = "UPDATE  $tbl_name SET name = '".$name."', time_start_cat = '".$time_start_cat."', time_end_cat = '".$time_end_cat."', rangetime = '".$range."' $wherecat";
	$result = mysql_query($sql);

?>
<?php echo RECORDSAVED ?>
<?php
$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagecat=listcat"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$categorylist = $_GET['categorylist'];
	if($categorylist)
		$start = ($categorylist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($categorylist == 0) $categorylist = 1;					//if no page var is given, default to 1.
	$prev = $categorylist - 1;							//previous page is page - 1
	$next = $categorylist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($categorylist > 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$prev#category\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $categorylist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($categorylist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $categorylist && $categorylist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $categorylist - $adjacents; $counter <= $categorylist + $adjacents; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
			}
		}

		//next button
		if ($categorylist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$next#category\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}

?>
<br /><?php echo LISTCAT ?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND  ?></td>
 <td class='resint'><?php echo RANGECAT ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['name']."</td>";
        echo "<td class='resint'>".$row['time_start_cat']."</td>";
        echo "<td class='resint'>".$row['time_end_cat']."</td>";
        echo "<td class='resint'>".$row['rangetime']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=edit&id=".$row['id']."#category\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=delete&del=yes&id=".$row['id']."#category\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
}
}
case 'delete':
$delete = $_REQUEST["del"] == "yes";
    if ($delete) {
$id = $_REQUEST["id"];
    if ($id) {
require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0
    //$max = $_REQUEST["max"];
    //$price = $_REQUEST["price"];
    //$status = $_REQUEST["status"];
    //$description = $_REQUEST["description"];
	/* Get data. */
	$sql = "DELETE FROM $tbl_name $where";
	$result = mysql_query($sql);

?>
<?php echo RECORDDELETE ?>
<?php
$tbl_name="wp_resservation_cat";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name";
    //echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	/* Setup vars for query. */
	$targetpage = "admin.php?page=allbooking&pagecat=listcat"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$categorylist = $_GET['categorylist'];
	if($categorylist)
		$start = ($categorylist - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($categorylist == 0) $categorylist = 1;					//if no page var is given, default to 1.
	$prev = $categorylist - 1;							//previous page is page - 1
	$next = $categorylist + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($categorylist > 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$prev#category\">� previous</a>";
		else
			$pagination.= "<span class=\"disabled\">previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $categorylist)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($categorylist < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $categorylist && $categorylist > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $categorylist - $adjacents; $counter <= $categorylist + $adjacents; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&categorylist=$lpm1#category\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=$lastpage#category\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&categorylist=1#category\">1</a>";
				$pagination.= "<a href=\"$targetpage&categorylist=2#category\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $categorylist)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&categorylist=$counter#category\">$counter</a>";
				}
			}
		}

		//next button
		if ($categorylist < $counter - 1)
			$pagination.= "<a href=\"$targetpage&categorylist=$next#category\">next �</a>";
		else
			$pagination.= "<span class=\"disabled\">next</span>";
		$pagination.= "</div>\n";
	}

?>
<br /><?php echo LISTCAT ?>
 <table>
 <tr>
 <td class='resint'>ID</td>
 <td class='resint'><?php echo NAME ?></td>
 <td class='resint'><?php echo TIMESTART ?></td>
 <td class='resint'><?php echo TIMEEND  ?></td>
 <td class='resint'><?php echo RANGECAT ?></td>
 <td class='resint'><?php echo EDIT ?></td>
 <td class='resint'><?php echo DELETE ?></td>
 </tr>
	<?php
		while($row = mysql_fetch_array($result))
		{
        echo "<tr>";
		// Your while loop here
        echo "<td class='resint'>".$row['id']."</td>";
        echo "<td class='resint'>".$row['name']."</td>";
        echo "<td class='resint'>".$row['time_start_cat']."</td>";
        echo "<td class='resint'>".$row['time_end_cat']."</td>";
        echo "<td class='resint'>".$row['rangetime']."</td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=edit&id=".$row['id']."#category\">".EDIT."</a></td>";
        echo "<td class='resint'><a href=\"admin.php?page=allbooking&pagecat=delete&del=yes&id=".$row['id']."#category\">".DELETE."</a></td>";

        echo "</tr>";
		}
	?>
</table>
<?=$pagination?>
<?php
}
}
break;
}
?>
</div>
</div>
<?php


	return 	$content;
}


function allbook_adm_settings()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_configuration()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_siteconf()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_menumanager()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_hotel()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_roomtype()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_seasons()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_booking()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_avv()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_history()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_stat()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_lang()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_tempemail()
{
	global $wpdb;

	$content="";

	echo $content;
}

function allbook_adm_constants()
{
	global $wpdb;

	$content="";

	echo $content;
}


function allbook_treesort ($arr)
{

	for ($i=0;$i<count($arr)-1;$i++) {
		$flag=$i;
		for ($j=$i+1;$j<count($arr);$j++) {
			if ($arr[$j][1]==$arr[$i][0])
			{
				array_splice($arr,$flag+1,0,array(($arr[$j])));
				unset($arr[$j+1]);
				$arr = array_values($arr);
				$flag=$flag+1;
			}
			//echo $j."-".$arr[$j][0]."<br>";
		}
		//echo "<br>";

	}


$map=array();
	for ($k=0;$k<count($arr);$k++)
	{
		if ($arr[$k][1]==0) array_push($map,0);
		$z=1;
		foreach($arr as $key) {
			if($arr[$k][1]==$key[0]) {
				array_push($map,$z);
			}
			$z++;
		}

	}


	return array($arr, $map);
}

function allbook_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;

   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_allbook_tinymce_plugin");
     add_filter('mce_buttons', 'register_allbook_button');
   }
}

function register_allbook_button($buttons) {
   array_push($buttons, "separator", "allbook");
   return $buttons;
}

// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_allbook_tinymce_plugin($plugin_array) {
   $plugin_array['allbook'] = get_option('siteurl').'/wp-content/plugins/js-appointment/editor_plugin.js';
   return $plugin_array;
}

function allbook_change_tinymce_version($version) {
	return ++$version;
}
// Modify the version when tinyMCE plugins are changed.
add_filter('tiny_mce_version', 'allbook_change_tinymce_version');
// init process for button control
add_action('init', 'allbook_addbuttons');

function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {

	$dates = array();
	$current = strtotime( $first );
	$last = strtotime( $last );

	while( $current <= $last ) {

		$dates[] = date( $format, $current );
		$current = strtotime( $step, $current );
	}

	return $dates;
}

	function QueryDay($categorys,$days,$month,$year)
	{
	    require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
	    $settings = allbook_get_settings();

        if ($categorys) {
        $category = $categorys;
        } else {
        $settings = allbook_get_settings();
        $category = $settings['allbook_catinit'];
          }
		//$times = create_time_range($settings['allbook_datastart'], $settings['allbook_dataend'], $settings['allbook_datarange']);
       $categoryquery = mysql_query("SELECT * FROM wp_resservation_cat WHERE id= '".$category."'");
       $rowcategory = mysql_fetch_array( $categoryquery );

       $rgtime = $rowcategory['rangetime']." mins";
       $rgtimedif = "+".$rowcategory['rangetime']." minutes";

       $times = create_time_range($rowcategory['time_start_cat'], $rowcategory['time_end_cat'], $rgtime);
// more examples
// $times = create_time_range('9:30am', '5:30pm', '30 mins');
// $times = create_time_range('9:30am', '5:30pm', '1 mins');
// $times = create_time_range('9:30am', '5:30pm', '30 secs');
// and so on
print "<table class=\"month\">";

//request date
$day = $days;
$months = $month;
$years = $year;
$dates = $years."-".$months."-".$day;
//require_once("libs/SQLManager.class.php");
    //$db = new SQLManager(true, "", "log.txt", true);
    //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");
echo "<tr><td>".TIMEINTERVALS."</td><td>".AVAIABILE."</td></tr>";
// format the unix timestamps
foreach ($times as $key => $time) {

$qrytime = $times[$key] = date('H:i:s', $time);
$querydate = mysql_query("SELECT * FROM wp_resservation_disp WHERE date = '".$dates."' AND time_start = '".$qrytime."' AND category= ".$category." LIMIT 1");
//$array_val = $db->getArray($querydate, true);
//echo $querydate;
$row = mysql_fetch_array( $querydate );
if ($row['max'] == 1) {
$avv = "<a href=\"".PLUGIN_URL_ALLBOOK."/addajax.php?action=add&id=".$row['id']."\" class=\"cart\">".PLACEAVV."</a>";
} else if($row['max'] >= 2) {
$avv = "<a href=\"".PLUGIN_URL_ALLBOOK."/addajax.php?action=add&id=".$row['id']."\" class=\"cart\">".PLACEAVVS."</a>";
} else {
$avv = NOTVVS;
}
    print "<tr>";
    echo "<td>".$years."/".$months."/".$day." - ".$times[$key] = date('H:i:s', $time)."</td>";
    echo "<td>".$row['max']." ".$avv."</td>";
    print "</tr>";
}
print "</table>";
//$db->Close();
	}

function OptionsBookId($key,$id)
{
$options = '<br />';
$options .= '<select name="options_book['.$key.']" size="1" class="options">';
$options .= '<option value="0">Select Options</option>';
$opt = mysql_query("SELECT * FROM wp_resservation_opt WHERE category = ".$id."");
if(mysql_num_rows($opt)!=0) {

while($row1 = mysql_fetch_array($opt))
        {
            /*** create the options ***/
            $options .= '<option value="'.$row1['id'].'"';
            $options .= '>'. $row1['name'] . ' - Price: '. $row1['price'] . '</option>'."\n";
            //$options .= "";
        }
        $options .= '</select><br />';
        } else {
        $options = '<br />No Additional services';
        }
return $options;
}

function OptionsBook($key,$id)
{
$options = '<br />';
$options .= '<select name="options_book['.$key.']" size="1" class="options">';
$options .= '<option value="0">Select Options</option>';
$opt = mysql_query("SELECT * FROM wp_resservation_opt WHERE category = ".$id."");
if(mysql_num_rows($opt)!=0) {

while($row1 = mysql_fetch_array($opt))
        {
            /*** create the options ***/
            $options .= '<option value="'.$row1['id'].'"';
            $options .= '>'. $row1['name'] . ' - Price: '. $row1['price'] . '</option>'."\n";
            //$options .= "";
        }
        $options .= '</select><br />';
        } else {
        $options = '<br />No Additional services';
        }
return $options;
}

function OptionsName($key)
{
$options = '';
$opt = mysql_query("SELECT * FROM wp_resservation_opt WHERE id = ".$key."");
while($row1 = mysql_fetch_array($opt))
        {
            /*** create the options ***/
            $options .= $row1['name'];
            //$options .= "";
        }
return $options;
}
function OptionsPrice($key)
{
$options = '';
$opt = mysql_query("SELECT * FROM wp_resservation_opt WHERE id = ".$key."");
while($row1 = mysql_fetch_array($opt))
        {
            /*** create the options ***/
            $options .= $row1['price'];
            //$options .= "";
        }
return $options;
}

function AppointmentType($id)
{

$event = mysql_query("SELECT * FROM wp_resservation WHERE groupid=".$id."");
$rowevent = mysql_fetch_array( $event );

$catid = $rowevent['groupid'];

$appointmenttype = '
<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
$(document).ready(function () {
$(\'#calTwo\').jCal({
				day:			new Date( (new Date()).setMonth( (new Date()).getMonth()) ),
				days:			1,
				showMonths:		1,
				drawBack:		function () {
						return false;
					},
				monthSelect:	true,
				sDate:			new Date(),
				dCheck:			function (day) {
						if (day.getDay() != 6)
							return \'day\';
						else
							return \'invday\';
					},
				callback:		function (day, days) {
						$(\'#calTwoDays\').val( days );
                        $.post("'.PLUGIN_URL_ALLBOOK.'/searchdata.php?search_action=searchadv", { cat: "'.$catid.'", day: day.getDate(), month: day.getMonth() + 1, year: day.getFullYear() },
                        function( data ) {
                        //alert("Data Loaded: " + data);
                        $( "#calTwoResult" ).html( data );
                        }
                        );
						return true;
					}
				});
		});
/*]]>*/
</script>
';

return $appointmenttype;
}

function AppointmentTypePost($id)
{

$event = mysql_query("SELECT * FROM wp_resservation WHERE id=".$id."");
$rowevent = mysql_fetch_array( $event );

$catid = $rowevent['groupid'];

$appointmenttype = '
<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
$(document).ready(function () {
$(\'#calTwo\').jCal({
				day:			new Date( (new Date()).setMonth( (new Date()).getMonth()) ),
				days:			1,
				showMonths:		1,
				drawBack:		function () {
						return false;
					},
				monthSelect:	true,
				sDate:			new Date(),
				dCheck:			function (day) {
						if (day.getDay() != 6)
							return \'day\';
						else
							return \'invday\';
					},
				callback:		function (day, days) {
						$(\'#calTwoDays\').val( days );
                        $.post("'.PLUGIN_URL_ALLBOOK.'/searchdata.php?search_action=searchadv", { cat: "'.$catid.'", day: day.getDate(), month: day.getMonth() + 1, year: day.getFullYear() },
                        function( data ) {
                        //alert("Data Loaded: " + data);
                        $( "#calTwoResult" ).html( data );
                        }
                        );
						return true;
					}
				});
		});
/*]]>*/
</script>
';

return $appointmenttype;
}

function AppointmentDesc($id)
{

$event = mysql_query("SELECT * FROM wp_resservation WHERE id=".$id."");
$rowevent = mysql_fetch_array( $event );

$appointmenttype = $rowevent['eventdesc'];

return $appointmenttype;
}

add_filter( 'pre_get_posts', 'my_get_posts' );

function my_get_posts( $query ) {
    if ( !is_admin() ) {
	if ( is_home() )  {


		$query->set( 'post_type', array( 'post', 'appointment', 'nav_menu_item') );

	return $query;
    }
    }
}

function add_pagination_to_author_page_query_string($query_string)
{

    if (isset($query_string['author_name'])) $query_string['post_type'] = array('post', 'appointment');
    return $query_string;

}
add_filter('request', 'add_pagination_to_author_page_query_string');

function myfeed_request($qv) {

	if (isset($qv['feed']) && !isset($qv['post_type']))
		$qv['post_type'] = array('post','appointment');
	return $qv;
}
add_filter('request', 'myfeed_request');

if(!function_exists('paginations')){
function paginations( $args = false )
{
	global $wp_query, $wp_rewrite;

	if( is_single() )
	{
		paginate_comments_links(array('type' => 'plain', 'end_size' => 3, 'mid_size' => 2));
	}
	else
	{
		$current = $wp_query->query_vars['paged'] > 1 ? $wp_query->query_vars['paged'] : 1;

		$pagination = array(
			'base'		=> $wp_rewrite->using_permalinks() ? user_trailingslashit(trailingslashit(remove_query_arg('s',get_pagenum_link(1))) . 'page/%#%/', 'paged') : @add_query_arg('paged','%#%'),
			'add_args'	=> !empty($wp_query->query_vars['s']) ? array('s'=>get_query_var('s')) : '',
			'total'		=> $wp_query->max_num_pages,
			'current'	=> $current,
			'end_size'	=> 2,
			'mid_size'	=> 2,
			'type'		=> 'plain',
			'format'	=> '',
		);

		echo paginate_links( $pagination );
	}
    	}
        }

if ( ! function_exists( 'the_excerpt_text' ) ) :
function the_excerpt_text($length) { // Outputs an excerpt of variable length (in characters)
global $post;
$text = $post->post_exerpt;
if ( '' == $text ) {
$text = get_the_content('');
$text = apply_filters('the_content', $text);
$text = str_replace(']]>', ']]>', $text);
}
$text = strip_shortcodes( $text ); // optional, recommended
$text = strip_tags($text); // use ' $text = strip_tags($text,'<p><a>'); ' to keep some formats; optional

$text = substr($text,0,$length).'';
echo apply_filters('the_excerpt',$text);
}
endif;

if(!function_exists('get_thumb')){

function get_thumb($postid=0, $size='full') {
	if ($postid<1)
	$postid = get_the_ID();
	$thumb = get_post_meta($postid, "thumb", TRUE); // Declare the custom field for the image
	if ($thumb != null or $thumb != '') {
		echo $thumb;
	}
	elseif ($images = get_children(array(
		'post_parent' => $postid,
		'post_type' => 'attachment',
		'numberposts' => '1',
		'post_mime_type' => 'image', )))
		foreach($images as $image) {
			$thumbnail=wp_get_attachment_image_src($image->ID, $size);
			?>
<?php echo $thumbnail[0]; ?>
<?php
		}
	else {
		echo get_bloginfo ('stylesheet_directory');
		echo '/images/image-pending.gif';
	}

}
}
?>