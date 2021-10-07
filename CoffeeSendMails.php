<?php

/**
 * CoffeeDB - An interactive Coffee-Logging- and Invoice-System with eMail-notification for small offices
 * For PHP Version 7.x and later
 *
 * @see https://www.github.com/xn--nding-jua/CoffeeDB/ The CoffeeDB GitHub project
 * 
 * @author    Dr.-Ing. Christian Nöding <christian@noeding-online.de>
 * @license   https://www.gnu.org/licenses/gpl-3.0 GNU General Public License 3
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'components/PHPMailer/src/Exception.php';
require 'components/PHPMailer/src/PHPMailer.php';
require 'components/PHPMailer/src/SMTP.php'; 

$transdate = date('Y-m-d', time());
$year = date('Y', strtotime($transdate));
$month = date('m', strtotime($transdate));

if($month==1){
  $payyear=$year-1;
  $paymonth=12;
}else{
  $payyear=$year;
  $paymonth=sprintf('%02d', $month-1);
}

// as this file is called monthly, we will create a backup with datecode
//copy('CoffeeDB.json', 'DB_Backups/' . $year . $month . '_CoffeeDB.json');

// load settings
$settings = __DIR__ . "/settings.json";
$settings_db = array(); // create empty array
$settings_jsondata = file_get_contents($settings);
$settings_db = json_decode($settings_jsondata, true);

// load database
$database = __DIR__ . "/CoffeeDB.json";
$coffee_db = array(); // create empty array
$jsondata = file_get_contents($database);
$coffee_db = json_decode($jsondata, true);

// load costs
$usermails = $coffee_db["UserMails"];
$usercash = $coffee_db["Cash"];
$cost_black = $coffee_db["Kosten"]["Schwarz"];
$cost_milk = $coffee_db["Kosten"]["Milch"];

// New PHPMailer()
$mail = new PHPMailer();

// Mail-Configuration
$mail->IsSMTP();
$mail->CharSet = 'UTF-8';
$mail->SMTPDebug  = 0;
$mail->SMTPAuth   = true;
$mail->Host       = $settings_db["SMTPServer"];
$mail->Port       = $settings_db["SMTPPort"];
$mail->Username   = $settings_db["SMTPUsername"];
$mail->Password   = $settings_db["SMTPPassword"];

// Content
$mail->isHTML(true);
$mail->setFrom($settings_db["SendMailFrom"], $settings_db["FriendlyMailName"]);

// iterate through all objects
if(array_key_exists($payyear, $coffee_db)){
if(array_key_exists($paymonth, $coffee_db[$payyear])){
foreach(array_keys($coffee_db[$payyear][$paymonth]) as $name) {
  // $name contains the current coffee-users of the current month
  
  if(array_key_exists($name, $usermails)){
    // this user has an email-address
    $count_black = $coffee_db[$payyear][$paymonth][$name]["Schwarz"];
    $count_milk = $coffee_db[$payyear][$paymonth][$name]["Milch"];

    if(($count_black+$count_milk)>0){
      // send mail only if user has used the coffeemachine
      $emailaddress = $usermails[$name];

      if(array_key_exists($name, $usercash)){
        $cash = $usercash[$name];
      }else{
        $cash = 0;
      }

      $htmlmessagebody1 = "Hallo " . $name . ",<br><br><b>Dein Kaffee-Bezug für " . $paymonth . "/" . $payyear . "</b><br>";
      $htmlmessagebody2 = "Kaffee schwarz: " . $count_black . " (" . number_format($count_black*$cost_black,2,",",".") . "€)<br>";
      $htmlmessagebody3 = "Kaffee mit Milch: " . $count_milk . " (" . number_format($count_milk*$cost_milk,2,",",".") . "€)<br><br>";
      $htmlmessagebody4 = "<b>Summe: </b>" . number_format($count_black*$cost_black + $count_milk*$cost_milk,2,",",".") . "€<br>";
      if($cash<0){
        $htmlmessagebody5 = "<font color=red><b>Kontostand: </b>" . number_format($cash,2,",",".") . "€</font><br><br>";
        $htmlmessagebody6 = "Dein Kontostand erlaubt keine weiteren Koffeineskapaden. Zahl was ein!<br>Vielen Dank und einen angenehmen Tag.<br><br><img src=\"" . $settings_db["CoffeeDBURL"] . "chart.php?name=" . $name . "\">";
      }else if($cash>0){
        $htmlmessagebody5 = "<font color=green><b>Kontostand: </b>" . number_format($cash,2,",",".") . "€</font><br><br>";
        $htmlmessagebody6 = "Dein Konto ist noch ausreichend gedeckt. Du darfst ruhig noch mehr Kaffee trinken.<br>Vielen Dank und einen angenehmen Tag.<br><br><img src=\"" . $settings_db["CoffeeDBURL"] . "chart.php?name=" . $name . "\">";
      }else{
        $htmlmessagebody5 = "<b>Kontostand: </b>" . number_format($cash,2,",",".") . "€<br><br>";
        $htmlmessagebody6 = "Dein Kontostand erlaubt keine weiteren Koffeineskapaden. Zahl was ein!<br>Vielen Dank und einen angenehmen Tag.<br><br><img src=\"" . $settings_db["CoffeeDBURL"] . "chart.php?name=" . $name . "\">";
      }

      $messagebody1 = "Hallo " . $name . ", Dein Kaffee-Bezug für " . $paymonth . "/" . $payyear;
      $messagebody2 = "Kaffee schwarz: " . $count_black . " (" . number_format($count_black*$cost_black,2,",",".") . "€)";
      $messagebody3 = "Kaffee mit Milch: " . $count_milk . " (" . number_format($count_milk*$cost_milk,2,",",".") . "€)";
      $messagebody4 = "Summe: " . number_format($count_black*$cost_black + $count_milk*$cost_milk,2,",",".") . "€";
      $messagebody5 = "Kontostand: " . number_format($cash,2,",",".") . "€";
      if($cash<0){
        $messagebody6 = "Dein Kontostand erlaubt keine weiteren Koffeineskapaden. Zahl was ein! Vielen Dank und einen angenehmen Tag.";
      }else if($cash>0){
        $messagebody6 = "Dein Konto ist noch ausreichend gedeckt. Du darfst ruhig noch mehr Kaffee trinken. Vielen Dank und einen angenehmen Tag.";
      }else{
        $messagebody6 = "Dein Kontostand erlaubt keine weiteren Koffeineskapaden. Zahl was ein! Vielen Dank und einen angenehmen Tag.";
      }

      $mail->addAddress($emailaddress, $name);
      $mail->Subject = 'Monatliche automatische Kaffeerechnung ' . $paymonth . '/' . $payyear;
      $mail->Body    = $htmlmessagebody1 . $htmlmessagebody2 . $htmlmessagebody3 . $htmlmessagebody4 . $htmlmessagebody5 . $htmlmessagebody6;
      $mail->AltBody = $messagebody1 . $messagebody2 . $messagebody3 . $messagebody4 . $messagebody5 . $messagebody6;

      $mail->send();
      $mail->ClearAddresses();
    }
  }
}
}
}

?>
