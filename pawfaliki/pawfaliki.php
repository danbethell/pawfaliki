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

function latestVersion( $title )
{  
	$results = array();
	
	if ($dh = opendir(pageDir( $title )))
	{
  	while (($file = readdir($dh)) !== false) 
  	{
  		if (preg_match( "/".$title."_v*/", $filename ) )
  			$results[count($results)] = $file;
    }
    closedir($dh);
  }
   
//	$results = glob( pageDir( $title ).$title."_v*" );
	$num=0;
	for ($i=0; $i<count($results); $i++)
	{
		$r = $results[$i];
		$strings = explode( "_", $r );
		$laststring = substr( $strings[count($strings)-1], 1 );
		if ( is_numeric( $laststring ) )
		{
			if ( $laststring>$num )
				$num = $laststring;
		}
	}
	return $num;
}

function writeFile( $title, $contents )
{
	if ( !file_exists( pageDir( $title ) ) )
	{
	//	mkdir( pageDir( $title ), 0777 );
	//	chmod( pageDir( $title ), 0777 );
	}
	$latest = latestVersion( $title );
	$new = $latest+1;
	$fd = fopen( pagePath( $title, $new ), "w" );
	fwrite( $fd, $contents );
	fclose( $fd );	
	//if (file_exists( pagePath( $title ) ) )
	//	unlink( pagePath( $title ) );
	//symlink( pagePath( $title, $new ), pagePath( $title ) );
}

function initWiki( $title )
{
	$dir = (dirname($_SERVER['SCRIPT_FILENAME'])."/Pages");
	if (!file_exists( $dir ) )
		mkdir( $dir, 0755 );
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
  	echo(">\n\t\t<PRE><TABLE WIDTH=\"100%\" BORDER=\"0\"><TR><TD ALIGN=\"LEFT\"><H1>".$title."</H1></TD><TD ALIGN=\"RIGHT\">".wikilink( "HomePage" )."</TD></TR></TABLE></PRE>\n");
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
	// Pawfaliki handles:
  //	bold - **this is bold**
  //	italic - ''this is italic''
  //	underlined - __this is underlined__
  // 	links - specified by WikiWords only!
  // 	extenal links - [[http://tikiwiki.org/]] - note no changing description using |!
  //	non parsed text - ~~~ this is non-parsed ~~~ - NOT IMPLEMENTED YET!
  //	images - {{src|desc|height|width|align|valign}}
  //  special chars - ~169~ - NOT IMPLEMENTED YET!
  //  unicode special chars - ~U:450373~ - NOT IMPLEMENTED YET!
	
	// verbatim text
	$patterns[0] = "/~~~(.*)~~~/";
	$replacements[0] = "\".verbatim( \"$1\" ).\"";	
	// external links
	$patterns[1] = "/\[\[(.*)\]\]/";
	$replacements[1] = "\".externallink( \"$1\" ).\"";		
	// images
	$patterns[2] = "/{{(.*)}}/";
	$replacements[2] = "\".image( \"$1\" ).\"";	
	
	// substitue complex expressions
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
	$patterns[3] = "/([A-Z][a-z0-9]+[A-Z][A-Za-z0-9]+)/";
	$replacements[3] = "\".wikilink( \"$1\" ).\"";	
	
	// substitute simple expressions
	$contents = preg_replace( $patterns, $replacements, $contents );		

	// final expansion
	$cmd = (" \$contents = \"".$contents."\";");
	eval($cmd);		
	
  return $contents;
}

function pageDir( $title )
{
	//return (dirname($_SERVER['SCRIPT_FILENAME'])."/Pages/".$title."/");
	return (dirname($_SERVER['SCRIPT_FILENAME'])."/Pages/");
}

function pagePath( $title, $version="LATEST" )
{
	$version="LATEST";
	return (pageDir($title).$title);//."_v".$version);
}

function pageExists( $title )
{
	if (file_exists( pagePath( $title ) ) )
		return true;
	else
		return false;
}

function displayPage( $title, &$mode )
{ 	
	$contents = "";
	if ( pageExists( $title ) )
	{
		// get contents of a file into a string
		$filename = pagePath( $title );
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
	
//		$contents = file_get_contents( pagePath( $title ) );
	}
	else
	{
		$contents = "This is the page for ".$title."!";
		$mode = "editnew";
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
