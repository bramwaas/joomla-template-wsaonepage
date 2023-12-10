<?php
/**
 * @package     	Joomla.Site
 * @subpackage  	mod_menu override
 * @copyright   	Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
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
 * 28-5-2020 deze en aangeroepen programma's hernoemd naar wsaonepagebs4... om deze alleen te laten werken bij een menu waarin expliciet voor die layout gekozen is.
 * 6-6-2020 eerste geslaagde poging om content artikelen op te halen.
 * 9-6-2020 newsfeeds op algemene manier trachten op te halen.
 * 24-6-2020 newsfeed werkt, maar populateState niet goed gebruikt.
 * 25-6-2020 populateState laten werken door juiste instelling input, hij lijkt later in actie te komen dan ik verwacht had
 * 27-6-2020 toch weer zelf vullen state en juiste params gezocht
 * 29-6-2020 populateState nu weer gebruikt en params, params.menu overschreven, aangevuld
 * 30-6-2020 ook actief menu aanpassen met setActive.
 * 1-7-2020 new code for one page  when #op# is in $item-note
 * 4-7-2020 aparte switch entry voor content en tags verwijderd en wat displays van params; tijdelijke vervanging app params door component params. 
 * 5-7-2020 tijdelijke vervanging app params door component params na megre met menu params. Newsfeeds newsfeed en Content featured werken 
 * 6-7-2020 algemene switch op option verwijderd alleen nog specifieke uitzonderingen, contactform ok labels correct translated. 
 * 12-7-2020 Ook initialisatie van SiteRouter per menu item, om verkeerde paden te corrigeren, ook extra override en default template path toegevoergd en display uit component gebruikt 
 * 13-7-2020 Nog enkele properties van BaseController aangepast, nu wertk display in BaseComponent ook echt
 * 15-7-2020 eigen display functie uit code verwijderd. addModelpath via controller.
 * 18-7-2020 added override wrapper view onepage, to put the sections of onepage in that view 
 * 19-7-2020 did not work correctly with wrapper only with com_content so overrides wrapper removed
 * 10-8-2020 list of components moved to com_wsaonepage where it belongs and removed from this module.
 * 1-9-2021 J4 Item->getParams() replacing ->params
 * 3-12-2021 removed use ... Registry that is not existent anymore and also not used in this block.
 * 16-12-2021 twbs 3 verwijzingen hersteld
 * 31-1-2022 referentie naar 'wsaonepagebs4_'.$item->type verbeterd, zodat deze ook bij gebruik in ander template werkt.
 */

\defined('_JEXEC') or die;
use Joomla\CMS\Factory;   // this is the same as use Joomla\CMS\Factory as Factory
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;  // JModelLegacy
use Joomla\CMS\Form\Form; 
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;  //tbv algemene renderComponent
use Joomla\CMS\MVC\Controller\BaseController; 



//JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel'); // Is waarschijnlijk overbodig om com_content op te kunnen halen 


$id = '';

if ($tagId = $params->get('tag_id', ''))
{
	$id = ' id="' . $tagId . '"';
}
$w0container = 'container';
// Note. It is important to remove spaces between elements.
$app = Factory::getApplication();
$document = Factory::getDocument();
$sitename = $app->get('sitename');
$itemid   = $app->input->getCmd('Itemid', '');
$input = $app->input;
$tDisplaySitename = htmlspecialchars($app->getTemplate(true)->params->get('displaySitename', 1)); // 1 yes 2 no
$tBrandImage = htmlspecialchars($app->getTemplate(true)->params->get('brandImage',''));
$menuType = htmlspecialchars($app->getTemplate(true)->params->get('menuType', ''));
$twbs_version = htmlspecialchars($app->getTemplate(true)->params->get('twbs_version', '4')); // bootstrap version 3, 5 or (default) 4 
if ($twbs_version == 3) {
	$menuType = str_replace(array("light", "dark", "bg-"), array("default", "inverse", ""), $menuType);	
}

$wsaNavbarExpand = htmlspecialchars($app->getTemplate(true)->params->get('wsaNavbarExpand', 'navbar-expand-md'));
$wsaNavtext = ($app->getTemplate(true)->params->get('wsaNavtext'));
$wsaNavbarWidthTransition = htmlspecialchars($app->getTemplate(true)->params->get('wsaNavbarWidthTransition', 'container'));

$wsaNavbarpos = htmlspecialchars($app->getTemplate(true)->params->get('wsaNavbarpos', ''));
$wsaNavbarbg = htmlspecialchars($app->getTemplate(true)->params->get('wsaNavbarbg', 'bg-custom'));
$wsaNavbarfg = htmlspecialchars($app->getTemplate(true)->params->get('wsaNavbarfg', 'navbar-light'));

$wsaNavbarbg = htmlspecialchars($app->getTemplate(true)->params->get('wsaNavbarbg', 'bg-custom'));
$wsaNavbarWidthTransition = htmlspecialchars($app->getTemplate(true)->params->get('wsaNavbarWidthTransition', 'container'));


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
echo '<!-- Begin Navbar-->';
if ($wsaNavbarpos == 'fixed-top') {echo '<div id="navbar-placeholder" class="navbar ' . $wsaNavbarExpand .'">&nbsp;</div>' . PHP_EOL;
}
echo '<' . $moduleTag . ' class="' . $module->position . ' navbar ' . $wsaNavbarpos . ' ' . $wsaNavbarExpand . ' ' . $wsaNavbarbg . ' ' . $wsaNavbarfg . ' ' . $wsaNavbarWidthTransition . '" role="navigation">'. PHP_EOL;

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
echo '<!-- $twbs_version=' . $twbs_version . ". -->\n";
if ($twbs_version == '3') {
echo '<div class="navbar-header">
	<button type="button"  class="navbar-toggle" data-toggle="collapse" data-target="#navbar-<?php echo $moduleIdPos; ?>"  aria-controls="navbar-<?php echo $moduleIdPos; ?>" aria-expanded="false">
	<span class="sr-only">Toggle navigation</span>
 	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	<span class="icon-bar"></span>
	</button>'; 
} else { // $twbs_version == '4' or '5'
echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-' . $moduleIdPos . '" aria-controls="navbar-' . $moduleIdPos . '" aria-expanded="false" aria-label="Toggle navigation">' . PHP_EOL
. '<span class="navbar-toggler-icon"></span>' . PHP_EOL
. '</button>' . PHP_EOL; }
if ($twbs_version == '3') { echo '</div> <!-- navbar-header -->';}

echo '<div id="navbar-' . $moduleIdPos . '" class="collapse navbar-collapse">' . PHP_EOL;
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
	if ($item->id == $active_id  || ($item->type === 'alias' && $item->getParams()->get('aliasoptions') == $active_id))
	{
		$class .= ' current';
	}

	if (in_array($item->id, $path))
	{
		$class .= ' active';
	}
	elseif ($item->type === 'alias')
	{
	    $aliasToId = $item->getParams()->get('aliasoptions');

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
        require (__DIR__ .  '/wsaonepagebs4_'.$item->type . '.php');
        break;
        
    default:
        require (__DIR__ .  '/wsaonepagebs4_url.php');
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