<?php

/*  Pawfaliki
 *  Copyright (C) 2005 Dan Bethell <dan at pawfal dot org>
 *                     Marc Vinyes <marc_contrib at ramonvinyes dot es>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

// setup some global storage
$config = array();
$config['PAWFALIKI_VERSION'] = "0.4.0"; // Pawfaliki version
$config['GENERAL'] = array();
$config['SYNTAX'] = array();
$config['BACKUP'] = array();
$config['RSS'] = array();
$config['EMAIL'] = array();
$config['USERS'] = array();
$config['RESTRICTED'] = array();
$config['BLOCKED_IPS'] = array();
$config['MISC'] = array();
$config['LOCALE'] = array();
$config['SPECIAL'] = array();
$config['LICENSE'] = array();
$config['INTERNAL'] = array('VERBATIM', 'ERRORS', 'DATA'); // used internally
$config['INTERNAL']['VERBATIM'] = array();
$config['INTERNAL']['ERRORS'] = array();
$config['INTERNAL']['DATA'] = array();

//================
//================
// CONFIGURATION
//================
//================

// GENERAL: General configuration stuff
$config['GENERAL']['TITLE'] = "Pawfaliki"; // Title of the wiki
$config['GENERAL']['HOMEPAGE'] = "HomePage"; // The title of the homepage
$config['GENERAL']['ADMIN'] = "webmaster at nowhere dot example"; // not used currently
$config['GENERAL']['CSS'] = "Pawfaliki:pawfaliki.css"; // CSS file (title:filename)
$config['GENERAL']['PAGES_DIRECTORY'] = "./PawfalikiPages/"; // Path to stored wiki pages
$config['GENERAL']['TEMP_DIRECTORY'] = "./PawfalikiTemp/"; // Path to temporary directory for backups
$config['GENERAL']['MODTIME_FORMAT'] = "(D M j G:i:s T Y)"; // date() compatible format string for the pagelist

// SYNTAX: Wiki editing syntax
$config['SYNTAX']['SHOW_BOX'] = true; // Display the wiki syntax box on edit page
$config['SYNTAX']['AUTOLINKING'] = true; // Auto-generation of WikiLinks
$config['SYNTAX']['HTMLCODE'] = false; // Allows raw html using %% tags

// BACKUP: Backup & Restore settings
$config['BACKUP']['ENABLE'] = true; // Enable backup & restore
$config['BACKUP']['USE_ZLIB'] = true; // If available use the libz module to produce gzipped backups
$config['BACKUP']['MAX_SIZE'] = 3000000; // maximum file size (in bytes) for uploading restore files

// RSS: RSS feed
$config['RSS']['ENABLE'] = true; // Enable rss support (http://mywiki.example?format=rss)
$config['RSS']['ITEMS'] = 10; // The number of items to display in rss feed (-1 for all).
$config['RSS']['TITLE_MODTIME'] = false; // Prints the modification time in the item title.
$config['RSS']['MODTIME_FORMAT'] = "(Y-m-d H:i:s T)"; // date() compatible format string

// CHANGES: email page changes
$config['EMAIL']['ENABLE'] = false; // do we email page changes?
$config['EMAIL']['CHANGES_TO'] = "admin@nowhere.example"; // if so, where to
$config['EMAIL']['CHANGES_FROM'] = "pawfaliki-changes@nowhere.example"; // & where from
$config['EMAIL']['MODTIME_FORMAT'] = "Y-m-d H:i:s"; // date() compatible format string for the pagelist
$config['EMAIL']['SHOW_IP'] = false; // show the modifiers ip in the email subject

// PASSWORDS: setup passwords
$config['USERS']['admin'] = "adminpassword"; // changing this would be a good idea!

// RESTRICTED: give access to some users to edit restricted pages
$config['RESTRICTED']['RestoreWiki'] = array("admin"); // only admin can restore wiki pages
//$config['RESTRICTED']['HomePage'] = array("admin"); // lock the homepage - admin only

// IP BLOCKING: blocked IP addresses
// $config['BLOCKED_IPS'][] = "192.168.0.*"; // block this ip address (can take wildcards)

// MISC: Misc stuff
$config['MISC']['EXTERNALLINKS_NEWWINDOW'] = false; // Open external links in a new window

// LOCALE: text for some titles, icons, etc - you can use wiki syntax in these for images etc...
$config['LOCALE']['EDIT_TITLE'] = "Edit: "; // title prefix for edit pages
$config['LOCALE']['HOMEPAGE_LINK'] = "[[HomePage]]"; // link to the homepage
$config['LOCALE']['PAGELIST_LINK'] = "[[PageList]]"; // link to the pagelist
$config['LOCALE']['BACKUP_LINK'] = "[[BackupWiki|backup]]"; // link to the backup page
$config['LOCALE']['RESTORE_LINK'] = "[[RestoreWiki|restore]]"; // link to the restore page
$config['LOCALE']['REQ_PASSWORD'] = "(locked)"; // printed next to the edit btn on a locked page
$config['LOCALE']['PASSWORD_TEXT'] = "Password:"; // printed next to the password entry box

// SPECIAL PAGES - reserved and unmodifiable by users
$config['SPECIAL']['PageList'] = 1; // the page list
$config['SPECIAL']['BackupWiki'] = 1; // the backup page
$config['SPECIAL']['RestoreWiki'] = 1; // the restore page

// LICENSES: pages with special licenses
$config['LICENSE']['DEFAULT'] = "creativeCommonsLicense"; // will call creativeCommonsLicense() function
$config['LICENSE']['PageList'] = "noLicense"; // will call noLicense() function
$config['LICENSE']['BackupWiki'] = "noLicense"; // will call noLicense() function
$config['LICENSE']['RestoreWiki'] = "noLicense"; // will call noLicense() function

//===========================================================================
//===========================================================================

// our licensing information
function creativeCommonsLicense()
{
	?>
			<!-- Creative Commons License -->
			This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/2.0/">Creative Commons License</a>.
			<!-- /Creative Commons License -->
			<!--
			<rdf:RDF xmlns="http://web.resource.org/cc/"
			    xmlns:dc="http://purl.org/dc/elements/1.1/"
			    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
				<Work rdf:about="">
				   <dc:type rdf:resource="http://purl.org/dc/dcmitype/Text" />
				   <license rdf:resource="http://creativecommons.org/licenses/by-sa/2.0/" />
				</Work>
				<License rdf:about="http://creativecommons.org/licenses/by-sa/2.0/">
				   <permits rdf:resource="http://web.resource.org/cc/Reproduction" />
				   <permits rdf:resource="http://web.resource.org/cc/Distribution" />
				   <requires rdf:resource="http://web.resource.org/cc/Notice" />
				   <requires rdf:resource="http://web.resource.org/cc/Attribution" />
				   <permits rdf:resource="http://web.resource.org/cc/DerivativeWorks" />
				   <requires rdf:resource="http://web.resource.org/cc/ShareAlike" />
				</License>
			</rdf:RDF>
			-->
	<?php
}

// blank license()
function noLicense()
{
	global $config;
	echo(wikiparse("Powered by [[http://www.pawfal.org/pawfaliki|Pawfaliki v".$config['PAWFALIKI_VERSION']."]]"));
}

// initialise our style sheets
function css( $pagename )
{
	global $config;
	$css = $config['GENERAL']['CSS'];
	if ($css!="")
	{
  		$tokens = explode(":", $css );
	    $title = $tokens[0];
		$path = implode(":", array_slice( $tokens, 1));
	    echo( "\t<LINK REL=\"stylesheet\" TYPE=\"text/css\" HREF=\"".$path."\" TITLE=\"".$title."\">\n");
		if ( $config['RSS']['ENABLE'] &&$pagename=="HomePage" )
			echo( "\t<LINK REL=\"alternate\" TITLE=\"".$config['GENERAL']['TITLE']." RSS\" HREF=\"".$_SERVER['PHP_SELF']."?format=rss\" TYPE=\"application/rss+xml\">\n" );
	}
}

// emails page changes
function emailChanges( $title, $contents )
{
	global $config;
	if ( $config['EMAIL']['ENABLE'] )
	{
		$date = date($config['EMAIL']['MODTIME_FORMAT']);
		$subject = $title." :: ".$date;
		if ( $config['EMAIL']['SHOW_IP'] )
		{
			$ipaddress = $_SERVER['REMOTE_ADDR'];
			$subject .= " :: IP ".$ipaddress;
		}
		mail( $config['EMAIL']['CHANGES_TO'], $subject, $contents, "From: ".$config['EMAIL']['CHANGES_FROM']."\r\n" );
	}
}

// writes a file to disk
function writeFile( $title, $contents )
{
	if (!$fd = @fopen( pagePath( $title ), "w" ))
	{ 
		error("Cannot open server's file for writing: ".pagePath( $title ));
		return 1;
	}
	
	if (@fwrite( $fd, $contents ) === FALSE)
	{
		error("Cannot write to server's file: ".pagePath( $title ));
		return 2;
	}
	
	// email page changes
	emailChanges( $title, $contents );
	
	fclose( $fd );	
	return 0;	
}

// reads the contents of a file into a string (php<4.3.0 friendly)
function pawfalikiReadFile( $filename )
{
	$result = "";			
	$size = filesize($filename);
	if ( $size>0 )
	{
		$handle = fopen($filename, "r");
		$result = fread($handle, $size);
		fclose($handle);					
	}
	return $result;
}

// returns the contents of a directory (php<4.3.0 friendly)
function pawfalikiReadDir($path)
{
	$results = array();
	if ($handle = @opendir($path)) 
	{
		while (false !== ($file = readdir($handle))) 
		{
			if ($file != "." && $file != "..") 
			{
				$results[] = $path."/".$file;
			}
		}
		closedir($handle);
	}
	return $results;
}

// init the wiki if no pages exist
function initWiki( $title )
{
	$contents = "Hello and welcome to Pawfaliki!";	
	writeFile( $title, $contents );
}

// get the title of a page
function getTitle()
{
	$page = "";
	if ( !isset($_REQUEST['page']) )
	{
		$page = "HomePage";
		if ( !pageExists( $page ) )
		{
			initWiki( $page );
		}
	}
	else
	{
		$page = $_REQUEST['page'];
	}
	return $page;
}

// get the current wiki 'mode'
function getMode()
{
	$mode = "";
	if ( !isset($_POST['mode']) )
	{
		$mode = "display";
	}
	else
	{
		$mode = $_POST['mode'];
	}
	return $mode;
}

// check 
function authPassword( $title, $password )
{
	global $config;
	$auth = false;
	foreach ($config['RESTRICTED'][$title] as $user)
	{
		if ($config['USERS'][$user]==$_POST['password']) 
		$auth = true;
	}
	return $auth;
}

// update the wiki - save/edit/backup/restore/cancel
function updateWiki( &$mode, $title, $config )
{	
	$backupEnabled = $config['BACKUP']['ENABLE'];
	// cleanup any temp files
	if ( $backupEnabled )
		cleanupTempFiles();
	
	// backup the wiki
	if ( $title=="BackupWiki" )
		if( $backupEnabled )
		{
			$wikiname = str_replace( " ", "_", $config['GENERAL']['TITLE'] );
			$date = date( "Y-m-d_H-i-s" );
			$filename = tempDir().$wikiname."_".$date.".bkup";
			backupPages( $filename );
			$mode = "backup";
		}
		else
		{
			error( "Backups have been disabled." );
		}
		
	// restore from backup
	if ( $title=="RestoreWiki" )
		if ( $backupEnabled )
		{
			if ( $mode=="restore"&&isset($_FILES['userfile']['name']) )
				restorePages();
			else
				$mode = "restore";
		}
		else		
		{
			error( "Restore has been disabled." );
			$mode = "restore";
		}

	// save page
	if ( $mode=="save" )
	{
		if ( isset($_POST['contents']) )
		{
			$contents = stripslashes( $_POST['contents'] );

			// restricted access
			$restricted=false;
			if (isLocked($title))
			{
				// check if the password is correct
				$restricted=!authPassword($title, $_POST['password']);
				if ($restricted)
					error("Wrong password. Try again.");
			}

			// write file    
			if (!isIpBlocked() && !$restricted)
				$error = writeFile( $title, $contents );
		}
		$mode = "display";

		// go back if you can't write the data (avoid data loss)
		if (($restricted) || ($error!=0))
			$mode="edit";
	}
	
	// cancel a page edit
	if ($mode=="cancel")
	{
		$mode = "display";
	}
	return $contents;
}

// generate our html header
function htmlHeader( $title, $config )
{
	$origTitle = $title;
	if ($title=="HomePage") 
		$title = $config['GENERAL']['HOMEPAGE'];  
	echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n");
	echo("<HTML>\n");
	echo("<HEAD>\n");
	echo("\t<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=ISO-8859-1\">\n");
	css($origTitle);
	echo("\t<TITLE>");  
	if ($config['GENERAL']['TITLE']==$title)
		echo($config['GENERAL']['TITLE']);
	else
		echo($config['GENERAL']['TITLE']." :: ".$title);	
	echo("</TITLE>\n");
	echo("</HEAD>\n");
	echo("<BODY>\n");

	// any errors?
	foreach ($config['INTERNAL']['ERRORS'] as $err)
	echo( "<P CLASS=\"error\">ERROR: ".$err."</P>" );

	echo("\t<TABLE WIDTH=\"100%\">\n");
	echo("\t\t<TR>\n");
	echo("\t\t\t<TD ALIGN=\"left\"><SPAN CLASS=\"wiki_header\">".$title."</SPAN></TD>\n");
	echo("\t\t\t<TD ALIGN=\"right\">".wikiparse( $config['LOCALE']['HOMEPAGE_LINK']." ".$config['LOCALE']['PAGELIST_LINK'] ) );
	echo( "</TD>\n");
	echo("\t\t</TR>\n");
	echo("\t</TABLE>\n");
}

// generate our html footer
function htmlFooter()
{
	echo("\t</BODY>\n</HTML>\n");
}

// the start of our wiki body
function htmlStartBlock()
{
	echo("\t<HR>\n");
	echo("\t<TABLE WIDTH=\"100%\" CLASS=\"wiki_body_container\">\n");
	echo("\t\t<TR>\n");
	echo("\t\t\t<TD>\n");
	echo("\n<!-- PAGE BODY -->\n");
}

// the end of our wiki body
function htmlEndBlock()
{
	echo("<!-- END OF PAGE BODY -->\n\n");
	echo("\t\t\t</TD>\n");
	echo("\t\t</TR>\n");
	echo("\t</TABLE>\n");
	echo("<HR>\n");
}

// link to another wiki page
function wikilink( $title )
{
	global $config;
	if ( pageExists( $title ) )
		return ("<A HREF=\"".$_SERVER['PHP_SELF']."?page=".$title."\">".$title."</A>");
	elseif ( $config['SYNTAX']['AUTOLINKING'] )
		return ($title."<A HREF=\"".$_SERVER['PHP_SELF']."?page=".$title."\">?</A>");
	else
		return ($title);
}

// link to another web page
function webpagelink( $text )
{
	global $config;
	$results = explode( "|", $text );
	$size=count($results);
	if ($size==0)
		return $text;		
		
	// page link
	$src=$results[0];

	// link text
	$desc="";
	if ($size>1)
		$desc = $results[1];
	else
		$desc = $src;	
	// is our text an image?
	$patterns = "/{{([^{]*)}}/";
	$replacements = "\".image( \"$1\" ).\"";	
	$cmd = (" \$desc = \"".preg_replace( $patterns, $replacements, $desc )."\";");
	eval($cmd);			

	// link target	
	$window="";                
	if ($size>2)
		$window = $results[2];
	else
	if ( $config['MISC']['EXTERNALLINKS_NEWWINDOW'] )
		$window = "_blank";
	else
		$window = "_self";
		
	// see whether it is a Wiki Link or not
	$prefix = explode( "/", $src );
	if ((count($prefix)==1)) // looks like a local file, an anchor link or a wikipage
	{
		if (pageExists($src)) // is it a wiki page
		{
			$src = $_SERVER['PHP_SELF']."?page=".$src;
			$window = "_self";
			$resultstr = "<A HREF=\"".$src."\" target=\"$window\">".$desc."</A>";
		}
		if ($src[0]=="#") // maybe its an anchor link
		{
			$window = "_self";
			$resultstr = "<A HREF=\"".$src."\" target=\"$window\">".$desc."</A>";
		}
		elseif ($config['SYNTAX']['AUTOLINKING']) // maybe autolink
		{
			$resultstr = ($src."<A HREF=\"".$_SERVER['PHP_SELF']."?page=".$src."\" target=\"$window\">?</A>");
		}
		else
			$resultstr = $desc;
	}
	else
	{		
		$resultstr = "<A HREF=\"".$src."\" target=\"$window\">".$desc."</A>";			
	}
	return verbatim( $resultstr );
}

// evaluate a chunk of text
function wikiEval( $str )
{	
	$result = "";
	$cmd = (" \$result = \"".$str."\";" );
	eval($cmd);
	return $result;
}

// colour some text
function colouredtext( $text )
{
	$results = explode( ":", $text );
	$size=count($results);
	if ($size<2)
		return $text;		
	$colour=$results[0];
	$contents = wikiEval(implode(":", array_slice( $results, 1)));
	$resultstr = "<SPAN STYLE=\"color: #".$colour.";\">".$contents."</SPAN>";
	return verbatim( $resultstr );
}

// place an image
function image( $text )
{	
	$results = explode( "|", $text );
	$size=count($results);	
	$src="";
	$desc="";
	$align="";
	$valign="";
	if ($size>=1)
		$src = " SRC=\"".$results[0]."\"";
	if ($size>=2)
		$desc = " ALT=\"".$results[1]."\"";
	else
		$desc = " ALT=\"[img]\"";
	if ($size>=3)
		$desc .= " WIDTH=\"".$results[2]."\"";
	if ($size>=4)
		$desc .= " HEIGHT=\"".$results[3]."\"";
	if ($size>=5)
		$align = "align:".$results[4].";";
	if ($size>=6)
		$valign=" vertical-align:".$results[5].";";	
	$resultstr="";
	if ($size>0)
		$resultstr = "<IMG".$src." STYLE=\"border:0pt none;".$width.$height.$align.$valign."\"".$desc.">";
	return verbatim( $resultstr );
}

// get some verbatim text
function getVerbatim( $index )
{
	global $config;
	$verbat = &$config['INTERNAL']['VERBATIM'];
	return $verbat[$index];
}

// store some verbatim text
function verbatim( $contents )
{
	global $config;
	$verbat = &$config['INTERNAL']['VERBATIM'];
	$index = count($verbat);
	$verbat[$index] = $contents;
	return "\".getVerbatim(".$index.").\"";
}

// replace special chars with the appropriate html
function htmltag( $contents )
{
	// ' must be used for fields
	$result = str_replace ("&lt;", "<", $contents);
	$result = str_replace ("&gt;", ">", $result);
	$result = str_replace ("&quot;", "\\\"", $result);
	return $result;
}

// parse wiki code & replace with html
function wikiparse( $contents )
{
	global $config;
	$contents = htmlspecialchars($contents, ENT_COMPAT, "ISO8859-1");

	// verbatim text
	$patterns[0] = "/~~~(.*)~~~/";
	$replacements[0] = "\".verbatim( \"$1\" ).\"";	

	// webpage links
	$patterns[1] = "/\[\[([^\[]*)\]\]/";
	$replacements[1] = "\".webpagelink( \"$1\" ).\"";		

	// images
	$patterns[2] = "/{{([^{]*)}}/";
	$replacements[2] = "\".image( \"$1\" ).\"";	

	// coloured text
	$patterns[3] = "/~~#([^~]*)~~/";
	$replacements[3] = "\".colouredtext( \"$1\" ).\"";	
	
	if ( $config['SYNTAX']['HTMLCODE'] )
	{
		$patterns[4] = "/%%(.*)%%/";
		$replacements[4] = "\".htmltag( \"$1\" ).\"";		
	}

	// substitute complex expressions
	$contents = wikiEval( preg_replace( $patterns, $replacements, $contents ) );

	// bold
	$patterns[0] = "/\*\*([^\*]*[^\*]*)\*\*/";
	$replacements[0] = "<B>$1</B>";

	// italic
	$patterns[1] = "/''([^']*[^']*)''/";
	$replacements[1] = "<I>$1</I>";

	// underline
	$patterns[2] = "/__([^_]*[^_]*)__/";
	$replacements[2] = "<SPAN STYLE=\\\"text-decoration: underline;\\\">$1</SPAN>";	

	// html shortcuts
	$patterns[3] = "/@@([^@]*)@@/";
	$replacements[3] = "<A NAME=\\\"$1\\\"></A>";

	// wiki words	
	if ( $config['SYNTAX']['AUTOLINKING'] )
	{
		$patterns[4] = "/([A-Z][a-z0-9]+[A-Z][A-Za-z0-9]+)/";
		$replacements[4] = "\".wikilink( \"$1\" ).\"";	
	}

	// substitute simple expressions & final expansion
	$contents = wikiEval( preg_replace( $patterns, $replacements, $contents ) );

	// replace some whitespace bits & bobs  
	$contents = str_replace( "\t", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $contents );
	$contents = str_replace( "  ", "&nbsp;&nbsp;", $contents );
	$contents = nl2br($contents);

	return $contents;
}

// returns the directory where the wiki pages are stored
function pageDir()
{
	global $config;
	return ($config['GENERAL']['PAGES_DIRECTORY']);
}

// returns the directory where the temporary backups are stored
function tempDir()
{
	global $config;
	return ($config['GENERAL']['TEMP_DIRECTORY']);
}

// returns the full path to a page
function pagePath( $title )
{
	return (pageDir().$title);
}

// clean up the temp directory
function cleanupTempFiles()
{
	$files = pawfalikiReadDir(tempDir());
	foreach( $files as $file )
	{
		$mtime = filemtime( $file );
		$now = date("U");
		if ( $now-$mtime>300 ) // delete any files that are older than 5 minutes
			unlink( $file ); 
	}
}

// is this page 'special'?
function isSpecial( $title )
{
	global $config;
	return ( isset( $config['SPECIAL'][$title] ) );
}

// is this page 'locked'?
function isLocked( $title )
{
	global $config;
	return ( isset( $config['RESTRICTED'][$title] ) );
}

// print the appropriate license
function printLicense( $title )
{
	global $config;
	$license_func = $config['LICENSE']['DEFAULT'];
	if ( isset( $config['LICENSE'][$title] ))
		$license_func = $config['LICENSE'][$title];
	eval( $license_func."();" );
}

// add an error to our error buffer
function error( $string )
{
	global $config;
	$config['INTERNAL']['ERRORS'][] = $string;
}

// are there any errors so far?
function anyErrors()
{
	global $config;
	if (count($config['INTERNAL']['ERRORS'])==0)
		return false;
	else
		return true;
}

// is this ip address blocked?
function isIpBlocked( )
{
	global $config;
	$result = false;
	$ipaddress = $_SERVER['REMOTE_ADDR'];  
	foreach ($config['BLOCKED_IPS'] as $ip)
	{
		if (preg_match( "/".$ip."/", $ipaddress ))
		{
			error( "Your ip address has been blocked from making changes!" );
			$result = true;
			break;
		}	
	}
	return $result;
}

// does a given page exist yet?
function pageExists( $title )
{
	if (file_exists( pagePath( $title ) ) || isSpecial( $title ) )
		return true;
	else
		return false;
}

// returns a list of pages
function pageList()
{
	global $config;
	$contents = "";
	$files = pawfalikiReadDir(pageDir());
	$details = array();
	foreach ($files as $file)
		$details[$file] = filemtime( $file );
	arsort($details);
	reset($details);
	while ( list($key, $val) = each($details) )  	
		$contents .= "[[".basename($key)."]] ".date($config['GENERAL']['MODTIME_FORMAT'], $val )."\n";
	return $contents;
}

// returns the pageList in RSS2.0 format
function rssFeed()
{
	global $config;
	echo( "<?xml version=\"1.0\"?>\n" );
	echo( "<rss version=\"2.0\">\n" );
	echo( "\t<channel>\n" );
	$title = $config['GENERAL']['TITLE'];
	echo( "\t\t<title>$title</title>\n" );
	$url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
	echo( "\t\t<link>$url</link>\n" );
	echo( "\t\t<description>Recently changed pages on the $title wiki.</description>\n" );
	echo( "\t\t<generator>Pawfaliki v".$config['PAWFALIKI_VERSION']."</generator>\n" );	
	$files = pawfalikiReadDir(pageDir());
	$details = array();
	foreach ($files as $file)
		$details[$file] = filemtime( $file );
	arsort($details);
	reset($details);
	$item = 0;
	$numItems = $config['RSS']['ITEMS'];
	while ( list($key, $val) = each($details) )  	
	{
		$title = basename($key);
		$modtime = date( $config['RSS']['MODTIME_FORMAT'], $val );
		$description = $title." ".$modtime;
		if ($config['RSS']['TITLE_MODTIME'])
			$title = $description;			
		echo( "\t\t<item>\n" );
		echo( "\t\t\t<title>$title</title>\n" );
		echo( "\t\t\t<link>$url?page=$title</link>\n" );	
		echo( "\t\t\t<description>$description</description>\n" );
		echo( "\t\t</item>\n" );	
		$item++;
		if ($numItems!=-1&&$item>=$numItems)
			break;
	}
	echo( "\t</channel>\n" );
	echo( "</rss>\n" );
}

// backup all the wiki pages to a file
function backupPages( &$filename )
{	
	global $config;
	$files = pawfalikiReadDir(pageDir());
	$details = array();
	foreach ($files as $file)
		$details[$file] = filemtime( $file );
	arsort($details);
	reset($details);	
	$pages = array();
	$pos = 0;
	while ( list($key, $val) = each($details) )  	
	{
		$pages[$pos] = array();
		$pages[$pos]['title'] = basename($key);
		$pages[$pos]['datestring'] = date("U", $val );
		$pos = $pos+1;
	}	
	$numpages = count($pages);
	if ($numpages==0) // must have at least 1 page for a backup
	{
		error("No pages to backup yet!");
		return;
	}
	if ( extension_loaded('zlib')&&$config['BACKUP']['USE_ZLIB'] ) // write a gzipped backup file
	{
		$filename = $filename.".gz";
		$zp = gzopen($filename, "w9");
		gzwrite($zp, $numpages."\n");
		foreach( $pages as $page )
		{
			$contents = $page['title']."\n".$page['datestring']."\n";
			$lines = rtrim( pawfalikiReadFile( pagePath( $page['title'] ) ) );
			$numlines = count( explode("\n", $lines) );
			if ($numlines==0) // no lines?! weird - we must have at least 1 line for restore
			{
				$numlines=1;
				$lines.="\n";
			}
			$contents .= "$numlines\n$lines\n";
			gzwrite( $zp, $contents );
		}
		gzclose($zp);	
	}
	else // otherwise normal binary file
	{
		$fd = fopen( $filename, "wb" );
		fwrite( $fd, $numpages."\n" );
		foreach( $pages as $page )
		{
			$contents = $page['title']."\n".$page['datestring']."\n";
			$lines = rtrim( pawfalikiReadFile( pagePath( $page['title'] ) ) );
			$numlines = count( explode("\n", $lines) );
			if ($numlines==0) // no lines?! weird - we must have at least 1 line for restore
			{
				$numlines=1;
				$lines.="\n";
			}
			$contents .= "$numlines\n$lines\n";
			fwrite( $fd, $contents );
		}
		fclose( $fd );	
	}
	return 0;
}

// restore all the wiki pages from a file
function restorePages()
{
	global $config, $_FILES;
	unset($config['INTERNAL']['DATA']['RESTORED']);
	if (!authPassword("RestoreWiki", $_POST['password']))
	{
		error("Wrong password. Try again.");
		return;
	}
	
	$filename = $_FILES['userfile']['tmp_name'];
	if ($filename=="none"||$_FILES['userfile']['size']==0||!is_uploaded_file($filename))
	{
		error( "No file was uploaded!<BR>Maybe the filesize exceeded the maximum upload size of ".$config['BACKUP']['MAX_SIZE']."bytes." );
		return;
	}
	
	// if we can use zlib functions - they can read uncompressed files as well
	$zlib = false;
	if ( extension_loaded('zlib')&&$config['BACKUP']['USE_ZLIB'] ) $zlib = true;

	// sanity check on file
	if ($zlib)
		$fd = gzopen($filename, "rb9");
	else
		$fd = fopen($filename, "rb");
	if (!$fd)
	{
		error("Could not read temporary upload file: $filename!");
		return;
	}				
	$fileerror = "NO ERROR";
	if ($zlib)
		$numPages = trim(gzgets($fd));
	else
		$numPages = trim(fgets($fd));
	if ($numPages>0) // must be at least 1 page
	{
		for ($i=0; $i<$numPages; $i++)
		{
			if ($zlib)
			{
				@gzgets($fd); if (gzeof($fd)) {$fileerror="GZ: Invalid title on page $i!";} // read title
				@gzgets($fd); if (gzeof($fd)) {$fileerror="GZ: Invalid mod time on page $i!";} // mod time
				$numLines = trim(gzgets($fd)); // num lines
			}
			else
			{
				@fgets($fd); if (feof($fd)) {$fileerror="Invalid title on page $i!";} // read title
				@fgets($fd); if (feof($fd)) {$fileerror="Invalid mod time on page $i!";} // mod time
				$numLines = trim(fgets($fd)); // num lines
			}
						
			if ($numLines>0) // must have at least 1 line
			{
				for ($j=0; $j<$numLines; $j++)
				{
					if ($zlib)
					{
						@gzgets($fd); if (gzeof($fd)&&$i!=$numPages-1) {$fileerror="GZ: Invalid line read on page $i!";} // page content
					}
					else
					{
						@fgets($fd); if (feof($fd)&&$i!=$numPages-1) {$fileerror="Invalid line read on page $i!";} // page content
					}
				}
			}
			else
			{
				$fileerror = "Invalid number of page lines on page $i!";
			}
		}
	}
	else
	{
		$fileerror = "Invalid number of backup pages!";
	}
	if ($zlib)
		gzclose($fd);
	else
		fclose($fd);		
	if ($fileerror!="NO ERROR")
	{
		$str = "This does not appear to be a valid backup file!";
		if(!$zlib)
			$str .= "<BR>NOTE: Zlib is not enabled so restoring a compressed file will not work.";
		error($str);
		return;
	}		
	
	// if we got here the file is OK - restore the pages!!
	$restored = &$config['INTERNAL']['DATA']['RESTORED'];
	$restored = array();		
	if ($zlib)
		$fd = gzopen($filename, "rb9");
	else
		$fd = fopen($filename, "rb");
	if ($zlib)
		$numPages = trim(gzgets($fd));
	else
		$numPages = trim(fgets($fd));
	for ($i=0; $i<$numPages; $i++)
	{
		if ($zlib)
		{
			$title = trim(gzgets($fd));
			$modtime = trim(gzgets($fd));
			$numLines = trim(gzgets($fd));
			$contents = "";
			for ($j=0; $j<$numLines; $j++)
				$contents .= gzgets($fd);		
		}
		else
		{
			$title = trim(fgets($fd));
			$modtime = trim(fgets($fd));
			$numLines = trim(fgets($fd));
			$contents = "";
			for ($j=0; $j<$numLines; $j++)
				$contents .= fgets($fd);
		}
		if (!writeFile($title, $contents))
		{
			if (@touch(pagePath( $title ), $modtime, $modtime)==false)
			{
				error("Could not modify filetimes for $title - ensure php owns the files!");
			}
			$restored[] = $title;
		}
	}
	if ($zlib)
		gzclose($fd);
	else
		fclose($fd);	
}

// print a little wiki syntax box
function printWikiSyntax()
{
	global $config;
	echo("\t<TABLE CLASS=\"wikisyntax\">\n");
	echo("\t\t<TR>\n");
	echo("\t\t\t<TD COLSPAN=3>");
	echo( wikiparse("**__Syntax__** ~~#0000FF:(optional values)~~\n") );
	echo("\t\t\t</TD>\n");
	echo("\t\t</TR>\n");
	echo("\t\t<TR>\n");
	echo("\t\t\t<TD ALIGN=\"right\">");
	echo( "bold text: <BR>" );
	echo( "italic text: <BR>" );
	echo( "underlined text: <BR>" );
	echo( "verbatim text: <BR>" );
	echo( "link: <BR>" );
	if ( $config['SYNTAX']['AUTOLINKING'] )
		echo( "wiki link: <BR>" );
	echo( "image: <BR>" );
	echo( "hex-coloured text: <BR>" );
	if ( $config['SYNTAX']['HTMLCODE'] )
		echo( "html code: <BR>" );
	echo( "anchor link: <BR>" );
	echo("\t\t\t</TD>\n");
	echo("\t\t\t<TD>");
	echo( "**abc**<BR>" );
	echo( "''abc''<BR>" );
	echo( "__abc__<BR>" );
	echo( "~~~abc~~~<BR>" );
	echo( "[[url|".wikiparse("~~#0000FF:description~~")."|".wikiparse("~~#0000FF:target~~")."]]<BR>" );
	if ( $config['SYNTAX']['AUTOLINKING'] )
		echo( "WikiWord<BR>" );
	echo( "{{url|".wikiparse("~~#0000FF:alt~~")."|".wikiparse("~~#0000FF:width~~")."|".wikiparse("~~#0000FF:height~~") );
	echo( "|".wikiparse("~~#0000FF:align~~")."|".wikiparse("~~#0000FF:vertical-align~~")."}}<BR>" );
	echo( "~~#AAAAAA:grey~~<BR>" );
	if ( $config['SYNTAX']['HTMLCODE'] )
		echo( "%%html code%%<BR>" );
	echo( "@@name@@<BR>" );
	echo("\t\t\t</TD>\n");
	echo("\t\t</TR>\n");
	echo("\t</TABLE>\n");
}

// display a wiki page
function displayPage( $title, &$mode, $contents="" )
{ 	
	global $config;

	// handle special pages 
	switch ($title)
	{
		case "PageList":
			$contents = pageList();
			break;
			
		case "RestoreWiki":		
			if ( !isset($config['INTERNAL']['DATA']['RESTORED']) )
			{			
				$contents .= "<B>WARNING: Restoring wiki pages will overwrite any existing pages with the same name!</B><BR><BR>" ;
				$contents .= "Backup File: ";    
				$contents .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".$config['BACKUP']['MAX_SIZE']."\"><BR>";
				$contents .= "<input name=\"userfile\" type=\"file\" class=\"fileupload\" size=\"32\"><BR><BR>";
				$contents .= "Enter the password below & click <b>restore</b>.";
			}
			else
			{
				$contents = wikiparse("Restored **".count($config['INTERNAL']['DATA']['RESTORED'])."** wiki pages:\n");
				foreach($config['INTERNAL']['DATA']['RESTORED'] as $page)
				{
					$contents .= wikiparse("-> [[$page]]\n");
				}
			}
			break;
			
		case "BackupWiki":
			if (!anyErrors())
			{
				$wikiname = str_replace( " ", "_", $config['GENERAL']['TITLE'] );
				$files = pawfalikiReadDir(pageDir());
				$backups = pawfalikiReadDir(tempDir());
				$contents = "Backed up ".count($files)." pages.\n\nRight-click on the link below and \"Save Link to Disk...\".\n";
			}
			break;
			
		default:
			if ( pageExists( $title ) )
			{
				if (!( ($mode=="edit") && ($contents!="") ))
					$contents = pawfalikiReadFile( pagePath( $title ) );
			}
			else
			{
				$contents = "This is the page for ".$title."!";
				$mode = "editnew";
			}
			break;
	}
	
	switch ($mode)
	{
		case "display":
			echo("<SPAN CLASS=\"wiki_body\">\n");
			echo( wikiparse( $contents ) );
			echo("</SPAN>\n");
			break;		
		case "backup":
			echo("<SPAN CLASS=\"wiki_body\">\n");
			echo( wikiparse( $contents ) );
			echo("</SPAN>\n");
			break;		
		case "restore": 
			if (!isset($config['INTERNAL']['DATA']['RESTORED']))
				echo( "<FORM enctype=\"multipart/form-data\"  ACTION=\"".$_SERVER['PHP_SELF']."?page=".$title."\" METHOD=\"post\">\n" );
			echo("<SPAN CLASS=\"wiki_body\">\n");
			echo( $contents );
			echo("</SPAN>\n");
			break;			
		case "edit": case "editnew":
			echo( "<FORM ACTION=\"".$_SERVER['PHP_SELF']."?page=".$title."\" METHOD=\"post\">\n" );
			echo( "<TEXTAREA NAME=\"contents\" COLS=\"80\" ROWS=\"24\">".$contents."</TEXTAREA>\n" );	
			break;
	}    	
}

// display the wiki controls
function displayControls( $title, &$mode )
{
	global $config;
	echo("\t<TABLE WIDTH=\"100%\">\n");
	echo("\t\t<TR>\n");
	echo("\t\t\t<TD ALIGN=\"left\">\n");
	switch ($mode)
	{
		case "display":
			if (!(isSpecial($title)))
			{
				echo( "\t\t\t\t<FORM ACTION=\"".$_SERVER['PHP_SELF']."?page=".$title."\" METHOD=\"post\">\n" );
				echo( "\t\t\t\t\t<P>\n" );
				echo( "\t\t\t\t\t\t<INPUT NAME=\"mode\" VALUE=\"edit\" TYPE=\"SUBMIT\">" );
				if (isLocked($title))
					echo( wikiparse($config['LOCALE']['REQ_PASSWORD']));				
				echo( "\n\t\t\t\t\t</P>\n" );
				echo( "\t\t\t\t</FORM>\n" );
			}
			if ($title=="PageList"&&$config['BACKUP']['ENABLE'])
			{				
				echo( wikiparse( " ".$config['LOCALE']['BACKUP_LINK']." ".$config['LOCALE']['RESTORE_LINK'] ) );
			}
			break;
		case "backup":
			if (!anyErrors())
			{
				$wikiname = str_replace( " ", "_", $config['GENERAL']['TITLE'] );
				$files = pawfalikiReadDir(pageDir());
				$backups = pawfalikiReadDir(tempDir());
				$details = array();
				foreach ($backups as $backup)
					$details[$backup] = filemtime( $backup );
				arsort($details);
				reset($details);	
				while ( list($key, $val) = each($details) )  	
				{
					$size = filesize($key);
					echo( wikiparse("[[$key|".basename($key)."]] ($size bytes)"));
				}
			}
			break;
		case "restore":
			if ( !isset($config['INTERNAL']['DATA']['RESTORED']) )
			{
				echo( "\t\t\t\t\t<P>\n" );
				echo(wikiparse(" ".$config['LOCALE']['PASSWORD_TEXT'])); 
				echo("<input name=\"password\" type=\"password\" class=\"pass\" size=\"17\">");
				echo( "\t\t\t\t\t<INPUT NAME=\"mode\" VALUE=\"restore\" TYPE=\"SUBMIT\">\n" );		
				echo( "\t\t\t\t\t</P>\n" );
				echo( "\t\t\t\t</FORM>\n" );
			}
			break;
		case "edit":
			echo( "\t\t\t\t\t<P>\n" );
			if (isLocked($title))
			{
				echo(wikiparse($config['LOCALE']['PASSWORD_TEXT'])); 
				echo("<input name=\"password\" type=\"password\" class=\"pass\" size=\"17\">");
			}
			echo( "\t\t\t\t\t<INPUT NAME=\"mode\" VALUE=\"save\" TYPE=\"SUBMIT\">\n" );
			echo( "\t\t\t\t\t<INPUT NAME=\"mode\" VALUE=\"cancel\" TYPE=\"SUBMIT\">\n" );
			echo( "\t\t\t\t\t</P>\n" );
			echo( "\t\t\t\t</FORM>\n" );
			break;
		case "editnew":
			echo( "\t\t\t\t\t<P>\n" );
			if (isLocked($title))
			{
				echo(wikiparse($config['LOCALE']['PASSWORD_TEXT'])); 
				echo("<input name=\"password\" type=\"password\" class=\"pass\" size=\"17\">");
			}
			echo( "\t\t\t\t\t\t<INPUT NAME=\"mode\" VALUE=\"save\" TYPE=\"SUBMIT\">" );
			echo( "\t\t\t\t\t</P>\n" );
			echo( "\t\t\t\t</FORM>\n" );
			break;
	}
	echo("\t\t\t</TD>\n");
	echo("\t\t\t<TD ALIGN=\"right\">\n");
	echo("\t\t\t\t<P STYLE=\"margin: 0px;\">\n");
	printLicense( $title );
	echo("\t\t\t\t</P>\n");
	echo("\t\t\t</TD>\n");
	echo("\t\t</TR>\n");
	echo("\t</TABLE>\n");
	if ( ($mode=="edit"||$mode=="editnew")&&$config['SYNTAX']['SHOW_BOX']&&$title!="RestoreWiki" )
		printWikiSyntax();
}

//==============
//==============
// MAIN BLOCK!
//==============
//==============

// by defining $LIBFUNCTIONSONLY and including this file we can use all
// the wiki functions without actually displaying a wiki.
if (!isset($LIBFUNCTIONSONLY))
{
	// stop the page from being cached
	header("Cache-Control: no-store, no-cache, must-revalidate");

	// find out what wiki 'mode' we're in
	$mode = getMode();

	$format = $_GET['format'];
	if ( $format=="rss"&&$config['RSS']['ENABLE'] )
	{
		rssFeed();
	}
	else
	{
		// get the page title
		$title = getTitle();

		// get the page contents
		$contents = updateWiki( $mode, $title, $config );

		// page header
		if ($mode=="edit") 
			htmlHeader(wikiparse($config['LOCALE']['EDIT_TITLE']).$title, $config); 
		else
			htmlHeader($title, $config); 

		// page contents
		htmlStartBlock();
		displayPage($title, $mode, $contents);
		htmlEndBlock();

		// page controls
		displayControls($title, $mode);

		// page footer
		htmlFooter();
	}
}
?>
