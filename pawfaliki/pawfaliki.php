<?php

/*  Pawfaliki
 *  Copyleft (C) 2004 Dan Bethell <dan at pawfal dot org>
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

$config = array();
$config['TITLE'] = "Pawfal";
$config['HOMEPAGE'] = "Pawfal";	
$config['BGCOLOR'] = "black";
$config['TEXT'] = "white";
$config['LINK'] = "lime";
$config['VLINK'] = "lime";
$config['ALINK'] = "green";

$config['VERBATIM'] = array();

$config['SPECIALPAGES'] = array();
$config['SPECIALPAGES']['PageList'] = 1;

function writeFile( $title, $contents )
{
	$fd = fopen( pagePath( $title ), "w" );
	fwrite( $fd, $contents );
	fclose( $fd );	
}

function initWiki( $title )
{
	$dir = (dirname($_SERVER['SCRIPT_FILENAME'])."/Pages");
	$contents = "Hello and welcome to Pawfaliki!";	
	writeFile( $title, $contents );
}

function getTitle( $config )
{
	$page = "";
	if (!isset($_REQUEST['page']))
	{
		$page = "HomePage";
		if (!pageExists( $page ))
		{
			initWiki( $page );
		}
	}
	else
		$page = $_REQUEST['page'];
	return $page;
}

function getMode( $config )
{
	$mode = "";
	if (!isset($_POST['mode']))
		$mode = "display";
	else
		$mode = $_POST['mode'];
	return $mode;
}

function updateWiki( &$mode, $title, $config )
{
	if ($mode=="save")
	{
		if (isset($_POST['contents']))
			writeFile( $title, stripslashes($_POST['contents']) );
		$mode = "display";
	}
	if ($mode=="cancel")
	{
		$mode = "display";
	}
}

function htmlheader( $title, $config )
{
	if ($title=="HomePage") $title = $config["HOMEPAGE"];
	if ($config['TITLE']==$title)
		echo("<HTML>\n\t<HEAD>\n\t\t<TITLE>".$config["TITLE"]."</TITLE>\n\t</HEAD>\n\t");
	else
		echo("<HTML>\n\t<HEAD>\n\t\t<TITLE>".$config["TITLE"]." :: ".$title."</TITLE>\n\t</HEAD>\n\t");
	echo("<BODY BGCOLOR=\"".$config['BGCOLOR']."\"");
	echo(" TEXT=\"".$config['TEXT']."\" ");
  	echo(" LINK=\"".$config['LINK']."\" ");
  	echo(" VLINK=\"".$config['VLINK']."\" ");
  	echo(" ALINK=\"".$config['ALINK']."\" ");
  	echo(">\n\t\t<PRE><TABLE WIDTH=\"100%\" BORDER=\"0\"><TR><TD ALIGN=\"LEFT\"><H1>".$title."</H1></TD><TD ALIGN=\"RIGHT\">".wikiparse( "HomePage PageList" )."</TD></TR></TABLE></PRE>\n");
}

function htmlfooter()
{
	echo("\t</BODY>\n</HTML>\n");
}

function htmlstartblock()
{
	echo("\n<PRE>\n");
}

function htmlprint( $string )
{
	echo($string);
}

function htmlendblock()
{
	echo("</PRE>\n\n");
}

function wikilink( $title )
{
	if ( pageExists( $title ) )
		return ("<A HREF=\"".$_SERVER['PHP_SELF']."?page=".$title."\">".$title."</A>");
	else
		return ($title."<A HREF=\"".$_SERVER['PHP_SELF']."?page=".$title."\">?</A>");
}

function rawtext( $text )
{
	return $text;
}

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

function colouredtext( $text )
{
	$results = explode( ":", $text );
	$size=count($results);
	if ($size==0||$size!=2)
		return $text;	
	
	$colour=$results[0];
	$contents=$results[1];
		
	$resultstr = "<FONT COLOR=\"#".$colour."\">".$contents."</FONT>";
	return verbatim( $resultstr );
}

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

function getVerbatim( $index )
{
	global $config;
	$verbat = &$config['VERBATIM'];
	return $verbat[$index];
}

function verbatim( $contents )
{
	global $config;
	$verbat = &$config['VERBATIM'];
	$index = count($verbat);
	$verbat[$index] = $contents;
	return "\".getVerbatim(".$index.").\"";
}

function wikiparse( $contents )
{
	$contents = htmlspecialchars($contents);
		
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
	$patterns[0] = "/\*\*(.*)\*\*/";
	$replacements[0] = "<B>$1</B>";
	
	// italic
	$patterns[1] = "/''(.*)''/";
	$replacements[1] = "<I>$1</I>";
	
	// underline
	$patterns[2] = "/__(.*)__/";
	$replacements[2] = "<U>$1</U>";	
	
	// wiki words
	$patterns[3] = "/([A-Z][a-z0-9]+[A-Z][A-Za-z0-9]+)/";
	$replacements[3] = "\".wikilink( \"$1\" ).\"";	
	
	// substitute simple expressions
	$contents = preg_replace( $patterns, $replacements, $contents );		

	// final expansion
	$cmd = (" \$contents = \"".$contents."\";");
	eval($cmd);		
	
  return $contents;
}

function pageDir( $title="" )
{
	return (dirname($_SERVER['SCRIPT_FILENAME'])."/Pages/");
}

function pagePath( $title )
{
	return (pageDir($title).$title);
}

function isSpecial( $title )
{
	global $config;
	return ( isset( $config['SPECIALPAGES'][$title] ) );
}

function pageExists( $title )
{
	if (file_exists( pagePath( $title ) ) || isSpecial( $title ) )
		return true;
	else
		return false;
}

function pageList()
{
	$contents = "";
	$files = glob(pageDir()."*");
	$details = array();
	foreach ($files as $file)
		$details[$file] = filemtime( $file );
	arsort($details);
	reset($details);
	while ( list($key, $val) = each($details) )
  	$contents .= basename($key)." (".date("D M j G:i:s T Y", $val ).")\n";
	return $contents;
}

function displayPage( $title, &$mode )
{ 	
	$contents = "";
	
	// handle special pages 
	// note - EVERY page included in config['SPECIALPAGES'] must be handled!
	switch ($title)
	{
		case "PageList":
			$contents = pageList();
			break;
			
		default:
			if ( pageExists( $title ) )
			{
				// get contents of a file into a string
				$contents = file_get_contents( pagePath( $title ) );
		
				// need to use this code if php version < 4.3.0
				/*
					$filename = pagePath( $title );
					$handle = fopen($filename, "r");
					$contents = fread($handle, filesize($filename));
					fclose($handle);
				*/	
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
			htmlprint( wikiparse( $contents ) );
      break;
    case "edit": case "editnew":
			htmlprint( "<FORM ACTION=\"".$_SERVER['PHP_SELF']."?page=".$title."\" METHOD=\"post\"><TEXTAREA NAME=\"contents\" ROWS=15 COLS=80>".$contents."</TEXTAREA>" );	
      break;
   }    	
}

function displayControls( $title, &$mode )
{
	switch ($mode)
  {
  	case "display":
 			if (!isSpecial($title))
	    	htmlprint( "<BR><FORM ACTION=\"".$_SERVER['PHP_SELF']."?page=".$title."\" METHOD=\"post\"><INPUT TYPE=\"HIDDEN\" NAME=\"mode\" VALUE=\"edit\"><INPUT VALUE=\"Edit\" TYPE=\"SUBMIT\"></FORM>" );
      break;
    case "edit":
    	htmlprint( "<BR><INPUT NAME=\"mode\" VALUE=\"save\" TYPE=\"SUBMIT\"> <INPUT NAME=\"mode\" VALUE=\"cancel\" TYPE=\"SUBMIT\"></FORM>" );
      break;
    case "editnew":
    	htmlprint( "<BR><INPUT NAME=\"mode\" VALUE=\"save\" TYPE=\"SUBMIT\"></FORM>" );
      break;
   }
}

// main block
header("Cache-Control: no-store, no-cache, must-revalidate");
$mode = getMode( $config );
$title = getTitle( $config );
updateWiki( $mode, $title, $config );
if ($mode=="edit") htmlHeader("Edit: ".$title, $config); else htmlHeader($title, $config); 
htmlstartblock();
displayPage($title, $mode);
htmlendblock();
displayControls($title, $mode);
htmlFooter();
?>
