<?php
define('WP_USE_THEMES', true);
//require('../../wp-load.php');
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');

$action = $_REQUEST["action"];
    if ($action == "checkout") {
?>

<?php
	/*
		Place code to connect to your DB here.
	*/
	//require_once("libs/SQLManager.class.php");
    require_once("libs/class.book.php");
    //$db = new SQLManager(true, "", "log.txt", true);
    //$db->Open("localhost", "danese_wpbook", "gqdxz+E;!u*z", "danese_wpbook");
    $id = $_REQUEST["id"];
    if ($id) {
    $where = "WHERE id = '".$id."'";
    }

	$tbl_name="wp_resservation_disp";		//your table name
	// How many adjacent pages should be shown on each side?
						//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT id, date,time_start,time_end,max,price,status FROM $tbl_name $where LIMIT 1";
	$result = mysql_query($sql);


$settings = allbook_get_settings();
?> <form name="bookingsave" id="bookingsave" action="<?php echo ''.$url.'/index.php?page_id='.$post_id.'&pager=conf&action=book' ?>" method="POST">

 <div class="checkoutwrap">
   <div class="checkoutrow">
     <div class="leftcheck"><?php echo NAME ?></div>
     <div class="rightinput"><input name="nome" id="nome" type="text" class="required" /></div>
   </div>
  <div class="checkoutrow">
      <div class="leftcheck"><?php echo SURNAME ?></div>
     <div class="rightinput"><input name="cognome" id="cognome" type="text" class="required" /></div>
   </div>
    <div class="checkoutrow">
     <div class="leftcheck"><?php echo ADDRESS ?></div>
     <div class="rightinput"><input name="indirizzo" id="indirizzo" type="text" class="required" /></div>
   </div>
    <div class="checkoutrow">
    <div class="leftcheck"><?php echo ZIP ?></div>
    <div class="rightinput"><input name="cap" id="cap" type="text" class="required" /></div>
   </div>
 <div class="checkoutrow">
     <div class="leftcheck"><?php echo CITY ?></div>
     <div class="rightinput"><input name="citta" id="citta" type="text" class="required" /></div>
   </div>
<div class="checkoutrow">
   <div class="leftcheck"><?php echo PROVINCE ?></div>
     <div class="rightinput"><input name="provincia" id="provincia" type="text" class="required" /></div>
   </div>
  <div class="checkoutrow">
     <div class="leftcheck"><?php echo COUNTRY ?></div>
    <div class="rightinput"><input name="nazione" id="nazione" type="text" class="required" /></div>
   </div>
 <div class="checkoutrow">
   <div class="leftcheck"><?php echo PHONE ?></div>
     <div class="rightinput"><input name="telefono" id="telefono" type="text" class="required" /></div>
   </div>
 <div class="checkoutrow">
     <div class="leftcheck"><?php echo EMAIL ?></div>
      <div class="rightinput"><input name="email" id="email" type="text" class="required email" /></div>
   </div>
 <div class="checkoutrow">
    <div class="leftcheck"><?php echo NOTE ?></div>
    <div class="rightinput"><textarea name="note"></textarea></div>
   </div>
   <div class="checkoutrow">
    <div class="leftcheck"></div>
     <input type="hidden" name="typepag" value="1" />
     <div class="paypalbutton"></div>
   </div>
	<?php
		while($row = mysql_fetch_array($result))
		{
		}
$data_array = $_REQUEST["id"];
$qty = $_REQUEST["qty"];
$date = $_REQUEST["date"];
$time_start = $_REQUEST["time_start"];
$time_end = $_REQUEST["time_end"];
$price = $_REQUEST["price"];
$total = $_REQUEST["total"];
$options_book = $_REQUEST["options_book"];
foreach($data_array as $key => $value) {
$output = '<tr>';
$output .= '<td><input name="id['.$key.']" type="hidden" value="'.$value[$key].'" /></td>';
$output .= '<td><input name="qty['.$key.']" type="hidden" value="'.$qty[$key].'" /></td>';
$output .= '<td><input name="date['.$key.']" type="hidden" value="'.$date[$key].'" /></td>';
$output .= '<td><input name="time_start['.$key.']" type="hidden" value="'.$time_start[$key].'" /></td>';
$output .= '<td><input name="time_end['.$key.']" type="hidden" value="'.$time_end[$key].'" /></td>';
$output .= '<td><input name="price['.$key.']" type="hidden" value="'.$price[$key].'" /></td>';
$output .= '<td><input name="total" type="hidden" value="'.$total.'" /><input name="options_book['.$key.']" type="hidden" value="'.$options_book[$key].'" /></td>';
$output .= '</tr>';
echo $output;
}
	?>
  <div class="checkoutrow">
 <div class="leftcheck"><button class="button" type="submit" name="conferma" value="send" ><?php echo SEND ?></button></div>
</div>
</div>
</form>

<?php //$db->Close();  ?>

<?php } ?>