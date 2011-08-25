<?php
define('WP_USE_THEMES', false);
require('../../../wp-load.php');
    //global $wpdb,  $query_string;
  	if (!empty($_REQUEST['search_action'])) {
		switch($_REQUEST['search_action']) {
			case 'searchadv':
	    require_once(PLUGIN_PATH_ALLBOOK."/libs/class.book.php");
	    $settings = allbook_get_settings();

        if ($_REQUEST["cat"]) {
        $category = $_REQUEST["cat"];
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
print "<div id=\"contentdata\">";
print "<table class=\"month\">";

//request date
$day = $_REQUEST["day"];
$months = $_REQUEST["month"];
$years = $_REQUEST["year"];
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
$avv = "<input type=\"radio\" name=\"id\" value=\"".$row['id']."\" id=\"idbook\" /".PLACEAVV.":";
} else if($row['max'] >= 2) {
$avv = "<input type=\"radio\" name=\"id\" value=\"".$row['id']."\" id=\"idbook\" />".PLACEAVVS.":";
} else if($row['max'] <= 0) {
$avv = NOTVVS;
} else {
$avv = NOTVVS;
}
    print "<tr>";
    echo "<td>".$times[$key] = date('H:i:s', $time)."</td>";
    if($row['max'] >= 1) {
    echo "<td>".$avv." ".$row['max']." </td>";
    } else if($row['max'] <= 0) {
    echo "<td>".$avv."</td>";
    }
    print "</tr>";
}
print "</table>";
print "</div>";
//$db->Close();
	}
    }


?>