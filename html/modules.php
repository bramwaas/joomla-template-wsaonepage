<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.wsa_onepage
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 17-5-2020 weer opgenomen, deels uit voorbeeld voor BS template, deels uit Cassiopeia.
 */

defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg. To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 * print_r ( mixed $expression [, bool $return = FALSE ] )
 */

/*
 * Module chrome for rendering the module in a submenu
 */
function modChrome_no($module, &$params, &$attribs)
{
    echo '<!-- style = "no" --> ';
    if ($module->content)
    {
        echo $module->content;
    }
}
function modChrome_none($module, &$params, &$attribs)
{
    echo '<!-- style = "none" --> ';
    echo "<-- $module: \n";
    print_r($module);
    echo "--- $params: \n";
    print_r($param);
    echo "--- $attribs: \n";
    print_r($attribs);
    echo "\n -->";
    
    if ($module->content)
    {
        echo $module->content;
    }
}
function modChrome_default($module, &$params, &$attribs)
{
    echo '<!-- style = "default" --> ';
    $modulePos	   = $module->position;
    $moduleTag     = $params->get('module_tag', 'div');
    $headerTag     = htmlspecialchars($params->get('header_tag', 'h4'));
    $headerClass   = htmlspecialchars($params->get('header_class', ''));
    
    if ($module->content)
    {
        echo '<' . $moduleTag . ' class="' . $modulePos . ' card ' . htmlspecialchars($params->get('moduleclass_sfx')) . '">';
        if ($module->showtitle && $headerClass !== 'card-title')
        {
            echo '<' . $headerTag . ' class="card-header' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
        }
        echo '<div class="card-body">';
        if ($module->showtitle && $headerClass === 'card-title')
        {
            echo '<' . $headerTag . ' class="' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
        }
        echo $module->content;
        echo '</div>';
        echo '</' . $moduleTag . '>';
    }
}

function modChrome_cardGrey($module, &$params, &$attribs)
{
    echo '<!-- style = "cardGrey" --> ';
    $modulePos	   = $module->position;
    $moduleTag     = $params->get('module_tag', 'div');
    $headerTag     = htmlspecialchars($params->get('header_tag', 'h4'));
    $headerClass   = htmlspecialchars($params->get('header_class', ''));
    
    if ($module->content)
    {
        echo '<' . $moduleTag . ' class="' . $modulePos . ' card card-grey ' . htmlspecialchars($params->get('moduleclass_sfx')) . '">';
        if ($module->showtitle && $headerClass !== 'card-title')
        {
            echo '<' . $headerTag . ' class="card-header' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
        }
        echo '<div class="card-body">';
        if ($module->showtitle && $headerClass === 'card-title')
        {
            echo '<' . $headerTag . ' class="' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
        }
        echo $module->content;
        echo '</div>';
        echo '</' . $moduleTag . '>';
    }
}


function modChrome_well($module, &$params, &$attribs)
{
    echo '<!-- style = "well" --> ';
    if ($module->content)
	{
		echo "<div class=\"well " . htmlspecialchars($params->get('moduleclass_sfx')) . "\">";
		if ($module->showtitle)
		{
			echo "<h3 class=\"page-header\">" . $module->title . "</h3>";
		}
		echo $module->content;
		echo "</div>";
	}
}

function modChrome_specialgrey($module, &$params, &$attribs)
{
    echo '<!-- style = "specialgrey" --> ';
    if ($module->content)
	{
		echo "<div class=\"specialgrey " . htmlspecialchars($params->get('moduleclass_sfx')) . "\">";
		if ($module->showtitle)
		{
			echo "<h3 class=\"page-header\">" . $module->title . "</h3>";
		}
		echo "<div class=\"module-content\">" . $module->content . "</div>";
		echo "</div>";
	}
}

function modChrome_specialblue($module, &$params, &$attribs)
{
    echo '<!-- style = "specialblue" --> ';
    if ($module->content)
	{
		echo "<div class=\"specialblue " . htmlspecialchars($params->get('moduleclass_sfx')) . "\">";
		if ($module->showtitle)
		{
			echo "<h3 class=\"page-header\">" . $module->title . "</h3>";
		}
		echo "<div class=\"module-content\">" . $module->content . "</div>";
		echo "</div>";
	}
}

function modChrome_specialred($module, &$params, &$attribs)
{
    echo '<!-- style = "specialred" --> ';
    if ($module->content)
	{
		echo "<div class=\"specialred " . htmlspecialchars($params->get('moduleclass_sfx')) . "\">";
		if ($module->showtitle)
		{
			echo "<h3 class=\"page-header\">" . $module->title . "</h3>";
		}
		echo "<div class=\"module-content\">" . $module->content . "</div>";
		echo "</div>";
	}
}

function modChrome_specialgold($module, &$params, &$attribs)
{
    echo '<!-- style = "specialgold" --> ';
    if ($module->content)
	{
		echo "<div class=\"specialgold " . htmlspecialchars($params->get('moduleclass_sfx')) . "\">";
		if ($module->showtitle)
		{
			echo "<h3 class=\"page-header\">" . $module->title . "</h3>";
		}
		echo "<div class=\"module-content\">" . $module->content . "</div>";
		echo "</div>";
	}
}

?>