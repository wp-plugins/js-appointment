<?php
//session_start();
define('WP_USE_THEMES', true);
//require('../../wp-load.php');
global $wp_query, $post;
$post = $wp_query->post;
$post_id = $post->ID;
$url=get_bloginfo('wpurl');

$typepag = $_REQUEST["typepag"];
$conferma = $_REQUEST["conferma"];
$action = $_REQUEST["action"];
    if ($action == "book") {
      if ($typepag == 1) {
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

    $nome = $_REQUEST["nome"];
    $cognome = $_REQUEST["cognome"];
    $indirizzo = $_REQUEST["indirizzo"];
    $cap = $_REQUEST["cap"];
    $citta = $_REQUEST["citta"];
    $provincia = $_REQUEST["provincia"];
    $nazione = $_REQUEST["nazione"];
    $telefono = $_REQUEST["telefono"];
    $email = $_REQUEST["email"];
    $note = $_REQUEST["note"];
    $typepag = $_REQUEST["typepag"];
?>

 <table>
    <tr>
     <td></td>
     <td><?php echo BOOKSUMMARY ?></td>
   </tr>
   <tr>
     <td><?php echo NAME ?></td>
     <td><?php echo $nome ?></td>
   </tr>
   <tr>
     <td><?php echo SURNAME ?></td>
     <td><?php echo $cognome ?></td>
   </tr>
   <tr>
     <td><?php echo ADDRESS ?></td>
     <td><?php echo $indirizzo ?></td>
   </tr>
   <tr>
     <td><?php echo ZIP ?></td>
     <td><?php echo $cap ?></td>
   </tr>
   <tr>
     <td><?php echo CITY ?></td>
     <td><?php echo $citta ?></td>
   </tr>
   <tr>
     <td><?php echo PROVINCE ?></td>
     <td><?php echo $provincia ?></td>
   </tr>
   <tr>
     <td><?php echo COUNTRY ?></td>
     <td><?php echo $nazione ?></td>
   </tr>
   <tr>
     <td><?php echo PHONE ?></td>
     <td><?php echo $telefono ?></td>
   </tr>
   <tr>
     <td><?php echo EMAIL ?><</td>
     <td><?php echo $email ?></td>
   </tr>
   <tr>
     <td><?php echo NOTE ?></td>
     <td><?php echo $note ?></td>
   </tr>
   <tr>
     <td><?php echo PAYTYPE ?></td>
     <td><?php echo CASH ?></td>
   </tr></table>
   <table>
	<?php
		while($row = mysql_fetch_array($result))
		{
		}
$settings = allbook_get_settings();
$allbook_customemail = $settings['allbook_customemail'];
$data_array = $_REQUEST["id"];
$qty = $_REQUEST["qty"];
$date = $_REQUEST["date"];
$time_start = $_REQUEST["time_start"];
$time_end = $_REQUEST["time_end"];
$price = $_REQUEST["price"];
$total = $_REQUEST["total"];
$options_book = $_REQUEST["options_book"];
$validcharscode = "0123456789";
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
foreach($data_array as $key => $value) {
$validchars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$code  = "";
$counter   = 0;
$length = 10;

   while ($counter < $length) {
     $actChar = substr($validchars, rand(0, strlen($validchars)-1), 1);

     // All character must be different
     if (!strstr($code, $actChar)) {
        $code .= $actChar;
        $counter++;
     }
   }
$date_book = date('Y-m-d H:i:s');
$sql = "INSERT INTO wp_resservation_book (id, nome, cognome, indirizzo, cap, citta, prov, nazione, telefono, email, data_book, data, time_start, time_end, note, status, code, id_book, price, invoice, options ) VALUES ('', '".$nome."', '".$cognome."', '".$indirizzo."', '".$cap."', '".$citta."', '".$provincia."', '".$nazione."', '".$telefono."', '".$email."', '".$date_book."', '".$date[$key]."', '".$time_start[$key]."', '".$time_end[$key]."', '".$note."', '1', '".$code."', '".$key."', '".$price[$key]."', '".$invoice."', '".$options_book[$key]."') ";
mysql_query($sql);
$query1 = "SELECT id, date,time_start,time_end,max,price,status FROM $tbl_name WHERE id = '".$key."' LIMIT 1";
$result1 = mysql_query($query1);
$row1 = mysql_fetch_array($result1);
$quantita = ($row1['max'] - $qty[$key]);
mysql_query("UPDATE $tbl_name SET max='".$quantita."' WHERE id='".$key."'");

//echo $sql;
$output = '<tr>';
$output .= '<td>'.TIMESTART.' '.$time_start[$key].' '.TIMEEND.' '.$time_end[$key].' '.DATES.': '.$date[$key].' '.CODEBOOK.': '.$code.' '.$quantita.'</td>';
$output .= '<td><input name="id['.$key.']" type="hidden" value="'.$value[$key].'" />';
$output .= '<input name="qty['.$key.']" type="hidden" value="'.$qty[$key].'" />';
$output .= '<input name="date['.$key.']" type="hidden" value="'.$date[$key].'" />';
$output .= '<input name="time_start['.$key.']" type="hidden" value="'.$time_start[$key].'" />';
$output .= '<input name="time_end['.$key.']" type="hidden" value="'.$time_end[$key].'" /></td>';
$output .= '</tr>';
$output .= '<tr><td></td></tr>';
$output .= '<tr><td></td></tr>';
echo $output;
}
echo "<tr><td>".TOTAL." $ ".$total."</td><td></td></tr>";
//echo "<tr><td>Invoice Number <a href='".PLUGIN_URL_ALLBOOK."/invoice.php?invoice=".$invoice."'>".$invoice."</td><td></td></tr>";
$book_id = "";
foreach($data_array as $key => $value) {
$book_id .= $key.";";
}
//$invoice = rand(5, 10);
$status = "1";
$sqlinvoice = "INSERT INTO wp_resservation_invoice (id, book_id, invocie, price, status ) VALUES ('', '".$book_id."', '".$invoice."', '".$total."', '".$status."') ";
mysql_query($sqlinvoice);
//mail("francodanese60@gmail.com", "Nuova prenotazione", "Nuova prenotazione inserita ");
//mail("info@lucazone.net", "Nuova prenotazione", "Nuova prenotazione inserita");
// Recipients
$mailTo = get_bloginfo('admin_email'); // note the comma

// From
$mailFrom = get_bloginfo('admin_email');
$mailFromName = get_bloginfo('name');

// Reply address
$mailReplyTo = $email;

// Message subject and contents
$mailSubject = NEWBOOK;
$mailMessage = "<html><body>".MESSAGEEMAIL."</html></body>";

// Text message charset
$mailCharset = "windows-1252"; // must be accurate (e.g. "Windows - 1252" is invalid)

// Create headres for mail() function
$headers  = "Content-type: text/html; charset=$mailCharset\r\n";
$headers .= "From: $mailFromName <$mailFrom>\r\n";
$headers .= "Reply-To: $mailReplyTo\r\n";


// Send mail
wp_mail($mailTo, $mailSubject, $mailMessage, $headers);

// Recipients
$mailTo1 = $email; // note the comma

// From
$mailFrom1 = get_bloginfo('admin_email');
$mailFromName1 = get_bloginfo('name');

// Reply address
$mailReplyTo1 = get_bloginfo('admin_email');

// Message subject and contents
$mailSubject1 = "".NEWBOOK1."";
$mailMessage1 = "<html><body>".MESSAGEEMAIL1."".$allbook_customemail."</a><br />\n
</html></body>";

// Text message charset
$mailCharset1 = "windows-1252"; // must be accurate (e.g. "Windows - 1252" is invalid)

// Create headres for mail() function
$headers1  = "Content-type: text/html; charset=$mailCharset1\r\n";
$headers1 .= "From: $mailFromName1 <$mailFrom1>\r\n";
$headers1 .= "Reply-To: $mailReplyTo1\r\n";

// Send mail
wp_mail($mailTo1, $mailSubject1, $mailMessage1, $headers1);
$_SESSION=array(); // Desetta tutte le variabili di sessione.
session_destroy(); //DISTRUGGE la sessione.
?>
<tr>
<td></td>
<td></td>
</tr>
</table>

<?php //$db->Close();  ?>

<?php } }
?>