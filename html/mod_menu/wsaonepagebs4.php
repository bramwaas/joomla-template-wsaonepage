<?php
/**
 * @package     	Joomla.Site
 * @subpackage  	mod_menu override
 * @copyright   	Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     	GNU General Public License version 2 or later; see LICENSE.txt
 * Modifications	Joomla CSS
 * 24-4-2016 ook begin en eind van navbar naar deze module-override gehaald (uit module position-1), zodat deze overal in index.php geplaatst kan worden
 * 30-4-2017 kleine aanpassingen vooruitlopend op BS4
 * 7-1-2018 start with J4 namespaces
 * 5-1-2019 brandimage geen extra / meer
 * 20-1-2019 navtext toegevoegd
 * 26-1-2019  aanpassingen verschillen BS4 en BS3 mbv twbs_version
 * 6-2-2019
 * 16-5-2020 twbs 3 verwijzingen verwijderd gebruik deze niet meer.
 * 20-5-2020 Item->id gekwalificeerd met $moduleIdPos . om hem zo uniek te maken
 * 26-5-2020 minder commentaar in html, maar wel vaker line feeds PHP_EOL
 * 28-5-2020 deze en aangeroepen programma's hernoemd naar wsaonepagebs4... om deze alleen te laten werken bij een men waain expliciet voor die layout gekozen is.
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;   // this is the same as use Joomla\CMS\Factory as Factory
use Joomla\CMS\Helper\ModuleHelper;

//use Joomla\CMS\HTML\HTMLHelper;
//use Joomla\CMS\Plugin\PluginHelper;
//use Joomla\CMS\Document\HtmlDocument;
//use Joomla\CMS\Document\Renderer\Html\ModulesRenderer;

$id = '';

if ($tagId = $params->get('tag_id', ''))
{
	$id = ' id="' . $tagId . '"';
}

// Note. It is important to remove spaces between elements.
$app = Factory::getApplication();
$document = Factory::getDocument();
$sitename = $app->get('sitename');
$tDisplaySitename = htmlspecialchars($app->getTemplate(true)->params->get('displaySitename')); // 1 yes 2 no
$tBrandImage = htmlspecialchars($app->getTemplate(true)->params->get('brandImage'));
$tMenuType = htmlspecialchars($app->getTemplate(true)->params->get('menuType'));
$twbs_version = htmlspecialchars($app->getTemplate(true)->params->get('twbs_version', '4')); // bootstrap version 3 of (default) 4 

$wsaNavbarExpand = htmlspecialchars($app->getTemplate(true)->params->get('wsaNavbarExpand', 'navbar-expand-md'));
$wsaNavtext = ($app->getTemplate(true)->params->get('wsaNavtext'));

$moduleTag     = $params->get('module_tag', 'div');
$headerTag     = htmlspecialchars($params->get('header_tag', 'h4'));
$headerClass   = htmlspecialchars($params->get('header_class', ''));
$moduleIdPos          = 'M' . $module->id . $module->position;


	/**
	 * Loads and renders the module
	 *
	 * @param   string  $position  The position assigned to the module
	 * @param   string  $style     The style assigned to the module
	 *
	 * @return  mixed
	 *
	 * copied from plugins\content\loadmodule 
	 */
	 function wsa_load($position, $style = 'none')
	{
		
		//self::$modules[$position] = '';
		$document = Factory::getDocument();
		$renderer = $document->loadRenderer('module');
		$modules  = ModuleHelper::getModules($position);
		$params   = array('style' => $style);
		//ob_start();

		foreach ($modules as $module)
		{
			echo $renderer->render($module, $params);
		}

		// self::$modules[$position] = ob_get_clean();

		return $modules[$position];
		
	}
?>

<?php 
// div met role = "navigation" in plaats van nav gebruikt oa IE8 nav nog niet kent, maar kan via moduleTag aangepast worden
echo '<!-- Begin Navbar--><' . $moduleTag . ' class="' . $module->position . ' navbar ' . $wsaNavbarExpand .  ' ' . $tMenuType . '" role="navigation">'. PHP_EOL;
// echo '<div class="container-fluid">' . PHP_EOL;
if ($tBrandImage > " ") {
    echo '<a class="navbar-brand" href="#"><img src="' . $tBrandImage .'" alt="Brand image ' . $sitename . '" /></a>'. PHP_EOL;
}
if(  $document->countModules('navbar-brand')){
    echo '<span class="navbar-text navbar-brand" >'. PHP_EOL;
    wsa_Load('navbar-brand');
    echo PHP_EOL . '</span>' . PHP_EOL;
}
if ($tDisplaySitename == "1") {
    echo '<a class="navbar-brand" href="#">' . $sitename . '</a>'. PHP_EOL;
}
echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-' . $moduleIdPos . '" aria-controls="navbar-' . $moduleIdPos . '" aria-expanded="false" aria-label="Toggle navigation">' . PHP_EOL
. '<span class="navbar-toggler-icon"></span>' . PHP_EOL
. '</button>' . PHP_EOL
. '<div id="navbar-' . $moduleIdPos . '" class="collapse navbar-collapse">' . PHP_EOL;
?>
<!-- oude module aangevuld met bS4 attributen -->
<ul <?php echo $id; ?> class="navbar-nav mr-auto mod-menu nav menu<?php echo $class_sfx;?>">
<?php foreach ($list as $i => &$item) 
{
	$class = 'nav-item item-'.$item->id;
	
	if ($item->id == $default_id)
	{
	    $class .= ' default';
	}
	if ($item->id == $active_id  || ($item->type === 'alias' && $item->params->get('aliasoptions') == $active_id))
	{
		$class .= ' current';
	}

	if (in_array($item->id, $path))
	{
		$class .= ' active';
	}
	elseif ($item->type === 'alias')
	{
		$aliasToId = $item->params->get('aliasoptions');

		if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
		{
			$class .= ' active';
		}
		elseif (in_array($aliasToId, $path))
		{
			$class .= ' alias-parent-active';
		}
	}

	if ($item->type === 'separator')
	{
		$class .= ' divider';
	}

	if ($item->deeper) {
		if ($item->level < 2) {
			$class .= ' dropdown deeper';
		}
		else {
			$class .= ' dropdown-submenu deeper';
		}
	}

	if ($item->parent)
	{
		$class .= ' parent';
	}

	echo '<li class="' . $class . '">';
	
	echo '<!--Itemtype =' . $item->type . ' -->' ;

	// Render the menu item.
	switch ($item->type) :
		case 'separator':
		case 'component':
		case 'heading':
		case 'url':
		    require ModuleHelper::getLayoutPath('mod_menu', 'wsaonepagebs4_'.$item->type);
			break;

		default:
		    require ModuleHelper::getLayoutPath('mod_menu', 'wsaonepagebs4_url');
			break;
	endswitch;

	// The next item is deeper.
	// TODO controleren of hier ID ook uniek gemaakt kan worden
	if ($item->deeper){
	    echo '<ul id=data-item-' . $moduleIdPos . $item->id . ' class="nav-child unstyled mod-menu__sub list-unstyled small dropdown-menu" . aria-labelledby="dropdownMenuLink-' . $moduleIdPos . $item->id . '">';
	}
	// The next item is shallower.
	elseif ($item->shallower)
	{
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
}
?></ul>
<!-- einde oude module -->
<?php 
if ($wsaNavtext > " "){
    echo '<span>' . $wsaNavtext . '</span>' . PHP_EOL;
}
if (  $document->countModules('navbar-right')) {
    echo '<span id="navbar-right-mod' . $moduleIdPos . '" class="navbar-text navbar-right" >' . PHP_EOL;
    wsa_Load('navbar-right');
    echo PHP_EOL . '</span>' . PHP_EOL;
}
echo '</div><!--/div container-fluid --></' . $moduleTag . '><!--End navbar-->'. PHP_EOL;

?>


<?php 
// voorlopig extra acties voor onepage sections
echo '<!-- onepage sections uit menu -->'. PHP_EOL;
foreach ($list as $i => &$item) {
    echo '<!-- item->type=' , $item->type , ' item->level=' , $item->level ,  ' $item->title=' , $item->title , ' $item->flink=' , $item->flink,  ' -->', PHP_EOL;  
    if ($item->type=='component' && $item->level==1) {
        echo '<section id="' , $item->flink , '" class="container" >', PHP_EOL;
        echo '<div class="container"><div class="row"><div class="col-lg-8 mx-auto">', PHP_EOL;
        echo '<p>' , $item->title , '</p>' , PHP_EOL;
        echo '</div></div></div>' , PHP_EOL;
        echo '</section>', PHP_EOL;
        
    }
    
    
}

/*
 * 	<section id="about">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <h2>About this page</h2>
          <p class="lead">This is a great place to talk about your webpage. This template is purposefully unstyled so you can use it as a boilerplate or starting point for you own landing page designs! This template features:</p>
          <ul>
            <li>Clickable nav links that smooth scroll to page sections</li>
            <li>Responsive behavior when clicking nav links perfect for a one page website</li>
            <li>Bootstrap's scrollspy feature which highlights which section of the page you're on in the navbar</li>
            <li>Minimal custom CSS so you are free to explore your own unique design options</li>
          </ul>
        </div>
      </div>
    </div>
  </section>
  

 * 
 * 
 * 
 */

echo '<!-- einde onepage sections uit menu -->'. PHP_EOL;

?>