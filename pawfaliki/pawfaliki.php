<?php

/*  Pawfaliki
 *  Copyright (C) 2004 Dan Bethell <dan at pawfal dot org>
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

// setup some storage
$config = array();
$config['VERBATIM'] = array();
$config['LOCKED'] = array();
$config['BLOCKED_IPS'] = array();
$config['ERRORS'] = array();
$config['SPECIAL'] = array();
$config['SPECIAL']['PageList'] = 1;

//===========================================================================
//===========================================================================
// CONFIG:
// This section contains variables to configure various aspects of the wiki
$config['TITLE'] = "Pawfal"; // Call the wiki
$config['HOMEPAGE'] = "Pawfal"; // Call the homepage
$config['ADMIN'] = "webmaster at pawfal dot org"; // printed on error messages
$config['CSS'] = "Pawfal:pawfal.css"; // title:filename
$config['LOCKED']['HomePage'] = 1; // lock the homepage
$config['LOCKED']['PawfalIki'] = 1;
/*
$config['BLOCKED_IPS'][] = "192.168.0.*"; // block this ip address (can take wildcards)
*/
//===========================================================================
//===========================================================================

// our licensing information
function license()
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

// initialise our style sheets
function css()
{
	global $config;
	if (isset( $config['CSS'] ) )
  {
  	$tokens = explode(":", $config['CSS'] );
    $title = $tokens[0];
		$path = implode(":", array_slice( $tokens, 1));
    echo( "\t<LINK REL=\"stylesheet\" TYPE=\"text/css\" HREF=\"".$path."\" TITLE=\"".$title."\">\n");
  }
}

// writes a file to disk
function writeFile( $title, $contents )
{
	$fd = fopen( pagePath( $title ), "w" );
	fwrite( $fd, $contents );
	fclose( $fd );	
}

// reads the contents of a file into a string (php<4.3.0 friendly)
function read_file( $filename )
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
function read_dir($path)
{
	$results = array();
	if ($handle = opendir($path)) 
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
	$dir = (dirname($_SERVER['SCRIPT_FILENAME'])."/Pages");
	$contents = "Hello and welcome to Pawfaliki!";	
	writeFile( $title, $contents );
}

// get the title of a page
function getTitle( $config )
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

// get the current wiki 'mode' (display or edit)
function getMode( $config )
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

// update the wiki (someone clicked save)
function updateWiki( &$mode, $title, $config )
{
	$result = "";
	if ( $mode=="save" )
	{
		if ( isset($_POST['contents']) )
    {
    	$oldcontents = stripslashes( $_POST['oldcontents'] );
    	$contents = stripslashes( $_POST['contents'] );
      
      // write file    
			if (!isIpBlocked())
  		{  
      	writeFile( $title, $contents );
      }
    }
		$mode = "display";
	}
	if ($mode=="cancel")
	{
		$mode = "display";
	}
  return $result;
}

// generate our html header
function htmlheader( $title, $config )
{
	if ($title=="HomePage") 
  	$title = $config["HOMEPAGE"];  
  echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n");
	echo("<HTML>\n");
  echo("<HEAD>\n");
	echo("\t<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=ISO-8859-1\">\n");
  css();
	echo("\t<TITLE>");  
	if ($config['TITLE']==$title)
		echo($config["TITLE"]);
	else
		echo($config["TITLE"]." :: ".$title);	
  echo("</TITLE>\n");
  echo("</HEAD>\n");
	echo("<BODY>\n");

	// any errors?
	foreach ($config['ERRORS'] as $err)
		echo( "<P CLASS=\"error\">".$err."</P>" );

  echo("\t<TABLE WIDTH=\"100%\">\n");
  echo("\t\t<TR>\n");
  echo("\t\t\t<TD ALIGN=\"left\"><SPAN CLASS=\"wiki_header\">".$title."</SPAN></TD>\n");
  echo("\t\t\t<TD ALIGN=\"right\">".wikiparse( "HomePage PageList" )."</TD>\n");
  echo("\t\t</TR>\n");
  echo("\t</TABLE>\n");
}

// generate our html footer
function htmlfooter()
{
	echo("\t</BODY>\n</HTML>\n");
}

// the start of our wiki body
function htmlstartblock()
{
  echo("\t<HR>\n");
  echo("\t<TABLE WIDTH=\"100%\" CLASS=\"wiki_body_container\">\n");
  echo("\t\t<TR>\n");
  echo("\t\t\t<TD>\n");
	echo("\n<!-- PAGE BODY -->\n");
}

// the end of our wiki body
function htmlendblock()
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
	elseif ( !isset($config['LOCKED']['ALL']) )
		return ($title."<A HREF=\"".$_SERVER['PHP_SELF']."?page=".$title."\">?</A>");
  else
  	return ($title);
}

// link to an external web page
function externallink( $text )
{
	$results = explode( "|", $text );
	$size=count($results);
	if ($size==0)
		return $text;		
	$src=$results[0];
	$desc="";
	if ($size>1)
		$desc = $results[1];
	else
		$desc = $src;		
	$resultstr = "<A HREF=\"".$src."\">".$desc."</A>";		
	return verbatim( $resultstr );
}

// colour some text
function colouredtext( $text )
{
	$results = explode( ":", $text );
	$size=count($results);
	if ($size<2)
		return $text;		
	$colour=$results[0];
	$contents = implode(":", array_slice( $results, 1));			
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
	$h="";
	$w="";
	$al="";
	$val="";	
	if ($size>=1)
		$src = " SRC=\"".$results[0]."\"";
	if ($size>=2)
		$desc = " ALT=\"".$results[1]."\"";
	if ($size>=3)
		$h = " HEIGHT=\"".$results[2]."\"";
	if ($size>=4)
		$w = " WIDTH=\"".$results[3]."\"";
	if ($size>=5)
		$al = " ALIGN=\"".$results[4]."\"";
	if ($size>=6)
		$val=" VALIGN=\"".$results[5]."\"";		
	$resultstr="";
	if ($size>0)
		$resultstr = "<IMG".$src.$desc.$h.$w.$al.$val.">";		
	return verbatim( $resultstr );
}

// get some verbatim text
function getVerbatim( $index )
{
	global $config;
	$verbat = &$config['VERBATIM'];
	return $verbat[$index];
}

// store some verbatim text
function verbatim( $contents )
{
	global $config;
	$verbat = &$config['VERBATIM'];
	$index = count($verbat);
	$verbat[$index] = $contents;
	return "\".getVerbatim(".$index.").\"";
}

// parse wiki code & replace with html
function wikiparse( $contents )
{
	global $config;
	$contents = htmlspecialchars($contents, ENT_COMPAT, "ISO8859-1");
			
	// verbatim text
	$patterns[0] = "/~~~(.*)~~~/";
	$replacements[0] = "\".verbatim( \"$1\" ).\"";	

	// external links
	$patterns[1] = "/\[\[([^\[]*)\]\]/";
	$replacements[1] = "\".externallink( \"$1\" ).\"";		

	// images
	$patterns[2] = "/{{([^{]*)}}/";
	$replacements[2] = "\".image( \"$1\" ).\"";	

	// coloured text
	$patterns[3] = "/~~#([^~]*)~~/";
	$replacements[3] = "\".colouredtext( \"$1\" ).\"";	
	
	// substitute complex expressions
	$cmd = (" \$contents = \"".preg_replace( $patterns, $replacements, $contents )."\";");
	eval($cmd);	
					
	// bold
	$patterns[0] = "/\*\*([^\*]*[^\*]*)\*\*/";
	$replacements[0] = "<B>$1</B>";
	
	// italic
	$patterns[1] = "/''([^']*[^']*)''/";
	$replacements[1] = "<I>$1</I>";
	
	// underline
	$patterns[2] = "/__([^_]*[^_]*)__/";
	$replacements[2] = "<SPAN STYLE=\\\"text-decoration: underline;\\\">$1</SPAN>";	
	
	// wiki words
	$patterns[3] = "/([A-Z][a-z0-9]+[A-Z][A-Za-z0-9]+)/";
	$replacements[3] = "\".wikilink( \"$1\" ).\"";	
	
	// substitute simple expressions
	$contents = preg_replace( $patterns, $replacements, $contents );		

	// final expansion
	$cmd = (" \$contents = \"".$contents."\";");
	eval($cmd);	
  
  $contents = str_replace( "\t", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $contents );
  $contents = str_replace( "  ", "&nbsp;&nbsp;", $contents );
  $contents = nl2br($contents);
  
  return $contents;
}

// returns the directory where the wiki pages are stored
function pageDir()
{
	return (dirname($_SERVER['SCRIPT_FILENAME'])."/Pages/");
}

// returns the full path to a page
function pagePath( $title )
{
	return (pageDir().$title);
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
	return ( isset( $config['LOCKED'][$title] )||isset( $config['LOCKED']['ALL'] ) );
}

function error( $string )
{
	global $config;
  $config['ERRORS'][] = $string;
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
      error( "Your ip address has been blocked from making changes by ".$config['ADMIN'] );
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
	$contents = "";
	$files = read_dir(pageDir());
	$details = array();
	foreach ($files as $file)
		$details[$file] = filemtime( $file );
	arsort($details);
	reset($details);
	while ( list($key, $val) = each($details) )
  	$contents .= basename($key)." (".date("D M j G:i:s T Y", $val ).")\n";
	return $contents;
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
			
		default:
			if ( pageExists( $title ) )
			{
				$contents = read_file( pagePath( $title ) );
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
  echo("\t\t\t<TD>\n");
	switch ($mode)
  {
  	case "display":
 			if (!(isSpecial($title)||isLocked($title)))
      {
        echo( "\t\t\t\t<FORM ACTION=\"".$_SERVER['PHP_SELF']."?page=".$title."\" METHOD=\"post\">\n" );
        echo( "\t\t\t\t\t<P>\n" );
        echo( "\t\t\t\t\t\t<INPUT TYPE=\"HIDDEN\" NAME=\"mode\" VALUE=\"edit\">\n" );
        echo( "\t\t\t\t\t\t<INPUT VALUE=\"Edit\" TYPE=\"SUBMIT\">\n" );
        echo( "\t\t\t\t\t</P>\n" );
        echo( "\t\t\t\t</FORM>\n" );
      }
      break;
    case "edit":
      echo( "\t\t\t\t\t<P>\n" );
      echo( "\t\t\t\t\t<INPUT NAME=\"mode\" VALUE=\"save\" TYPE=\"SUBMIT\">\n" );
      echo( "\t\t\t\t\t<INPUT NAME=\"mode\" VALUE=\"cancel\" TYPE=\"SUBMIT\">\n" );
      echo( "\t\t\t\t\t</P>\n" );
      echo( "\t\t\t\t</FORM>\n" );
      break;
    case "editnew":
      echo( "\t\t\t\t\t<P>\n" );
      echo( "\t\t\t\t\t\t<INPUT NAME=\"mode\" VALUE=\"save\" TYPE=\"SUBMIT\">" );
      echo( "\t\t\t\t\t</P>\n" );
      echo( "\t\t\t\t</FORM>\n" );
  		break;
  }
	echo("\t\t\t</TD>\n");
  echo("\t\t\t<TD ALIGN=\"right\">\n");
  echo("\t\t\t\t<P>\n");
  license();
  echo("\t\t\t\t</P>\n");
  echo("\t\t\t</TD>\n");
  echo("\t\t</TR>\n");
  echo("\t</TABLE>\n");
}

//==============
//==============
// MAIN BLOCK!
//==============
//==============

// stop the page from being cached
header("Cache-Control: no-store, no-cache, must-revalidate");

// find out what wiki 'mode' we're in
$mode = getMode( $config );

// get the page title
$title = getTitle( $config );

// get the page contents
$contents = updateWiki( $mode, $title, $config );

// page header
if ($mode=="edit") 
	htmlHeader("Edit: ".$title, $config); 
else
	htmlHeader($title, $config); 
  
// page contents
htmlstartblock();
if ( $contents!="" )
{
	echo( "<SPAN CLASS=\"wiki_body\">\n");
	echo( wikiparse( $contents ) );
	echo( "</SPAN>\n");
}
else
	displayPage($title, $mode, $contents);
htmlendblock();

// page controls
displayControls($title, $mode);

// page footer
htmlFooter();
?>
