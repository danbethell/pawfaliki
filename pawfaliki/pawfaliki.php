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

// setup some storage
$config = array();
$config['VERBATIM'] = array(); // used internally for verbatim text processing
$config['ERRORS'] = array(); // used internally for error handling
$config['SPECIAL'] = array(); // used for handling 'special' non-wiki pages
$config['BLOCKED_IPS'] = array(); // an array of block IP addresses
$config['LOCALE'] = array(); // text for titles, links etc...
$config['USERS'] = array(); // uses and passwords
$config['RESTRICTED'] = array(); // restricted page access
$config['LICENSE'] = array(); // support for different licenses

//==============
//==============
// CONFIGURE!
//==============
//==============

// General configuration
$config['TITLE'] = "My Wiki"; // Call the wiki
$config['HOMEPAGE'] = "HomePage"; // Call the homepage
$config['ADMIN'] = "webmaster at somewhere dot org"; // printed on error messages
$config['CSS'] = "Pawfaliki:pawfaliki.css"; // title:filename
$config['PAGES_DIRECTORY'] = "./Pages/"; // The paths of the stored wiki pages
$config['DISABLE_AUTOLINKING'] = false; // Disables auto-generation of WikiLinks
$config['ALLOW_HTMLCODE'] = false; // Allows posting of raw html using %% tags
$config['SHOW_WIKISYNTAX'] = true; // display the wiki syntax box on edit page
$config['EXTERNALLINKS_NEWWINDOW'] = false; // open external links in a new window

// special pages - unmodifiable
$config['SPECIAL']['PageList'] = 1;

// user's passwords
 $config['USERS']['admin'] = "adminpassword"; // changing this would be a good idea!
// $config['USERS']['user1'] = "user1password";

// give access to some users to edit restricted pages
 $config['RESTRICTED']['HomePage'] = array("admin"); 
// $config['RESTRICTED']['SomePage'] = array("admin", "user1"); 

// pages with special licenses
$config['LICENSE']['DEFAULT'] = "creative_commons_license"; // will call creative_commons_license() function
//$config['LICENSE']['SomePage'] = "my_other_license"; // will call my_other_license() function

// blocked IP addresses
// $config['BLOCKED_IPS'][] = "192.168.0.*"; // block this ip address (can take wildcards)

// text for some titles, icons, etc - you can use wiki syntax in these for images etc...
$config['LOCALE']['EDIT_TITLE'] = "Edit: "; // title prefix for edit pages
$config['LOCALE']['HOMEPAGE_LINK'] = "[[HomePage]]";
$config['LOCALE']['PAGELIST_LINK'] = "[[PageList]]";
$config['LOCALE']['REQ_PASSWORD'] = "(locked)";
$config['LOCALE']['PASSWORD_TEXT'] = "Password:"; // wiki text

//===========================================================================
//===========================================================================

// our licensing information
function creative_commons_license()
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
	fclose( $fd );	
	return 0;	
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
	global $config;
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
				$restricted=true;
				foreach ($config['RESTRICTED'][$title] as $user)
				{
					if ($config['USERS'][$user]==$_POST['password']) 
						$restricted=false;
				}
				if ($restricted)
					error("Wrong password. Try again.");
			}
			
      // write file    
			if (!isIpBlocked() && !$restricted)
      	$error = writeFile( $title, $contents );
    }
		$mode = "display";
		
		// go back if you can't write the data (avoid data loss)
		if (($restricted) || ($error!=0) || isIPBlocked())
		{
			$mode="edit";
		}
	}
	if ($mode=="cancel")
	{
		$mode = "display";
	}
  return $contents;
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
		echo( "<P CLASS=\"error\">ERROR: ".$err."</P>" );

  echo("\t<TABLE WIDTH=\"100%\">\n");
  echo("\t\t<TR>\n");
  echo("\t\t\t<TD ALIGN=\"left\"><SPAN CLASS=\"wiki_header\">".$title."</SPAN></TD>\n");
  echo("\t\t\t<TD ALIGN=\"right\">".wikiparse( $config['LOCALE']['HOMEPAGE_LINK']." ".$config['LOCALE']['PAGELIST_LINK'] )."</TD>\n");
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
	elseif ( !$config['DISABLE_AUTOLINKING'] )
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
		if ( $config['EXTERNALLINKS_NEWWINDOW'] )
			$window = "_blank";
		else
			$window = "_self";
		
	// see whether it is a Wiki Link or not
	$prefix = explode( "/", $src );
	if ((count($prefix)==1))
	{
	  if (pageExists($src))
		{
			$src = $_SERVER['PHP_SELF']."?page=".$src;
			$window = "_self";
			$resultstr = "<A HREF=\"".$src."\" target=\"$window\">".$desc."</A>";
		}
		elseif (!$config['DISABLE_AUTOLINKING'])
		{
			$resultstr = ($src."<A HREF=\"".$_SERVER['PHP_SELF']."?page=".$src."\" target=\"$window\">?</A>");
		}
		else
			$resultstr = $desc;
	}
	else      
		$resultstr = "<A HREF=\"".$src."\" target=\"$window\">".$desc."</A>";			
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
	
	if ( $config['ALLOW_HTMLCODE'] )
	{
		$patterns[4] = "/%%(.*)%%/";
		$replacements[4] = "\".htmltag( \"$1\" ).\"";		
	}
	
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
	
	// html shortcuts
	$patterns[3] = "/@@([^@]*)@@/";
	$replacements[3] = "<A NAME=\\\"$1\\\"></A>";
	
	// wiki words	
	if ( !$config['DISABLE_AUTOLINKING'] )
	{
		$patterns[4] = "/([A-Z][a-z0-9]+[A-Z][A-Za-z0-9]+)/";
		$replacements[4] = "\".wikilink( \"$1\" ).\"";	
	}

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
	global $config;
	return ($config['PAGES_DIRECTORY']);
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

// add an error to our buffer
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
		$contents .= "[[".basename($key)."]] (".date("D M j G:i:s T Y", $val ).")\n";
	return $contents;
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
	echo( "web link: <BR>" );
	if ( !$config['DISABLE_AUTOLINKING'] )
		echo( "wiki link: <BR>" );
	echo( "image: <BR>" );
	echo( "hex-coloured text: <BR>" );
	if ( $config['ALLOW_HTMLCODE'] )
		echo( "html code: <BR>" );
	echo( "anchor link: <BR>" );
	echo("\t\t\t</TD>\n");
  echo("\t\t\t<TD>");
	echo( "**abc**<BR>" );
	echo( "''abc''<BR>" );
	echo( "__abc__<BR>" );
	echo( "~~~abc~~~<BR>" );
	echo( "[[url|".wikiparse("~~#0000FF:description~~")."|".wikiparse("~~#0000FF:target~~")."]]<BR>" );
	if ( !$config['DISABLE_AUTOLINKING'] )
		echo( "SomePage<BR>" );
	echo( "{{url|".wikiparse("~~#0000FF:alt~~")."|".wikiparse("~~#0000FF:width~~")."|".wikiparse("~~#0000FF:height~~") );
	echo( "|".wikiparse("~~#0000FF:align~~")."|".wikiparse("~~#0000FF:vertical-align~~")."]]<BR>" );
	echo( "~~#AAAAAA:grey~~<BR>" );
	if ( $config['ALLOW_HTMLCODE'] )
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
			
		default:
			if ( pageExists( $title ) )
			{
				if (!( ($mode=="edit") && ($contents!="") ))
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
	if ( ($mode=="edit"||$mode=="editnew")&&$config['SHOW_WIKISYNTAX'] )
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
	$mode = getMode( $config );

	// get the page title
	$title = getTitle( $config );

	// get the page contents
	$contents = updateWiki( $mode, $title, $config );

	// page header
	if ($mode=="edit") 
		htmlHeader(wikiparse($config['LOCALE']['EDIT_TITLE']).$title, $config); 
	else
		htmlHeader($title, $config); 

	// page contents
	htmlstartblock();
	displayPage($title, $mode, $contents);
	htmlendblock();

	// page controls
	displayControls($title, $mode);

	// page footer
	htmlFooter();
}
?>
