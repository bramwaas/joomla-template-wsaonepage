<?php

/**
 * @package     	Joomla.Site
 * @subpackage  	mod_menu override
 * @copyright   	Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     	GNU General Public License version 2 or later; see LICENSE.txt
 * Modifications	Joomla CSS
 * 30-4-2017 nav-link toegevoegd bij class voor <a> ivm BS4
 * 1-1-2018 foutje in html na-link verbeterd door ontbrekende spatie na " in te voegen.
 * 9-2-2019 data-target toegevoegd voor openen submenu's 
 * 2020-05-25 Item->id gekwalificeerd met $moduleIdPos om hem beter uniek te maken
 * 2020-06-30 iets andere bookmark en keuze voor one page
 */

defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
$class = $item->anchor_css ? 'class="'.$item->anchor_css.'" ' : '';
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';

if ($item->menu_image)
{
	$item->params->get('menu_text', 1) ?
	$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /><div class="image-title">'.$item->title.'</div> ' :
	$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
	
	if ($item->deeper) {
		$class = 'class="'.$item->anchor_css.' dropdown-toggle" data-toggle="dropdown" ';
		$item->flink = '#';
	}
}

elseif ($item->deeper) {
	$linktype = $item->title. '<b class="caret"></b>' ;
	if ($item->level < 2) {
		$class = 'class="'.$item->anchor_css.' dropdown-toggle" data-toggle="dropdown" ';
		$item->flink = '#data-item-' . $moduleIdPos . $item->id;
	}
	else { // level >= 2
		$linktype = $item->title;  // origineel alleen deze
	}
}

else {
	$linktype = $item->title;
}

$class = ($class > ' ') ? str_ireplace('class="','class="nav-link ',$class) : 'class="nav-link" ';
// TODO omzetten naar voorwaarde bij browsernav
if (stripos($item->note, '#op#') !== false) { // new code for one page  when #op# is in $item-note
        $item->bookmark = ($item->flink == '/') ? 'home' : ltrim(str_ireplace(array('/', '\\', '.html'), array('-', '-', ''), $item->flink), '-#') ;
        
    ?><a id="dropdownMenuLink-<?php echo $moduleIdPos . $item->id . '" ' . $class; ?>href="<?php echo  '#' . $item->bookmark ; ?>"  <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
}
else
{
switch ($item->browserNav) :
default:
case 0:
    ?><a id="dropdownMenuLink-<?php echo $moduleIdPos . $item->id . '" ' . $class; ?>href="<?php echo $item->flink; ?>"  <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
		break;

	case 1:
		// _blank
?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
		break;

	case 2:
	// window.open

?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a>
<?php
		break;

endswitch;
}
