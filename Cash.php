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

// load nescesary headers and apply style-sheets
include("inc.header.php");
html_bootstrap3_createHeader("de","CoffeeDB",$conf['base_url']);

// get current year and month
$transdate = date('Y-m-d', time());
$year = date('Y', strtotime($transdate));
$month = date('m', strtotime($transdate));

// load database
$database = __DIR__ . "/CoffeeDB.json";
$coffee_db = array(); // create empty array
$jsondata = file_get_contents($database);
$coffee_db = json_decode($jsondata, true);

// load costs
$users = $coffee_db["Users"];
$sorted_users = $users;
sort($sorted_users);
$usercash = $coffee_db["Cash"];
$cost_black = $coffee_db["Kosten"]["Schwarz"];
$cost_milk = $coffee_db["Kosten"]["Milch"];
?>

<body>
<br>
<div class="container">
<form name='volume' method='post' action='<?php print $_SERVER['PHP_SELF']; ?>'>

<div class="panel-group">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <i class='mdi mdi-coffee'></i> Kaffee-Kassensystem
        <br><br>
	<select name="userselect" class="form-control">
	<?php
	foreach ($sorted_users as $user) {
	   echo '<option value="' . $user .'">' . $user . '</option>';
	}
	echo '<option value=" ________________"> ________________</option>';
	echo '<option value="Kasse">Kasse</option>';
	?>
	</select>
	<br>
	<input value="" id="name" name="cashedit" placeholder="Bitte Ein- oder Auszahlungs-Betrag eingeben..." class="form-control input-md" type="text">
	<br>
	<button id="change_cash" name="change_cash" class="btn btn-success" value="add">Einzahlen</button>
	<button id="change_cash" name="change_cash" class="btn btn-danger" value="remove">Auszahlen</button>
	<button id="change_cash" name="change_cash" class="btn btn-info" value="show">Kontostand</button>
	<button id="change_cash" name="change_cash" class="btn btn-info" value="overview">Gesamtübersicht</button>
      </h4>
    </div><!-- /.panel-heading -->

    <div class="panel-body">
	<?php
	if(isset($_POST["change_cash"])) {
          if($_POST["change_cash"] == "overview"){
	    echo "<b>Übersicht des aktuellen Monats ". $month . "/" . $year . "</b><br><br>";
	    $sum_coffee_black = 0;
	    $sum_coffee_milk = 0;
	    $sum_cash = 0;
	    echo '<table style="width:100%">';
	    echo "<tr>";
	    echo "<th>Name</th><th>Schwarz</th><th>Milch</th><th>Kontostand</th>";
	    echo "</tr>";
	    foreach(array_keys($coffee_db[$year][$month]) as $username){
	      $sum_coffee_black += $coffee_db[$year][$month][$username]["Schwarz"];
	      $sum_coffee_milk += $coffee_db[$year][$month][$username]["Milch"];

	      if(array_key_exists($username, $usercash)){
	        $cash = $usercash[$username];
	      }else{
	        $cash = 0;
	      }
	      $sum_cash += $cash;
	      echo "<tr>";
	      if($cash<0){
	        echo "<td>" . $username . "</td><td>" . $coffee_db[$year][$month][$username]["Schwarz"] . "</td><td>" . $coffee_db[$year][$month][$username]["Milch"] . "</td><td><font color=red>" . number_format($cash,2,",",".") . "€</font></td>";
	      }else{
	        echo "<td>" . $username . "</td><td>" . $coffee_db[$year][$month][$username]["Schwarz"] . "</td><td>" . $coffee_db[$year][$month][$username]["Milch"] . "</td><td><font color=green>" . number_format($cash,2,",",".") . "€</font></td>";
	      }
	      echo "</tr>";
	    }
	    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	    echo "<tr><td>Summe aktueller Nutzer</td><td>" . $sum_coffee_black . " (" . number_format(($sum_coffee_black*$cost_black),2,",",".") . "€)</td><td>" . $sum_coffee_milk . " (" . number_format(($sum_coffee_milk*$cost_milk),2,",",".") . "€)</td><td>" . number_format($sum_cash,2,",",".") . "€</td></tr>";
	    echo "<tr><td>Gesamtsumme</td><td>&nbsp;</td><td>&nbsp;</td><td>" . number_format($usercash["Kasse"],2,",",".") . "€</td></tr>";
	    echo "</table><br>";

            // create chart and show it
            echo "<br><img src=\"chart.php?name=total\"></img>";
	  }else{
	  if(($_POST["userselect"]==" Bitte Nutzer auswählen...") || ($_POST["userselect"]==" ________________") || ($_POST["userselect"]==" Neuer Nutzer")) {
	    $name = "";
	  }else{
	    $name = $_POST["userselect"];
	  }

	  if(strlen($name)>3) {
	    if($_POST["cashedit"]!=="") {
	      if($_POST["change_cash"] == "add"){
                $cash_change=(float)str_replace(',','.',$_POST["cashedit"]);
	      }else if($_POST["change_cash"] == "remove"){
                $cash_change=-(float)str_replace(',','.',$_POST["cashedit"]);
	      }else{
                $cash_change=0;
	      }
	    }else{
	      $cash_change=0;
	    }

	    if(array_key_exists($name, $usercash)){
	      $cash = $usercash[$name] + $cash_change;

	      if($cash_change>0){
		echo $cash_change . "€ eingezahlt<br><br>";
	      }else if($cash_change<0){
		echo -$cash_change . "€ ausgezahlt<br><br>";
	      }

	      if($cash<0){
                echo "<font color=red><i class='mdi mdi-cash-multiple'></i> " . "<b>Kontostand: " . number_format($cash,2,",",".") . "€</b></font><br>";
	      }else{
                echo "<font color=green><i class='mdi mdi-cash-multiple'></i> " . "<b>Kontostand: " . number_format($cash,2,",",".") . "€</b></font><br>";
	      }
	      $coffee_db["Cash"][$name] += $cash_change;
	      if($name!=="Kasse"){
	        $coffee_db["Cash"]["Kasse"] += $cash_change;
	      }
	      if($cash_change!=0){
                echo "<br><i class='mdi mdi-cash-multiple'></i> " . "<b>Neuer Gesamt-Kontostand: " . number_format($coffee_db["Cash"]["Kasse"],2,",",".") . "€</b><br>";
	      }
 	    }else{
	      // kein Kontoeintrag gefunden
	      if($cash_change!=0){
	        // neuen Kontroeintrag anlegen
	        $cash = $cash_change;
                if($cash<0){
                  echo "<font color=red><i class='mdi mdi-cash-multiple'></i> " . "<b>Kontostand: " . number_format($cash,2,",",".") . "€</b></font><br>";
                }else{
                  echo "<font color=green><i class='mdi mdi-cash-multiple'></i> " . "<b>Kontostand: " . number_format($cash,2,",",".") . "€</b></font><br>";
                }
	        $coffee_db["Cash"][$name] = $cash_change;
	        if($name!=="Kasse"){
	          $coffee_db["Cash"]["Kasse"] += $cash_change;
	        }
	        if($cash_change!=0){
                  echo "<br><i class='mdi mdi-cash-multiple'></i> " . "<b>Neuer Gesamt-Kontostand: " . number_format($coffee_db["Cash"]["Kasse"],2,",",".") . "€</b><br>";
		}
	      }else{
	        // kein Kontoeintrag gefunden, somit auch keine Kontoauskunft
	        echo "<font color=red><b>Kein Kontoeintrag gefunden!</b></font>";
	      }
	    }

	    // Convert updated array to JSON and write json data into data.json file
	    $jsondata = json_encode($coffee_db, JSON_PRETTY_PRINT);
            if(!file_put_contents($database, $jsondata)) {
              echo "<br><br><b><font color=red>Error saving to database!</font></b>";
            }
	  }
	  }
	}
	$_POST=array(); //clear
	?>

	</div><!-- /.panel-body -->
  </div><!-- /.panel -->
</div><!-- /.panel-group -->
</form>
</div><!-- /.container -->
</body>
</html>
