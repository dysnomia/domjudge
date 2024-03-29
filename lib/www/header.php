<?php
/**
 * Common page header.
 * Before including this, one can set $title, $ajaxtitle, $refresh,
 * $printercss, $jscolor and $menu.
 *
 * Part of the DOMjudge Programming Contest Jury System and licenced
 * under the GNU GPL. See README and COPYING for details.
 */
if (!defined('DOMJUDGE_VERSION')) die("DOMJUDGE_VERSION not defined.");

header('Content-Type: text/html; charset=' . DJ_CHARACTER_SET);

/* Prevent clickjacking by forbidding framing in modern browsers.
 * Really want to frame DOMjudge? Then change DENY to SAMEORIGIN
 * or even comment out the header altogether. For the public
 * interface there's no risk, and embedding the scoreboard in a
 * frame may be useful.
 */
if ( ! IS_PUBLIC ) header('X-Frame-Options: DENY');

if ( isset($refresh) &&
     (!isset($_COOKIE["domjudge_refresh"]) ||
      (bool)$_COOKIE["domjudge_refresh"]) ) {
	header('Refresh: ' . $refresh);
}

if(!isset($menu)) {
	$menu = true;
}
if(!isset($ajaxtitle)) {
	$ajaxtitle = '';
}
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
	<!-- DOMjudge version <?php echo DOMJUDGE_VERSION?> -->
<title><?php echo $title?></title>
<link rel="shortcut icon" href="../images/favicon.png" type="image/png" />
<link rel="stylesheet" href="../style.css" type="text/css" />
<?php
if ( IS_JURY ) {
	echo "<link rel=\"stylesheet\" href=\"style_jury.css\" type=\"text/css\" />\n";
	if (isset($printercss)) {
		echo "<link rel=\"stylesheet\" href=\"style_printer.css\" type=\"text/css\" media=\"print\" />\n";
	}
	if (isset($jscolor)) {
		echo "<script type=\"text/javascript\" src=\"" .
		"../js/jscolor.js\"></script>\n";
	}
	echo "<script type=\"text/javascript\" src=\"" .
		"../js/sorttable.js\"></script>\n";
}
if ( IS_PUBLIC ) {
	echo "<script type=\"text/javascript\" src=\"../js/djfav.js\"></script>\n";
} else {
	echo "<script type=\"text/javascript\" src=\"../js/domjudge.js\"></script>\n";
}
if ( ! empty($extrahead) ) echo $extrahead;
?>
</head>
<?php

if ( IS_JURY ) {
	echo "<body onload=\"setInterval('updateClarifications(\'$ajaxtitle\')', 20000)\">\n";
} else {
	echo "<body>\n";
}

/* NOTE: here a local menu.php is included
 *       both jury and team have their own menu.php
 */
if ( $menu ) include("menu.php");
