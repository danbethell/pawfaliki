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

// == CONFIG ==
// This section contains variables to configure various aspects of the wiki
$config['TITLE'] = "Pawfal";
$config['HOMEPAGE'] = "Pawfal";	
$config['BGCOLOR'] = "black";
$config['TEXT'] = "white";
$config['LINK'] = "lime";
$config['VLINK'] = "lime";
$config['ALINK'] = "green";
$config['WORDWRAP'] = 96;
$config['CHANGES_TO'] = "dan-b@moving-picture.com";
// == CONFIG ==

$config['VERBATIM'] = array();
$config['SPECIALPAGES'] = array();
$config['SPECIALPAGES']['PageList'] = 1;

function writeFile( $title, $contents )
{
	$fd = fopen( pagePath( $title ), "w" );
	fwrite( $fd, $contents );
	fclose( $fd );	
}

function writeFileFromPatch( $title, $patch )
{
	emailPatch( $title, $patch );
  $filename = pagePath( $title );
	$fd = fopen( $filename, "r");
	$contents = fread( $fd, filesize($filename) );
	fclose( $fd );
	$newcontents = txt_patch( $contents, $patch ); 
	$fd = fopen( pagePath( $title ), "w" );
	fwrite( $fd, $newcontents );
	fclose( $fd );
  return $newcontents;	
}

function emailPatch( $title, $patch )
{
	global $config;	
  if ($config['CHANGES_TO']!=""&&$patch!="")
  {
	  $ipaddress = $_SERVER['REMOTE_ADDR'];
	  $subject = $title." :: ".gethostbyaddr($ipaddress)." (".$ipaddress.")";
    
    // format for email
    $patcharray = splitWithNL( $patch );
    $body = "";
    foreach ( $patcharray as $p )
    {
    	if (strlen(trim($p))>0)
      	$body.=trim($p)."\n";
    }
		mail( $config['CHANGES_TO'], $subject, $body );
 	}
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

function updateWiki( &$mode, $title, $config )
{
	$result = "";
	if ( $mode=="save" )
	{
		if ( isset($_POST['contents']) )
    {
    	$oldcontents = $_POST['oldcontents'];
    	$contents = stripslashes( $_POST['contents'] );
      
      $patch = diff( $oldcontents, $contents );
			$result = writeFileFromPatch( $title, $patch );
    }
		$mode = "display";
	}
	if ($mode=="cancel")
	{
		$mode = "display";
	}
  return $result;
}

function htmlheader( $title, $config )
{
	if ($title=="HomePage") 
  	$title = $config["HOMEPAGE"];
    
	echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 3.2 Final//EN\">");
	echo("<HTML>\n\t<HEAD>\n\t\t");
	echo("<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=ISO-8859-1\">\n\t\t");
	echo("<TITLE>");
  
	if ($config['TITLE']==$title)
		echo($config["TITLE"]);
	else
		echo($config["TITLE"]." :: ".$title);
	
  echo("</TITLE>\n\t</HEAD>\n\t");
	echo("<BODY BGCOLOR=\"".$config['BGCOLOR']."\"");
	echo(" TEXT=\"".$config['TEXT']."\" ");
  echo(" LINK=\"".$config['LINK']."\" ");
  echo(" VLINK=\"".$config['VLINK']."\" ");
  echo(" ALINK=\"".$config['ALINK']."\" ");
  echo(">\n\t\t<PRE><TABLE WIDTH=\"100%\" BORDER=\"0\">");
  echo("<TR><TD ALIGN=\"LEFT\"><H1>".$title."</H1></TD>");
  echo("<TD ALIGN=\"RIGHT\">".wikiparse( "HomePage PageList" ));
  echo("</TD></TR></TABLE></PRE>\n");
}

function htmlfooter()
{
	echo("\t</BODY>\n</HTML>\n");
}

function htmlstartblock()
{
	global $config;
	echo("\n<PRE WIDTH=\"".$config['WORDWRAP']."\">\n");
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
	$tokens = array_slice( $results, 1);
	$contents = implode(":", $tokens);
			
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
	
	//$contents = wordwrap( $contents, $config['WORDWRAP'] );	
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
	global $config;
	$contents = "";
	
	// handle special pages 
	switch ($title)
	{
		case "PageList":
			$contents = pageList();
			break;
			
		default:
			if ( pageExists( $title ) )
			{
				// get contents of a file into a string
				//$contents = file_get_contents( pagePath( $title ) );
		
				// need to use this code if php version < 4.3.0
				$filename = pagePath( $title );
				$handle = fopen($filename, "r");
				$contents = fread($handle, filesize($filename));
				fclose($handle);					
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
			htmlprint( "<FORM ACTION=\"".$_SERVER['PHP_SELF']."?page=".$title."\" METHOD=\"post\"><INPUT TYPE=\"HIDDEN\" NAME=\"oldcontents\" VALUE=\"".$contents."\"><TEXTAREA NAME=\"contents\" ROWS=32 COLS=".$config['WORDWRAP'].">".$contents."</TEXTAREA>" );	
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

/*
  Implementation of a GNU diff alike function from scratch.
  Copyright (C) 2003  Nils Knappmeier <nk@knappi.org>

  http://www.pmwiki.org/wiki/Cookbook/PHPDiffEngine
  
  Note: I've slightly reformatted the code from its original form
        but not modified the implementation at all.
*/

function splitWithNL($text) 
{
	$array = split("\n", $text);
	for ($i=0; $i<count($array); $i++) 
  {
  	if ($array[$i][count($array[$i])-1]!="\n")
			$array[$i]=$array[$i]."\n";
	}
	if ($array[count($array)-1]=="\n") 
  {
		array_pop($array);
	} 
  else 
  {
		$array[count($array)-1]=$array[count($array)-1];
	}
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
  $array1 = splitWithNL($text1);
  $array2 = splitWithNL($text2);
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
	    while( dist( $a, $scan )<dist( $a, $best ) ) 
      {
				$tmp[1]=nextOccurence( $array2[$scan[2]], $r_array1, $scan[1] );
				$tmp[2]=$scan[2];
				if ( $tmp[1] && dist( $a, $tmp ) < dist( $a,$best ) ) 
        	$best=$tmp; 	    
				$tmp[1]=$scan[1];
				$tmp[2]=nextOccurence( $array1[$scan[1]], $r_array2, $scan[2] );
				if ( $tmp[2] && dist( $a, $tmp ) < dist( $a,$best ) ) 
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

function php_Patch($page, $restore) {
  $page_text = $page['text'];
  krsort($page); reset($page);
  
  foreach($page as $k=>$v) {
    if ($k < $restore) break;
    if (!preg_match('/^diff:/',$k)) continue;
    $page_text = txt_patch($page_text, $v);
  }
  return $page_text;

}

function txt_patch($text, $patch) 
{	
	$array=splitWithNL($text);
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
	    array_walk($replace, 'cut_head','> ');
	    array_splice($array, $right_start-1, 0, $replace);
	    $current+=$right_end-$right_start+2;
		} 
    else if ($action=="d") 
    {
	  	/* Check whether lines in patch are like in file */
	    $should=array_slice($patch_array, $current+1, $left_end-$left_start+1);
	    array_walk($should, 'cut_head','< ');
	    $is=array_splice($array, $right_start, $left_end-$left_start+1);
	    if ($should!==$is) 
      {
				//print "<pre>According to the patch, in lines $left_start to ";
				//print " $left_end there should be a ";
				//print urlencode(implode("",$should))."";
				//print "but I only find a ";
				//print urlencode(implode("",$is))."</pre>";
	    }
	    $current+=$left_end-$left_start+2;
		} 
    else if ($action=="c") 
    {
	  	$replace=array_slice($patch_array,
		  $current+1+$left_end-$left_start+2,
		  $right_end-$right_start+1);
	    array_walk($replace, 'cut_head','> ');
	    $is = array_splice($array, $right_start-1, $left_end-$left_start+1, $replace);

	    /* Check whether lines in patch are like in text */
	    $should=array_slice($patch_array, $current+1, $left_end-$left_start+1);
	    array_walk($should, 'cut_head','< ');
	    
	    if ($should!==$is) 
      {
				//print "<pre>According to the patch, in lines $left_start to";
				//print "$left_end there should be a\n";
				//print implode("",$should);
				//print "but I only find a\n";
				//print implode("",$is)."</pre>";
	    }
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

// main block
header("Cache-Control: no-store, no-cache, must-revalidate");
$mode = getMode( $config );
$title = getTitle( $config );
$contents = updateWiki( $mode, $title, $config );
if ($mode=="edit") htmlHeader("Edit: ".$title, $config); else htmlHeader($title, $config); 
htmlstartblock();
if ( $contents!="" )
	htmlprint( wikiparse( $contents ) );
else
	displayPage($title, $mode);
htmlendblock();
displayControls($title, $mode);
htmlFooter();

?>
