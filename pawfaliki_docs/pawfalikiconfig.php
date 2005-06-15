<?php
//============================
// Pawfaliki configuration file
//============================
$config['GENERAL']['TITLE'] = "Pawfaliki"; // Title of the wiki
$config['GENERAL']['HOMEPAGE'] = "Pawfaliki"; // The title of the homepage
$config['GENERAL']['ADMIN'] = "webmaster at pawfal dot org"; // not used currently
$config['GENERAL']['CSS'] = "Pawfaliki:pawfaliki.css"; // CSS file (title:filename)
$config['GENERAL']['SHOW_CONTROLS'] = false; // show all the wiki controls - edit, save, PageList etc...
$config['USERS']['admin'] = "pawfalikirocks"; // changing this would be a good idea!
$config['RESTRICTED']['HomePage'] = array("admin");
$config['RESTRICTED']['Documentation'] = array("admin");
?>
