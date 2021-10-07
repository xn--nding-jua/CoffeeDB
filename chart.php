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

require_once ('components/jpgraph/src/jpgraph.php');
require_once ('components/jpgraph/src/jpgraph_line.php');
require_once ('components/jpgraph/src/jpgraph_bar.php');

// get current year and month
$transdate = date('Y-m-d', time());
$year = date('Y', strtotime($transdate));
$month = date('m', strtotime($transdate));
$day = date('d', strtotime($transdate));
$tagesnamen = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
$monatsnamen = array("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
$monatsnamen_kurz = array("Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez");

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

// calculate number of work-days in a specific month of a specific year
function countDays($year, $month, $ignore) {
    $count = 0;
    $counter = mktime(0, 0, 0, $month, 1, $year);
    while (date("n", $counter) == $month) {
        if (in_array(date("w", $counter), $ignore) == false) {
            $count++;
        }
        $counter = strtotime("+1 day", $counter);
    }
    return $count;
}

// calculate number of workdays between two dates
function getWorkdays($date1, $date2, $workSat = FALSE, $patron = NULL) {
  if (!defined('SATURDAY')) define('SATURDAY', 6);
  if (!defined('SUNDAY')) define('SUNDAY', 0);

  // Array of all public festivities
  $publicHolidays = array('01-01', '01-06', '04-25', '05-01', '06-02', '08-15', '11-01', '12-08', '12-25', '12-26');
  // The Patron day (if any) is added to public festivities
  if ($patron) {
    $publicHolidays[] = $patron;
  }

  /*
   * Array of all Easter Mondays in the given interval
   */
  $yearStart = date('Y', strtotime($date1));
  $yearEnd   = date('Y', strtotime($date2));

  for ($i = $yearStart; $i <= $yearEnd; $i++) {
    $easter = date('Y-m-d', easter_date($i));
    list($y, $m, $g) = explode("-", $easter);
    $monday = mktime(0,0,0, date($m), date($g)+1, date($y));
    $easterMondays[] = $monday;
  }

  $start = strtotime($date1);
  $end   = strtotime($date2);
  $workdays = 0;
  for ($i = $start; $i <= $end; $i = strtotime("+1 day", $i)) {
    $day = date("w", $i);  // 0=sun, 1=mon, ..., 6=sat
    $mmgg = date('m-d', $i);
    if ($day != SUNDAY &&
      !in_array($mmgg, $publicHolidays) &&
      !in_array($i, $easterMondays) &&
      !($day == SATURDAY && $workSat == FALSE)) {
        $workdays++;
    }
  }

  return intval($workdays);
}

// create array for chart
$name = $_GET["name"];
$chart_dataPoints_X = array();
$chart_dataPoints_Y1 = array();
$chart_dataPoints_Y2 = array();
foreach($coffee_db as $year_key => $year_value){
  if($year_key==$year){
    foreach($year_value as $month_key => $month_value){
      if($name=="total"){
	// create sum over all names and add it to array
	$sum_black=0;
	$sum_milk=0;
	foreach($month_value as $name_key => $name_value){
	  $sum_black+=$name_value["Schwarz"];
	  $sum_milk+=$name_value["Milch"];
	}
        if ((int)$month_key==$month) {
            $dcoff_dtag=($sum_black+$sum_milk)/(getWorkdays(date('Y-M-d', strtotime($month . '/01/' . $year)), date('Y-M-d', strtotime($month . '/' . $day . '/' . $year)), FALSE, NULL));
        }else{
            $dcoff_dtag=($sum_black+$sum_milk)/(countDays($year, $month, array(0,6)));
        }
        array_push($chart_dataPoints_X, $monatsnamen_kurz[(int)$month_key-1] . " / " . number_format($dcoff_dtag, 1, '.', ''));
        array_push($chart_dataPoints_Y1, $sum_black);
        array_push($chart_dataPoints_Y2, $sum_milk);
      }else{
        if(array_key_exists($name, $month_value)){
          if ((int)$month_key==$month) {
              $dcoff_dtag=($month_value[$name]["Schwarz"]+$month_value[$name]["Milch"])/(getWorkdays(date('Y-M-d', strtotime($month . '/01/' . $year)), date('Y-M-d', strtotime($month . '/' . $day . '/' . $year)), FALSE, NULL));
          }else{
              $dcoff_dtag=($month_value[$name]["Schwarz"]+$month_value[$name]["Milch"])/(countDays($year, $month, array(0,6)));
          }
          array_push($chart_dataPoints_X, $monatsnamen_kurz[(int)$month_key-1] . " / " . number_format($dcoff_dtag, 1, '.', ''));
          array_push($chart_dataPoints_Y1, $month_value[$name]["Schwarz"]);
          array_push($chart_dataPoints_Y2, $month_value[$name]["Milch"]);
        }
      }
    }
  }
}


// Create the graph.
$graph = new Graph(600,300);
$graph->clearTheme();
$graph->SetScale('textlin');

$graph->img->SetMargin(40,20,0,50);
$graph->img->SetAntiAliasing();
//$graph->SetShadow();

//$theme_class = new AquaTheme;
$theme_class = new SoftyTheme;
$graph->SetTheme($theme_class);

// Create plot1
$plot1=new BarPlot($chart_dataPoints_Y1);
$plot1->SetColor('black');
$plot1->SetFillColor('red');
$plot1->SetLegend('Schwarz');

// Create plot2
$plot2 = new BarPlot($chart_dataPoints_Y2);
$plot2->SetColor('black');
$plot2->SetFillColor('blue');
$plot2->SetLegend('Milchkaffee');

// Add the plots to the graph
//$graph->Add($plot1);
//$graph->Add($plot2);
// Create the grouped bar plot
$gbplot = new GroupBarPlot(array($plot1,$plot2));
$graph->Add($gbplot);

$graph->title->Set($name . 's Kaffeebezug ' . $year);
$graph->xaxis->title->Set('Monat / Tassen pro Werktag');
$graph->yaxis->title->Set('Kaffeeanzahl');

//$graph->title->SetFont(FF_FONT2,FS_BOLD);
//$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
//$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->xaxis->SetTickLabels($chart_dataPoints_X);
$graph->xaxis->SetTextTickInterval(1);
$graph->xaxis->SetTextLabelInterval(1);
$graph->xaxis->SetLabelAngle(0);

$graph->legend->Pos(0.02,0.035,"right","center");

// Display the graph
$graph->Stroke();
?>

