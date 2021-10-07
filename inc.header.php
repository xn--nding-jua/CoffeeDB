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

function html_bootstrap3_createHeader($lang="en",$title="Welcome",$url_absolute="") {
    /*
    * HTML for the header and body tag
    */
    print "<!DOCTYPE html>
<html lang=\"".$lang."\">
    <head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

        <title>".$title."</title>

        <!-- Latest compiled and minified CSS -->
        <link rel=\"stylesheet\" href=\"".$url_absolute."components/bootstrap/css/bootstrap.darkly.css\">

        <!-- Latest compiled and minified JavaScript -->
        <script src=\"".$url_absolute."components/bootstrap/js/bootstrap.min.js\"></script>
        <script src=\"".$url_absolute."components/bootstrap/js/collapse.js\"></script>
        <script src=\"".$url_absolute."components/bootstrap/js/transition.js\"></script>

        <link href='".$url_absolute."components/MaterialDesign-Webfont/css/materialdesignicons.min.css' media='all' rel='stylesheet' type='text/css' />

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src=\"".$url_absolute."components/bootstrap/js/html5shiv3.7.2.min.js\"></script>
            <script src=\"".$url_absolute."components/bootstrap/js/respond1.4.2.min.js\"></script>
        <![endif]-->

        <link rel=\"icon\" type=\"image/png\" sizes=\"256x256\"  href=\"".$url_absolute."icons/coffee256.png\">
        <link rel=\"icon\" type=\"image/png\" sizes=\"128x128\" href=\"".$url_absolute."icons/coffee128.png\">
        <link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"".$url_absolute."icons/coffee32.png\">
        <link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"".$url_absolute."icons/coffee16.png\">
        <meta name=\"msapplication-TileColor\" content=\"#ffffff\">
        <meta name=\"msapplication-TileImage\" content=\"".$url_absolute."icons/coffee128.png\">
        <meta name=\"theme-color\" content=\"#ffffff\">

        <style type='text/css'>
        .playerControls {
            margin-bottom: 1em;
        }
        .controlPlayer {
            margin-right: 1em;
        }
        .btnFolder, .folderContent {
            max-width: 100%;
            overflow: hidden;
        }
        .btn-player-xl {
            padding:4px 0px;
            font-size:38px;
            line-height:1;
            border-radius:6px;
        }
        .btn-player-l {
            padding:0px 0px;
            font-size:30px;
            line-height:1;
            border-radius:6px;
        }
        .btn-player-m {
            padding:15px 16px;
            font-size:18px;
            line-height:1;
            border-radius:6px;
        }
        .btn-player-s {
            padding:15px 5px;
            font-size:11px;
            line-height:1;
            border-radius:6px;
        }
        .playerWrapper,
        .playerWrapperSub {
            display: block!important;
            clear: both;
            height: auto;
            margin: 0 auto;
            text-align: center;
            margin-top: 1em;
        }
        .playerWrapper a {
            color: #00bc8c!important;
        }
        .playerWrapper a:hover {
            color: #008966!important;
        }
        .playerWrapperCover img {
            max-height: 200px;
        }
        .playerWrapperSub a {
            color: #aaa!important;
        }
        .playerWrapperSub a:hover {
            color: #eee!important;
        }
        .table td.text {
            max-width: 100px;
        }
        .table td.text span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            max-width: 100%;
        }
        .mdi-72px.mdi-set, .mdi-72px.mdi:before {
          font-size: 72px;
        }
        .mdi-60px.mdi-set, .mdi-60px.mdi:before {
          font-size: 60px;
        }
        .hoverGrey:hover {
            color: #999!important;
        }
        .btn-panel-big {
            font-size: 3em!important;
            margin-right: 0.1em;
        }
        .btn-panel-col {
            /*color: #f39c12!important;*/
        }
        .btn-panel-col:hover {
            /*color: #e45c00!important;*/
        }
        .img-playlist-item {
            max-width: 100px;
            float: left;
            margin-right: 1em;
            border:1px solid white;
        }
        .img-playlist-item-placeholder {
            display: block;
            background-color: transparent;
            width: 100px;
            height: 50px;
            float: left;
            margin-right: 1em
        }
        /* anchor behaviour */
        
        /* flash briefly on click */
        .panel-heading a.btn-panel-big:active {
            color: #fff!important;
        }
        .panel-heading a.btn-panel-big {
            cursor: pointer;
        } 
        </style>
    <meta http-equiv=\"refresh\" content=\"120\" />
    </head>
    \n";
}

?>
