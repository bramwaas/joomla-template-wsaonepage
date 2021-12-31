<?php
/**
 * @package     	Joomla.Site
 * @subpackage  	mod_menu override
 * @copyright   	Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     	GNU General Public License version 2 or later; see LICENSE.txt
 * Modifications	Joomla CSS
  * bw 2015-09-26       line 56 </a></span> changed in </span></a></span>
 * 31-12-2021 eerste aanpassingen BS5 (data- => data-bs- )
 * 31-12-2021  ook hier nav-link toegevoegd bij class voor <a> ivm BS5
 */

\defined('_JEXEC') or die;
use Joomla\CMS\Filter\OutputFilter;

// Note. It is important to remove spaces between elements.
$class = $item->anchor_css ? 'class="'.$item->anchor_css.'" ' : '';
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';

if ($item->menu_image)
{
    $item->getParams()->get('menu_text', 1) ?
    $linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />	<span class="image-title">'.$item->title.'</span> ' :
    $linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
    if ($item->deeper) {
        $class = 'class="'.$item->anchor_css.' dropdown-toggle" data-toggle="dropdown"  data-bs-toggle="dropdown" ';
        $item->flink = '#';
    }
    
}
elseif ($item->deeper) {
    $linktype = $item->title. '<b class="caret"></b>' ;
    if ($item->level < 2) {
        $class = 'class="'.$item->anchor_css.' dropdown-toggle" data-toggle="dropdown"  data-bs-toggle="dropdown" ';
        $item->flink = '#';
    }
    else {
        $linktype = $item->title;
    }
}
else {
	$linktype = $item->title;
}

$flink = $item->flink;
$flink = OutputFilter::ampReplace(htmlspecialchars($flink));
$class = ($class > ' ') ? str_ireplace('class="','class="nav-link ',$class) : 'class="nav-link" ';
if (isset($item->bookmark) || stripos($item->note, '#op#') !== false) { // new code for one page  when #op# is in $item-note
    //        $item->bookmark = ($item->flink == '/') ? 'home' : ltrim(str_ireplace(array('/', '\\', '.html'), array('-', '-', ''), $item->flink), '-#') ;
    //              create bookmark from route in accordance with component wsaonepage default, only if not set by component already.
    if (!isset($item->bookmark)) {$item->bookmark = ($item->route == '/') ? 'home' : ltrim(str_ireplace(array('/', '\\', '.html'), array('-', '-', ''), $item->route), '-#') ;}
     ?><a id="dropdownMenuLink-<?php echo $moduleIdPos . $item->id . '" ' . $class; ?>href="<?php echo  '#' . $item->bookmark ; ?>"  <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
}
else
switch ($item->browserNav) :
	default:
	case 0:
?><a <?php echo $class; ?>href="<?php echo $flink; ?>" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
		break;
	case 1:
		// _blank
?><a <?php echo $class; ?>href="<?php echo $flink; ?>" target="_blank" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
		break;
	case 2:
		// window.open
		$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'.$params->get('window_open');
			?><a <?php echo $class; ?>href="<?php echo $flink; ?>" onclick="window.open(this.href,'targetWindow','<?php echo $options;?>');return false;" <?php echo $title; ?>><span><?php echo $linktype; ?></span></a><?php
		break;
endswitch;
