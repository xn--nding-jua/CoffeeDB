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
        <i class='mdi mdi-coffee'></i> Interaktive Kaffeebezugsliste
        <br><br>
	<select name="userselect" class="form-control">
	<?php
	foreach ($sorted_users as $user) {
	   echo '<option value="' . $user .'">' . $user . '</option>';
	}
	?>
	</select>
	<br>
	<input value="" id="name" name="nameedit" placeholder="Bitte neuen Nutzernamen eingeben..." class="form-control input-md" type="text">
	<br>
	<button id="inc_coffee" name="inc_coffee" class="btn btn-danger" value="black">Schwarz</button>
	<button id="inc_coffee" name="inc_coffee" class="btn btn-danger" value="doubleshot">Double-Shot</button>
	<button id="inc_coffee" name="inc_coffee" class="btn btn-warning" value="milk">Milchkaffee</button>
	<button id="inc_coffee" name="inc_coffee" class="btn btn-info" value="show">Info</button>
      </h4>
    </div><!-- /.panel-heading -->

    <div class="panel-body">
	<?php
	if(isset($_POST["inc_coffee"]) && isset($_POST["nameedit"])) {
	  if($_POST["userselect"]==" Neuer Nutzer"){
	    $name = $_POST["nameedit"];
	    if(strlen($name)>3){
	      array_push($users, $name);
	      $coffee_db["Users"] = $users;
	      $sorted_users = $users;
	      sort($sorted_users);
	      echo "Neue/r Kaffeetrinker/in " . $name . " wurde angelegt...<br><br>";
	    }
	  }else if(($_POST["userselect"]==" Bitte Nutzer auswählen...") || ($_POST["userselect"]==" ________________")) {
	    $name = "";
	  }else{
	    $name = $_POST["userselect"];
	  }

	 if(strlen($name)>3) {
	    if($_POST["inc_coffee"] == "black"){
              $black_increase=1;
              $milk_increase=0;
	    }else if($_POST["inc_coffee"] == "doubleshot"){
              $black_increase=2;
              $milk_increase=0;
	    }else if($_POST["inc_coffee"] == "milk"){
              $black_increase=0;
              $milk_increase=1;
	    }else{
              $black_increase=0;
              $milk_increase=0;
	    }

            if (!array_key_exists($year, $coffee_db)) {
              // a new year has begun
              echo "Es wurde ein neues Jahr angelegt...<br>";
              $coffee_db[$year] = array($month => array($name => array("Schwarz" => $black_increase, "Milch" => $milk_increase)));
            }else if (!array_key_exists($month, $coffee_db[$year])) {
              // a new month has begun
              echo "Es wurde ein neuer Monat angelegt...<br>";
              $coffee_db[$year][$month] = array($name => array("Schwarz" => $black_increase, "Milch" => $milk_increase));
            }else if (!array_key_exists($name, $coffee_db[$year][$month])) {
              // new user
              $coffee_db[$year][$month][$name] = array("Schwarz" => $black_increase, "Milch" => $milk_increase);
            }else{
              // user is known in this month, so increase or decrease the number of coffees
              $black_counter = $coffee_db[$year][$month][$name]["Schwarz"] + $black_increase;
              $milk_counter = $coffee_db[$year][$month][$name]["Milch"] + $milk_increase;
              $coffee_db[$year][$month][$name] = array("Schwarz" => $black_counter, "Milch" => $milk_counter);
            }

	    if(array_key_exists($name, $usercash)){
	      $cash = $usercash[$name] - ($black_increase*$cost_black) - ($milk_increase*$cost_milk);
 	    }else{
	      $cash = 0 - ($black_increase*$cost_black) - ($milk_increase*$cost_milk);
	    }
	    $coffee_db["Cash"][$name] = $cash;

            $count_black = $coffee_db[$year][$month][$name]["Schwarz"];
            $count_milk = $coffee_db[$year][$month][$name]["Milch"];
            echo "<i class='mdi mdi-account'></i><b> " . $name . "s Kaffee-Bezug für " . $month . "/" . $year  . "</b><br>";
            echo "<i class='mdi mdi-coffee-outline'></i> " . "Kaffee schwarz: " . $count_black . " (" . number_format($count_black*$cost_black,2,",",".") . "€)<br>";
            echo "<i class='mdi mdi-coffee'></i> " . "Kaffee mit Milch: " . $count_milk . " (" . number_format($count_milk*$cost_milk,2,",",".") . "€)<br><br>";
            echo "<i class='mdi mdi-cash-multiple'></i> " . "<b>Summe in " . $month . "/" . $year . ": " . number_format($count_black*$cost_black + $count_milk*$cost_milk,2,",",".") . "€</b><br>";
	    if($cash<0){
              echo "<font color=red><i class='mdi mdi-cash-multiple'></i> " . "<b>Kontostand: " . number_format($cash,2,",",".") . "€</b></font><br>";
	    }else if($cash>0){
              echo "<font color=green><i class='mdi mdi-cash-multiple'></i> " . "<b>Kontostand: " . number_format($cash,2,",",".") . "€</b></font><br>";
	    }else{
              echo "<i class='mdi mdi-cash-multiple'></i> " . "<b>Kontostand: " . number_format($cash,2,",",".") . "€</b><br>";
	    }

	    // create chart and show it
	    echo "<br><img src=\"chart.php?name=" . $name . "\"></img>";

	    // Convert updated array to JSON and write json data into data.json file
	    $jsondata = json_encode($coffee_db, JSON_PRETTY_PRINT);
            if(!file_put_contents($database, $jsondata)) {
              echo "<br><br><b><font color=red>Error saving to database!</font></b>";
            }
	  }
	}else{
	  echo "<div id=\"wcom-f30fbf2256ad85d20ca9b854fa81e740\" class=\"wcom-default w300x250\" style=\"border: 1px solid rgb(204, 204, 204); background-color: rgb(238, 238, 238); border-radius: 5px; color: rgb(0, 0, 0);\"><link rel=\"stylesheet\" href=\"//cs3.wettercomassets.com/woys/5/css/w.css\" media=\"all\"><div class=\"wcom-city\"><a style=\"color: #000\" href=\"https://www.wetter.com/deutschland/kassel/DE0005331.html\" target=\"_blank\" rel=\"nofollow\" aria-label=\"Wetter Berlin\" title=\"Wetter Kassel\">Wetter Kassel</a></div><div id=\"wcom-f30fbf2256ad85d20ca9b854fa81e740-weather\"></div><script type=\"text/javascript\" src=\"//cs3.wettercomassets.com/woys/5/js/w.js\"></script><script type=\"text/javascript\">_wcomWidget({id: 'wcom-f30fbf2256ad85d20ca9b854fa81e740',location: 'DE0005331',format: '300x250',type: 'spaces'});</script></div>";
	}

	echo "<div id=\"countdowncounter\"></div>";
	echo "<script src=\"countdown.js\"></script>";

	$_POST=array(); //clear
	?>

	</div><!-- /.panel-body -->
  </div><!-- /.panel -->
</div><!-- /.panel-group -->
</form>
</div><!-- /.container -->
</body>

</html>
