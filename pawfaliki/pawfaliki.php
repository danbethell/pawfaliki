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
$config['SPECIAL'] = array();
$config['LOCKED'] = array();
$config['BLOCKED_IPS'] = array();
$config['ERRORS'] = array();
$p_diff = new PawfalikiDiff();

//===========================================================================
//===========================================================================
// CONFIG:
// This section contains variables to configure various aspects of the wiki
$config['TITLE'] = "Hello";
$config['HOMEPAGE'] = "HomePage";	
$config['BUTTONPREFIX'] = "[";
$config['BUTTONSUFFIX'] = "]";
$config['CHANGES_TO'] = "";
$config['LOCKED']['HomePage'] = 1;
$config['SPECIAL']['PageList'] = 1;
$config['ADMIN'] = "webmaster at pawfal dot org";
$config['ADMIN_EMAIL'] = "webmaster@pawfal.org";

//===========================================================================
//===========================================================================

// our licensing information
function license()
{
	?>
			<!-- Creative Commons License -->
			This work is licensed under a <a rel="license" CLASS="wiki_external" href="http://creativecommons.org/licenses/by-sa/2.0/">Creative Commons License</a>.
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
	echo("\t<STYLE TYPE=\"text/css\">\n");
  ?>
<!--
	body
	{
		background-color: black;
		color: white; 
		font-family: courier-new, monospace;
		font-size: 12px;  
		margin-left: 6px;
		margin-right: 18px;
	}
  
	td.wiki_header
	{
		font-size: 24px;
		font-weight: bold;    
	}
  
	pre.wiki_body
	{
		width: 100%;
		font-family: courier-new, monospace;
		padding-left: 4px;
		padding-right: 4px;
	}
  
	hr.wiki_break
	{  
		border-style: none;
		border-top-style: solid;
		border-width: 1px;
		border-color: #666666;
	}
  
	a
	{
		text-decoration: none;  
	}
  
	a.wiki_internal
	{
		color: lime;
	}
    
	a.wiki_internal:hover
	{
		color: black;
		background: lime;
	}
  
	a.wiki_internal:active
	{
		color: green;
	}
  
	a.wiki_external
	{
		color: lime;
	}
    
	a.wiki_external:hover
	{
		color: black;
		background: lime;
	}
  
	a.wiki_external:active
	{
		color: green;
	}
  
	input.wiki_btn
	{	
		font-family: courier-new, monospace;
		font-size: 12px; 
		color: lime;
		background: black;
		border-style: none;
		padding: 0px;
		cursor: pointer;
	}
  
	input.wiki_btn:hover
	{	
		color: black;
		background: lime;
	}
  
	input.wiki_btn:active
	{	
		color: green;
	}  
  
	p.error
	{
		font-weight: bold;
		text-align: center;
		padding: 3px;
		border-style: solid;
		border-width: 1px;
		color: red;
	}
        
	textarea.wiki_edit
	{	
		font-family: courier-new, monospace;
		font-size: 12px; 
		color: black;
		background: white;
		padding: 4px;
		border-style: none; 
		height: 320px;
		width: 99%;
	}
  
-->
  <?php
	echo("\t</STYLE>\n");
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

// writes a new file from a patched original
function writeFileFromPatch( $title, $patch )
{
	$newcontents = "";
	if (!isIpBlocked())
  {
		global $p_diff;
		emailPatch( $title, $patch );
	  $contents = read_file( pagePath( $title ) );
		$newcontents = $p_diff->txt_patch( $contents, $patch ); 
		$fd = fopen( pagePath( $title ), "w" );
		fwrite( $fd, $newcontents );
		fclose( $fd );
  }
  return $newcontents;	
}

// emails a patch to the CHANGES_TO email address
function emailPatch( $title, $patch )
{
	global $config, $p_diff;	
  if ($config['CHANGES_TO']!=""&&$patch!="")
  {
	  $ipaddress = $_SERVER['REMOTE_ADDR'];
	  $subject = $title." :: ".gethostbyaddr($ipaddress)." (".$ipaddress.")";
    $patcharray = $p_diff->splitWithNL( $patch );
    $body = "";
    foreach ( $patcharray as $p )
    {
    	if (strlen(trim($p))>0)
      	$body.=trim($p)."\n";
    }
		mail( $config['CHANGES_TO'], $subject, $body, "From: ".$config['ADMIN_EMAIL']."\r\n" );
 	}
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
	global $p_diff;
	$result = "";
	if ( $mode=="save" )
	{
		if ( isset($_POST['contents']) )
    {
    	$oldcontents = stripslashes( $_POST['oldcontents'] );
    	$contents = stripslashes( $_POST['contents'] );
      
      // write file      
      writeFile( $title, $contents );
			
      //$patch = $p_diff->diff( $oldcontents, $contents );
      //$result = writeFileFromPatch( $title, $patch );
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
	echo("<BODY TEXT=\"white\">\n");

	// any errors?
	foreach ($config['ERRORS'] as $err)
		echo( "<P CLASS=\"error\">".$err."</P>" );

  echo("\t<TABLE CLASS=\"wiki_top\" WIDTH=100%>\n");
  echo("\t\t<TR>\n");
  echo("\t\t\t<TD CLASS=\"wiki_header\">".$title."</TD>\n");
  echo("\t\t\t<TD CLASS=\"wiki_page_buttons\" ALIGN=\"right\">".wikiparse( $config['BUTTONPREFIX']."HomePage".$config['BUTTONSUFFIX']." ".$config['BUTTONPREFIX']."PageList".$config['BUTTONSUFFIX'] )."</TD>\n");
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
	echo("\n<!-- PAGE BODY -->\n");
  echo("<HR CLASS=\"wiki_break\">\n");
}

// the end of our wiki body
function htmlendblock()
{
  echo("<HR CLASS=\"wiki_break\">\n");
	echo("<!-- END OF PAGE BODY -->\n\n");
}

// link to another wiki page
function wikilink( $title )
{
	if ( pageExists( $title ) )
		return ("<A HREF=\"".$_SERVER['PHP_SELF']."?page=".$title."\" CLASS=\"wiki_internal\">".$title."</A>");
	else
		return ($title."<A HREF=\"".$_SERVER['PHP_SELF']."?page=".$title."\" CLASS=\"wiki_internal\">?</A>");
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
	$resultstr = "<A HREF=\"".$src."\" CLASS=\"wiki_external\">".$desc."</A>";		
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
	$contents=$results[1];
	$tokens = array_slice( $results, 1);
	$contents = implode(":", $tokens);			
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
	return ( isset( $config['LOCKED'][$title] ) );
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
			echo("<PRE CLASS=\"wiki_body\" WIDTH=\"100%\">\n");
			echo( wikiparse( $contents ) );
      echo("</PRE>\n");
      break;
    case "edit": case "editnew":
			echo( "<FORM CLASS=\"wiki_edit\" ACTION=\"".$_SERVER['PHP_SELF']."?page=".$title."\" METHOD=\"post\">\n" );
      //echo( "<INPUT TYPE=\"HIDDEN\" NAME=\"oldcontents\" VALUE=\"".addslashes($contents)."\">\n" );
      echo( "<TEXTAREA CLASS=\"wiki_edit\" NAME=\"contents\"\" WRAP=\"OFF\">".$contents."</TEXTAREA>\n" );	
      break;
   }    	
}

// display the wiki controls
function displayControls( $title, &$mode )
{
	global $config;
  echo("\t<TABLE CLASS=\"wiki_bottom\" WIDTH=100%>\n");
  echo("\t\t<TR>\n");
  echo("\t\t\t<TD CLASS=\"wiki_controls\">\n");
	switch ($mode)
  {
  	case "display":
 			if (!(isSpecial($title)||isLocked($title)))
      {
	    	echo( "\t\t\t\t<BR>\n" );
        echo( "\t\t\t\t<FORM ACTION=\"".$_SERVER['PHP_SELF']."?page=".$title."\" METHOD=\"post\">\n" );
        echo( "\t\t\t\t\t<INPUT TYPE=\"HIDDEN\" NAME=\"mode\" VALUE=\"edit\">\n" );
        echo( "\t\t\t\t\t".$config['BUTTONPREFIX']."<INPUT CLASS=\"wiki_btn\" VALUE=\"Edit\" TYPE=\"SUBMIT\">".$config['BUTTONSUFFIX']."" );
        echo( "\t\t\t\t</FORM>\n" );
      }
      break;
    case "edit":
    	echo( "\t\t\t\t<BR>\n" );
      echo( "\t\t\t\t\t".$config['BUTTONPREFIX']."<INPUT CLASS=\"wiki_btn\" NAME=\"mode\" VALUE=\"save\" TYPE=\"SUBMIT\">".$config['BUTTONSUFFIX']."\n" );
      echo( "\t\t\t\t\t".$config['BUTTONPREFIX']."<INPUT CLASS=\"wiki_btn\" NAME=\"mode\" VALUE=\"cancel\" TYPE=\"SUBMIT\">".$config['BUTTONSUFFIX']."" );
      echo( "\t\t\t\t</FORM>\n" );
      break;
    case "editnew":
    	echo( "\t\t\t\t<BR>\n" );
      echo( "\t\t\t\t".$config['BUTTONPREFIX']."<INPUT CLASS=\"wiki_btn\" NAME=\"mode\" VALUE=\"save\" TYPE=\"SUBMIT\">".$config['BUTTONSUFFIX']."" );
      echo( "\t\t\t\t</FORM>\n" );
  		break;
  }
	echo("\t\t\t</TD>\n");
  echo("\t\t\t<TD ALIGN=\"right\" CLASS=\"wiki_license\">\n");
  license();
  echo("\t\t\t</TD>\n");
  echo("\t\t</TR>\n");
  echo("\t</TABLE>\n");
}

/*	
  The PawfalikiDiff class is based upon Nils Knappmeier's GPL'd
  "Implementation of a GNU diff alike function from scratch"
  and is Copyright (C)2003, Nils Knappmeier <nk@knappi.org>
  http://www.pmwiki.org/wiki/Cookbook/PHPDiffEngine  
*/
class PawfalikiDiff 
{
	function splitWithNL($text) 
	{
	  $array = split("\n", $text);
	  for ($i=0; $i<count($array); $i++) 
  	  if ($array[$i][count($array[$i])-1]!="\n")
			  $array[$i]=$array[$i]."\n";
	  if ($array[count($array)-1]=="\n") 
		  array_pop($array);
    else 
		  $array[count($array)-1]=$array[count($array)-1];
	  return $array;
  }

  function nextOccurence($line, &$r_array, $where) 
  {
	  $tmp = $r_array[$line];
	  if (!$tmp) return FALSE;
	  foreach($tmp as $nr) 
		{
		  if ($where<=$nr) 
		  {
			  $where = $nr;
			  return $nr;
		  }
    }
	  return FALSE;
  }

  function dist($a,$b) 
  {
	  $d1=$b[1]-$a[1];
	  $d2=$b[2]-$a[2];
	  return $d2+$d1;
  }

  function diff($text1, $text2) 
  {
    $array1 = $this->splitWithNL($text1);
    $array2 = $this->splitWithNL($text2);
    foreach($array1 as $nr => $line) 
    {
		  $r_array1[$line][] = $nr;
    }
    foreach($array2 as $nr => $line) 
    {
		  $r_array2[$line][] = $nr;
    }
    $result="";

    $a[1]=0;  /* counter for array1 */
    $a[2]=0;  /* counter for array2 */
    $actions=Array();
    while($a[1]<sizeof($array1) && $a[2]<sizeof($array2)) 
    {
		  if ($array1[$a[1]]==$array2[$a[2]]) 
      {
	      $a[1]++;
	      $a[2]++;
	      $actions[]=copy;
		  } 
      else 
      {
	  	  $best[1]=count($array1);
	      $best[2]=count($array2);
	      $scan=$a;
	      while( $this->dist( $a, $scan )<$this->dist( $a, $best ) ) 
        {
				  $tmp[1]=$this->nextOccurence( $array2[$scan[2]], $r_array1, $scan[1] );
				  $tmp[2]=$scan[2];
				  if ( $tmp[1] && $this->dist( $a, $tmp ) < $this->dist( $a,$best ) ) 
        	  $best=$tmp; 	    
				  $tmp[1]=$scan[1];
				  $tmp[2]=$this->nextOccurence( $array1[$scan[1]], $r_array2, $scan[2] );
				  if ( $tmp[2] && $this->dist( $a, $tmp ) < $this->dist( $a,$best ) ) 
        	  $best=$tmp; 
				  $scan[1]++;
				  $scan[2]++;
	      }

	      for($i=$a[1]; $i<$best[1]; $i++) 
        {
				  $actions[]=del;
	      }
	      for($i=$a[2]; $i<$best[2]; $i++) 
        {
				  $actions[]=add;
	      }	
	      $a=$best;
		  }	       
    }
    for( $i=$a[1]; $i<sizeof($array1); $i++ ) 
    {
		  $actions[]=del;
    }   
    for( $i=$a[2]; $i<sizeof($array2); $i++ ) 
    {
		  $actions[]=add;
    }
    $actions[]=finish;
    $x=$xold=0;
    $y=$yold=0;
    $realAction=""; /* the current action */

    foreach( $actions as $action ) 
    {
		  if ($action==del) 
      {
	      if ($realAction=="" || $realAction=="d") 
        {
				  $realAction="d";
	      } 
        else 
        {
				  $realAction="c";
	      }
	      $x++;
		  }
		  if ($action==add) 
      {
	      if ($realAction=="" || $realAction=="a") 
        {
				  $realAction="a";
	      } 
        else 
        {
				  $realAction="c";
	      }
	      $y++;
		  }
		  if ($action==copy || $action==finish) 
      {     
	  	  if ($xold+1 == $x) 
        {
				  $xstr=$x;
	      } 
        else 
        {
				  $xstr=($xold+1).",$x";
	      }
	      if ($yold+1 == $y) 
        {
				  $ystr=$y;
	      } 
        else 
        {
				  $ystr=($yold+1).",$y";
	      }

	      if ($realAction=="a") 
        {
				  $result.= ($x)."a$ystr\n";
				  for($i=$yold; $i<$y;$i++) 
          {
			      $result.= "> ".$array2[$i];
				  }
	      } 
        else if ($realAction=="d") 
        {
				  $result.= ($xstr)."d".($y)."\n";
				  for($i=$xold; $i<$x;$i++) 
          {
			      $result.= "< ".$array1[$i];
				  }
	      }
        else if ($realAction=="c") 
        {
				  $result.= "$xstr$realAction$ystr\n";
				  for($i=$xold; $i<$x;$i++) 
          {
			      $result.= "< ".$array1[$i];
				  }
				  $result.= "---\n";
				  for($i=$yold; $i<$y;$i++) 
				  {
			      $result.= "> ".$array2[$i];
				  }
	      }
	      $x++; $y++;
	      $realAction="";
	      $xold=$x;
	      $yold=$y;
		  }
	  }
    return $result;
  }
  
  function array_cut_head( &$arr, $prefix )
  {
  	foreach( $arr as $a )
    {
    	$this->cut_head( $a, 0, $prefix );
    }
  }

  function cut_head(&$str, $key, $prefix) 
  {
	  if (strpos($str,$prefix)===0) 
    {
		  $str =  substr($str,strlen($prefix));
    } 
    else 
    {
		  print "Something is wrong in the patch: ";
		  print "'$str' should begin with '$prefix'\n";
		  exit;
    }
  }

  function txt_patch($text, $patch) 
  {	
	  $array=$this->splitWithNL($text);
    if ($patch=="") 
  	  return $text;
    if ( substr($patch, -1)=="\n" ) 
  	  $patch = substr($patch, 0, strlen($patch)-1);

    $patch_array=split("\n", $patch);

    for ( $i=0; $i<count($patch_array); $i++ ) 
    {
		  $patch_array[$i]=$patch_array[$i]."\n";
    }

    $i=0;
    $nlIndex=array_search("\\ No newline at end of file\n", $patch_array);
    while ( $nlIndex!=false && $i<2 ) 
    { 
		  /* This shouldn't be happening more than two times in a valid patch */
		  $newEntry=$patch_array[$nlIndex-1].$patch_array[$nlIndex];
		  array_splice($patch_array, $nlIndex-1, 2, $newEntry);
		  $nlIndex=array_search("\\ No newline at end of file\n", $patch_array);
		  $i++;
    }

    /* Start computing */
    $current=0;
    do 
    {
		  if ( preg_match("/^([\d,]+)([adc])([\d,]+)$/", $patch_array[$current],$matches)==0 ) 
      {
	      print "<pre>Error in line $current: ".$patch_array[$current]." not a command\n".sizeof($patch_array); 
	      print "</pre>";
	      exit;
		  }
		  list($full, $left, $action, $right) = $matches;

		  /* Compute start and end of each side */
		  list($left_start, $left_end)=split(",",$left);
		  list($right_start, $right_end)=split(",",$right);
		  if ($left_end=="") 
      { 
    	  $left_end = $left_start; 
      }
		  if ($right_end=="") 
      { 
    	  $right_end = $right_start; 
      }	

      /* Perform action and switch to next patch */
		  if ($action=="a") 
      {
	      $replace=array_slice($patch_array, $current+1, $right_end-$right_start+1);	    
	      $this->array_cut_head($replace, '> ');
	      array_splice($array, $right_start-1, 0, $replace);
	      $current+=$right_end-$right_start+2;
		  } 
      else if ($action=="d") 
      {
	  	  /* Check whether lines in patch are like in file */
	      $should=array_slice($patch_array, $current+1, $left_end-$left_start+1);
	      $this->array_cut_head($should, '< ');
	      $is=array_splice($array, $right_start, $left_end-$left_start+1);
	      $current+=$left_end-$left_start+2;
		  } 
      else if ($action=="c") 
      {
	  	  $replace=array_slice($patch_array,
		    $current+1+$left_end-$left_start+2,
		    $right_end-$right_start+1);
	      $this->array_cut_head($replace, '> ');
	      $is = array_splice($array, $right_start-1, $left_end-$left_start+1, $replace);

	      /* Check whether lines in patch are like in text */
	      $should=array_slice($patch_array, $current+1, $left_end-$left_start+1);
	      $this->array_cut_head($should, '< ');
	      $current+=1+$left_end-$left_start+1+1+$right_end-$right_start+1;
		  }
	  } while ( $current<count($patch_array) );

    $result = implode("", $array);
    $suffix="\n\\ No newline at end of file\n";
    if (substr($result,-strlen($suffix))==$suffix) 
    {
		  $result=substr($result,0,strlen($result)-strlen($suffix));
    }
    return $result;
  }
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
	echo("<PRE CLASS=\"wiki_body\" WIDTH=\"100%\">\n");
	echo( wikiparse( $contents ) );
	echo( "</PRE>\n");
}
else
	displayPage($title, $mode, $contents);
htmlendblock();

// page controls
displayControls($title, $mode);

// page footer
htmlFooter();
?>
