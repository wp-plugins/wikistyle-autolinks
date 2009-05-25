<?php
/*
Plugin Name: Autolink WikiStyle
Plugin URI: http://www.xgear.info/software/autolink-wikistyle/
Description: Adds the ability to create automatic in-post links from your page titles or from a list of manual defined links (<em>eg. If you have a page called 'Magic Page', every time you'll write <strong>Magic Page</strong> in a Post, it'll became a link to that page</em>).
Version: 1.3.1
Author: Marco Piccardo
Author URI: http://www.xgear.info/
*/

/*
License: GPLv2
Compatibility: All

Installation:
Put the autolink-wikistyle.php file in your /wp-content/plugins/ directory
and activate through the administration panel.

Read the info and configure the plugin at the Options->Autolink WikiStyle configuration panel.

Copyright (C) Marco Piccardo (http://www.xgear.info/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* Changelog - Release Notes
* v1.0
- Added an option to exclude Autolink generation to specified pages. 
* v0.9
- First Public Version.
- Added possibility to change the HTML tag wrapping the Autolink.
- Added possibility to select which kind of Autolink you want to generate.
- Added possibility to generate custom links. 
* v0.1a
- First non-Public Version.
*/

// Admin Panel
function autolinkAddPages() {
	add_options_page('AutoLink Options', 'AutoLinks', 8, __FILE__, 'autolinkAddAdminPage');
}

function autolinkShowInfoMessage($msg) {
	echo '<div id="message" class="updated fade"><p>' . $msg . '</p></div>';
}

function autolinkShowErrorMessage($msg) {
	echo '<div id="message" class="error fade"><p>' . $msg . '</p></div>';
}

function autolinkSelectedOption($opt) {
	if($opt==1) {
		return 'checked="checked"';
	}
}

function autolinkAddAdminPage() {
	if (isset($_POST['saveOptions'])) {

		// For a little bit more security and easier maintenance, a separate options array is used.
		//var_dump($_POST);
		$options = array(
			"autolinkWS_links"	=> $_POST["autolinkWS_links"],
			"autolinkWS_posts"	=> $_POST["autolinkWS_posts"],
			"autolinkWS_static"	=> $_POST["autolinkWS_static"],
			"autolinkWS_before"	=> $_POST["autolinkWS_before"],
			"autolinkWS_after"	=> $_POST["autolinkWS_after"],
			"autolinkWS_exclusion"	=> $_POST["autolinkWS_exclusion"],
			"autolinkWS_exclusions"	=> $_POST["autolinkWS_exclusions"],
			"autolinkWS_links_exclusion" => $_POST["autolinkWS_links_exclusion"],
			"autolinkWS_links_exclusions" => $_POST["autolinkWS_links_exclusions"]
			);
	
		update_option("autolinkWS_options", $options);
		autolinkShowInfoMessage("Autolink WikiStyle options saved.");

	} elseif (isset($_POST["resetOptions"])) {

		delete_option("autolinkWS_options");
		delete_option("autolinkWS_custom");
		autolinkShowInfoMessage("Autolink WikiStyle options deleted from the WordPress database.");

	} elseif (isset($_POST["saveCustom"])) {

		if(count($_POST["autolink_custom_word"])>0) {
			foreach($_POST["autolink_custom_word"] as $current => $word) {
				$customs[] = array(
					"word"	=> $word,
					"type"	=> $_POST["autolink_custom_type"][$current],
					"url"	=> $_POST["autolink_custom_url"][$current],
					);
			}

			update_option("autolinkWS_custom", $customs);
			autolinkShowInfoMessage("Autolink WikiStyle custom links saved.");
		} else {
			delete_option("autolinkWS_custom");
			autolinkShowErrorMessage("No Autolink WikiStyle custom link inserted!");
		}
	}
	
	$options = get_option("autolinkWS_options");
 	$custom_links = get_option("autolinkWS_custom");

?>
	<div class="wrap">
		<h2>Autolink WikiStyle</h2>
		<div style="position: absolute; top: 0px; right: 0px;"><a href="http://www.treasurewebhunt.com"><img border=0 src="<? bloginfo('siteurl'); ?>/wp-content/plugins/wikistyle-autolinks/images/feast.png" /></a></div>
		<p>Welcome to the Admin Panel of Autolink WikiStyle. With this Plugin you can have any word in your posts substituted with a link to a page of your blog or a custom address.</p>
		<p>The links will be generated only if the title of the page, the link or the custom link, is in the same case of the word you want to link.</p>
		<p><strong>eg.</strong> If you have a page titled <em>Magic Page</em> and a post that sounds like:<br/>
		&acute;<em>[...] yesterday I made a magic page.</em>&acute;<br/>
		the autolink will not be generated.</p>
	</div>

	<div class="wrap">
		<h2>Base Configuration</h2>

	  <form name="formamt" method="post" action="<?=$_SERVER['REQUEST_URI']?>">

            <p><input type="checkbox" name="autolinkWS_links" value="1" <?=autolinkSelectedOption($options["autolinkWS_links"])?> /> Use Blog links to make AutoLinks.<br/><em>Selecting this option will make AutoLink WikiStyle to use your blog links as sourse for generating in-post link.</em></p>
            <p><input type="checkbox" name="autolinkWS_posts" value="1" <?=autolinkSelectedOption($options["autolinkWS_posts"])?> /> Use Blog pages to make AutoLinks.<br/><em>Selecting this option will make AutoLink WikiStyle to use your blog pages as sourse for generating in-post link.</em></p>
        <p><input type="checkbox" name="autolinkWS_static" value="1" <?=autolinkSelectedOption($options["autolinkWS_static"])?> /> Use Custom links to make AutoLinks.<br/><em>Selecting this option will make AutoLink WikiStyle to use your blog links as sourse for generating in-post link.<br/>Set up custom links in the Advanced Congfiguration Panel</em></p>
        <p><input type="checkbox" name="autolinkWS_exclusion" value="1" <?=autolinkSelectedOption($options["autolinkWS_exclusion"])?> /> Don't generate Autolinks in following pages.<br/>
        Comma Separated list of WordPress pages ID: <input type="text" name="autolinkWS_exclusions" value='<?=$options["autolinkWS_exclusions"]?>' /><br/><em>Selecting this option will make AutoLink WikiStyle not to generate autolink in the specified pages.</em><br/><strong>eg.</strong> <em>insert a list of pages like:</em> 12,34,54</p>
        <p><input type="checkbox" name="autolinkWS_links_exclusions" value="1" <?=autolinkSelectedOption($options["autolinkWS_links_exclusions"])?> /> Excludes following pages to be linked by AutoLinks.<br/>
        Comma Separated list of WordPress pages ID: <input type="text" name="autolinkWS_links_exclusion" value='<?=$options["autolinkWS_links_exclusion"]?>' /><br/><em>Selecting this option will make AutoLink WikiStyle not to generate autolink to the specified pages.</em><br/><strong>eg.</strong> <em>insert a list of pages like:</em> 12,34,54</p>
<? if(isset($options["autolinkWS_before"]) and $options["autolinkWS_before"]!='') { $defval1 = stripslashes($options["autolinkWS_before"]); } else { $defval1 = '<a rel="bookmark"'; } ?>
<? if(isset($options["autolinkWS_after"]) and $options["autolinkWS_after"]!='') { $defval2 = stripslashes($options["autolinkWS_after"]); } else { $defval2 = '</a>"'; } ?>
            <p>Customize HTML tag: <input type="text" name="autolinkWS_before" value='<?=$defval1?>' style="text-align:right" /> 
            href="<em>link</em>"  title=&quot;<em>word</em>&quot;&gt;<em>word</em> 
          <input type="text" name="autolinkWS_after" value="<?=$defval2?>" /><br/><em>You can customize the link tag that the Autolink WikiStyle plugin wrap around your AutoLinks.</em><br/><strong>eg.</strong> <em>&lt;span class=&quot;colored&quot;&gt;&lt;a </em> href=&quot;<em>link</em>&quot;  title=&quot;<em>word</em>&quot;&gt;<em>word</em>&lt;/a&gt;&lt;/span&gt;<br/><strong>Please note that the <em> href=&quot;link&quot;  title=&quot;<em>word</em>&quot;&gt;word</em> will be automatically generated!</strong></p>

		<p class="submit">
				<input type="submit" name="saveOptions" value="Update Options &raquo;" />
		</p>

		</form>
	</div>

	<div class="wrap"> 
		<h2>Advanced Configuration</h2>
		<form name="formcust" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
            <p>Here you can specify custom link for the Autolink WikiStyle plugin. Custom links allow you to make any word you want to link to any page, internal or external your blog. You have to select if the link is internal or esxternal. In the case of internal link you have to specify only the ID of the page: the permalink will be auto-generated.</p>
            <p>Here you are some examples:</p>
            <ul><li>
            	<strong>eg.</strong> <em>Magic Page</em> | Internal (this blog) | 32
            </li><li>
            	<strong>eg.</strong> <em>xGear</em> | External (the net) | http://www.xgear.info
            </li></ul>
            <p><strong>Remember to use <em>http://</em> before any external link!</strong></p>

           <script type="text/javascript">
                //<![CDATA[
                
                var pages = [ <?php
                    if(count($custom_links)>0) {
                        for($i=0; $i<count($custom_links); $i++) {
                            //$v=&$this->_pages[$i];
                            if($i>0) echo ",";
                            echo '{word:"' . $custom_links[$i]['word'] . '", type:"' . $custom_links[$i]['type'] . '", url:"' . $custom_links[$i]['url'] . '"}';											
                        }
                    }
                ?> ];
                
                function sm_addPage(word,type,url) {
                
                    var table = document.getElementById('sm_pageTable').getElementsByTagName('TBODY')[0];
                    var ce = function(ele) { return document.createElement(ele) };
                    var tr = ce('TR');
                                                                
                    var td = ce('TD');
					td.style.width='200px';
                    var iUrl = ce('INPUT');
                    iUrl.type="text";
                    iUrl.style.width='95%';
                    iUrl.name="autolink_custom_word[]";
                    if(url) iUrl.value=word;
                    td.appendChild(iUrl);
                    tr.appendChild(td);
                    
					td = ce('TD');
					td.style.width='200px';
					var iPrio = ce('SELECT');
					iPrio.style.width='95%';
					iPrio.name="autolink_custom_type[]";
						var op = ce('OPTION');
						op.text = 'Internal (this blog)';		
						op.value = 'int';
						try {
							iPrio.add(op, null); // standards compliant; doesn't work in IE
						} catch(ex) {
							iPrio.add(op); // IE only
						}
						if(type && type == op.value) {
							iPrio.selectedIndex = 0;
						}
						var op = ce('OPTION');
						op.text = 'External (the net)';		
						op.value = 'ext';
						try {
							iPrio.add(op, null); // standards compliant; doesn't work in IE
						} catch(ex) {
							iPrio.add(op); // IE only
						}
						if(type && type == op.value) {
							iPrio.selectedIndex = 1;
						}
					td.appendChild(iPrio);
					tr.appendChild(td);


                    var td = ce('TD');
                    var iUrl = ce('INPUT');
                    iUrl.type="text";
                    iUrl.style.width='95%';
                    iUrl.name="autolink_custom_url[]";
                    if(url) iUrl.value=url;
                    td.appendChild(iUrl);
                    tr.appendChild(td);
                    
					var td = ce('TD');
					td.style.textAlign="center";
					td.style.width='5px';
					var iAction = ce('A');
					iAction.innerHTML = 'X';
					iAction.href="javascript:void(0);"
					iAction.onclick = function() { table.removeChild(tr); };
					td.appendChild(iAction);
					tr.appendChild(td);

                    var firstRow = table.getElementsByTagName('TR')[1];
                    if(firstRow) {
                        var firstCol = firstRow.childNodes[1];
                        if(firstCol.colSpan>1) {
                            firstRow.parentNode.removeChild(firstRow);
                        }
                    }
                    var cnt = table.getElementsByTagName('TR').length;
                    if(cnt%2) tr.className="alternate";
                    
                    table.appendChild(tr);										
                }
                
                function sm_loadPages() {
                    for(var i=0; i<pages.length; i++) {
                        sm_addPage(pages[i].word,pages[i].type,pages[i].url);
                    }
                }
                
                //]]>										
            </script>
            <table width="100%" cellpadding="3" cellspacing="3" id="sm_pageTable"> 
                <tr>
                    <th scope="col">Word</th>
                    <th scope="col">Type of Link</th>
                    <th scope="col">Link to the Page (or ID)</th>
					<th scope="col">&nbsp;</th>
                </tr>			
                <? //if(count($this->_pages)<=0) { ?>
                        <tr> 
                            <td colspan="5" align="center">No custom link defined.</td> 
                        </tr>
				<? //} ?>
            </table>
            <a href="javascript:void(0);" onclick="sm_addPage();">Add new custom link</a>

			<p class="submit">
				<input type="submit" name="saveCustom" value="Save Custom Links &raquo;" />
			</p>

		</form>
	</div>

	<div class="wrap">
		<h2>Reset Plugin</h2>
		<form name="formamtreset" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
			<p>By pressing the "Reset" button, the plugin will be reset. This means that the stored options will be deleted from the WordPress database. Although it is not necessary, you should consider doing this before uninstalling the plugin, so no trace is left behind.</p>
			<p class="submit">
				<input type="submit" name="resetOptions" value="Reset Options" />
			</p>
		</form>
	</div>
<? if(isset($custom_links[0])) { ?>
<script type="text/javascript">if(typeof(sm_loadPages)=='function') addLoadEvent(sm_loadPages); </script>
<? } ?>
<?

}

function autolinkParsePost($texto) {
	global $wpdb, $tableposts, $post;
	$options = get_option("autolinkWS_options");
 	$custom_links = get_option("autolinkWS_custom");
	$exclusions = explode(',',$options['autolinkWS_exclusions']);
	$links_exclusions = explode(',',$options['autolinkWS_links_exclusions']);
	$enlaces = array();
	$i=0;

	if($options["autolinkWS_exclusion"]!=1 or ($options["autolinkWS_exclusion"]==1 and !in_array($post->ID,$exclusions))) {

		// cargo en salida un array con los tags html
		$patron="/<[^>]*>/i";
		preg_match_all($patron,$texto, $salida, PREG_PATTERN_ORDER);
	
		// cargo los tags html en un array y los sustituyo por etiquetas temporales
		foreach ($salida[0] as $sal) {     
			$buscando[$i] = $salida[0][$i];
			$sustitucion[$i] = "TEMPTAG".$i;
			$texto = ereg_replace($buscando[$i],$sustitucion[$i++],$texto);
		}
	
		// en primer lugar a??mos los links de nuestra base de datos
		if ($options["autolinkWS_links"]==1) {
			$links = get_linkobjects();
			if ($links) {
				foreach ($links as $link) {
					$enlaces[$link->link_name] = stripslashes($options["autolinkWS_before"]).' href="'.$link->link_url.'" title="'.$link->link_name.'">'.$link->link_name.stripslashes($options["autolinkWS_after"]);
				}
			}
		}
	
		// cargo los post para extraer los permalinks
		if ($options["autolinkWS_posts"]==1) {
			$request = "SELECT ID, post_title FROM $tableposts WHERE post_status = 'publish' and ID<>".$post->ID." ORDER BY post_title";
			$envios = $wpdb->get_results($request);
			foreach ($envios as $envio) {
				if(!($options["autolinkWS_links_exclusion"]==1 and in_array($envios->ID,$links_exclusions))) {
					$post_title = stripslashes($envio->post_title);
					$permalink = get_permalink($envio->ID);
					$enlace = stripslashes($options["autolinkWS_before"]).' href="'.$permalink.'" title="'.$post_title.'">'.$post_title.stripslashes($options["autolinkWS_after"]);
					$enlaces[$post_title] = $enlace;
				}
			}
		}
	
		if ($options["autolinkWS_static"]==1) {
			if(is_array($custom_links)) {
				foreach ($custom_links as $envio) {
					$word = stripslashes($envio['word']);
					if($envio['type']=='int') {
						$permalink = get_permalink($envio['url']);
						$enlace = stripslashes($options["autolinkWS_before"]).' href="'.$permalink.'" title="'.$word.'">'.$word.stripslashes($options["autolinkWS_after"]);
					} elseif($envio['type']=='ext') {
						$enlace = stripslashes($options["autolinkWS_before"]).' href="'.$envio['url'].'" title="'.$word.'">'.$word.stripslashes($options["autolinkWS_after"]);
					}
					$enlaces[$word] = $enlace;
				}
			}
		}
	
		// sustituyo las cadenas buscadas por los permalinks
		if(count($enlaces)>0) {
			foreach($enlaces as $buscado => $sustituido) {   
				$texto = str_replace($buscado,$sustituido,$texto);
			}
		}
	
		// e invierto los tag temporales por los html
		foreach ($salida[0] as $sal) {     
			$texto = ereg_replace($sustitucion[--$i],$buscando[$i],$texto);
		}
	}

	return $texto;

}

add_filter('the_content', 'autolinkParsePost'); 
add_action('admin_menu', 'autolinkAddPages');

?>