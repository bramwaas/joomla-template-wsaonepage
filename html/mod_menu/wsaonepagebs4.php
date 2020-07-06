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
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;   // this is the same as use Joomla\CMS\Factory as Factory
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry; // for new Registry en params object
use Joomla\CMS\MVC\Model\BaseDatabaseModel;  // JModelLegacy
use Joomla\CMS\Form\Form; 
use Joomla\CMS\Language\Text;

use Joomla\CMS\Component\ComponentHelper;  //tbv algemene renderComponent
use Joomla\CMS\MVC\Controller\BaseController; 

//use Joomla\CMS\HTML\HTMLHelper;
//use Joomla\CMS\Plugin\PluginHelper;
//use Joomla\CMS\Document\HtmlDocument;
//use Joomla\CMS\Document\Renderer\Html\ModulesRenderer;
//use Joomla\CMS\MVC\Model\FormModel;  // JModelForm


//JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel'); // Is waarschijnlijk overbodig om com_content op te kunnen halen 


$id = '';

if ($tagId = $params->get('tag_id', ''))
{
	$id = ' id="' . $tagId . '"';
}

// Note. It is important to remove spaces between elements.
$app = Factory::getApplication();
$document = Factory::getDocument();
$sitename = $app->get('sitename');
$itemid   = $app->input->getCmd('Itemid', '');
$input = $app->input;
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
	
	
	
	//  Uitgaande van Display method BaseController.php (in plaats van $this $controller gebruikens en in plaats van gegevens uit input de gegevens uit de menu link
	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
     * extra params, in stead of using values from input object, or other objects
	 * @param   object   $controller Instance of BaseController object
	 * @param   string   $viewName   Name of the view to create to display the component data
	 * @param   string   $prefix     Prefix of the name of the view to create to display the component data
	 * @param   string   $basePath   Path to the component used as basepath for the view
	 * @param   string   $viewType   Type of view, default 'html' 
	 * @param   string   $viewLayout Layout of the output, name of the layout file, default 'default'
	 * 
	 * 	 *
	 * @return  \JControllerLegacy  A \JControllerLegacy object to support chaining.
	 *
	 * @since   3.0
	 */
	function wsaDisplay($cachable = false, $urlparams = array(), $controller, $viewName , $prefix = '', $basePath, $viewType = 'html', $viewLayout = 'default', $model = null)
	{
	    $document = \JFactory::getDocument();
//	    if (!isset($viewtype))    $viewType = $document->getType();  // normaal html
//	    if (!isset($viewName)) $viewName = $controller->get('input')->get('view', $controller->get('default_view')); // naam van de view bijv featured, article, newsfeed
//	    if (!isset($viewLayout)) $viewLayout = $controller->get('input')->get('layout', 'default', 'string'); // naam van layout bv default vewijzend naar layoutbestand.
	    echo '<!-- wsaDisplay overgenomen van BaseController.php en aangepast in wsaonepagebs4.php:' , PHP_EOL;
	    echo '$urlparams:', PHP_EOL;
	    print_r($urlparams);
	    echo PHP_EOL, '$viewType:', PHP_EOL;
	    print_r($viewType);
	    echo PHP_EOL, '$viewName:', PHP_EOL;
	    print_r($viewName);
	    echo PHP_EOL, '$viewLayout:', PHP_EOL;
	    print_r($viewLayout);
	    echo '-->', PHP_EOL;
	    // extra om foute instellingen te overschrijven.
	    $controller->set('paths',  array('view' => $basePath . '/views/' )); // TODO controleren of nodig en dan ook terugdraaien
	                          
	    // einde extra
	    $view = $controller->getView($viewName, $viewType, $prefix, array('base_path' => $basePath, 'layout' => $viewLayout));
	    
	    // tijdelijk extra $model via parameters
	    if (isset($model)) {
	        // Push the model into the view (as default)
	        $view->setModel($model, true);
	    }
	    // einde extra
	    // Get/Create the model
	    else if ($model = $controller->getModel($viewName))
	    {
	        // Push the model into the view (as default)
	        $view->setModel($model, true);
	    }
	    
	    $view->document = $document;
	    
	    // Display the view
	    if ($cachable && $viewType !== 'feed' && \JFactory::getConfig()->get('caching') >= 1)
	    {
	        $option = $controller->input->get('option');
	        
	        if (is_array($urlparams))
	        {
	            $app = \JFactory::getApplication();
	            
	            if (!empty($app->registeredurlparams))
	            {
	                $registeredurlparams = $app->registeredurlparams;
	            }
	            else
	            {
	                $registeredurlparams = new \stdClass;
	            }
	            
	            foreach ($urlparams as $key => $value)
	            {
	                // Add your safe URL parameters with variable type as value {@see \JFilterInput::clean()}.
	                $registeredurlparams->$key = $value;
	            }
	            
	            $app->registeredurlparams = $registeredurlparams;
	        }
	        
	        try
	        {
	            /** @var \JCacheControllerView $cache */
	            $cache = \JFactory::getCache($option, 'view');
	            $cache->get($view, 'display');
	        }
	        catch (\JCacheException $exception)
	        {
	            $view->display();
	        }
	    }
	    else
	    {
	        $view->display();
	    }
	    
	    return $controller;
	}
	//  Uitgaande van Display method BaseController.php
	
	
?>

<?php 
// div met role = "navigation" in plaats van nav gebruikt oa IE8 nav nog niet kent, maar kan via moduleTag aangepast worden
$tMenuType = 'fixed-top';  // todo tijdelijk hard-coded
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
/* 
 * List of sections with a component for each menu-item on this page.
 * 
 * By default, joomla only has one component per page, so a component often stores and uses general variables.
 * We sometimes have to override those in the list of menu components, 
 * so first secure the variables of the component page and restore them after processing the component list.
  */
/* 
 * secure variables of app page and page component
 */
$wsaOrgAppParams = clone $app->getParams();
$wsaOrgInput = clone $input;
$wsaOrgActiveMenuItem     = $app->getMenu()->getActive();
$wsaOrgDocumentViewType = $document->getType();  //= html is always ok
echo '<!-- onepage Component Sections from menu -->'. PHP_EOL;
//echo '<!-- App params org: ', PHP_EOL;
//print_r($wsaOrgAppParams);
//echo ' -->', PHP_EOL;
try {
//    echo '<!-- input object:' . PHP_EOL;
//    print_r($input);
//    echo ' -->', PHP_EOL;
    if ($controller = BaseController::getInstance(substr($wsaOrgMenuQueryArray['option'],4)) ) 
    {
        $wsaOrgControllerVars = $controller->getProperties(FALSE);
        echo '<!-- $controller->get(name):' , $controller->get(name), ' -->', PHP_EOL;
    }
} catch (Exception $e) {
    echo '<!-- '. PHP_EOL;
    echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
    echo ' -->', PHP_EOL;
}
// [Itemid] => 299
// [option] => com_content
// [view] => article
// [id] => 143


    
foreach ($list as $i => &$item) {
    try {
// TODO juiste selectie voor menuitems
        if (stripos($item->note, '#op#') !== false) { // new code for one page  when #op# is in $item-note
        /*
         * actions for all kind of components (option) / views (view)
         * start with overwrite app values with values of this menu option.
         */
        // aangepaste versie van componentpath ed in variabelen in plaats van constantes.
        $wsaOption = preg_replace('/[^A-Z0-9_\.-]/i', '', $item->query['option']);
        $wsaComponent = ucfirst(substr($wsaOption, 4));
        $wsaJPATH_COMPONENT = JPATH_BASE . '/components/' . $wsaOption;
        $wsaJPATH_COMPONENT_SITE = JPATH_SITE . '/components/' . $wsaOption;
        $wsaJPATH_COMPONENT_ADMINISTRATOR = JPATH_ADMINISTRATOR . '/components/' . $wsaOption;
        foreach ($wsaOrgActiveMenuItem->query as $tmpKey => $tmpVal) {
            $app->input->set($tmpKey,NULL);}
        foreach ($item->query as $tmpKey => $tmpVal) {
            $app->input->set($tmpKey,$tmpVal);}
        $app->input->set ('Itemid', $item->id); // set de Itemid op de Id van het huidige menu alternatief is misschien ook het alternatief met setActive
        $app->getMenu()->setActive($item->id > 0 ? $item->id : $wsaOrgActiveMenuItem->id );
        // zoek component params        
        $wsaComponentParams = $app->getParams($item->query['option']);
//        echo '<!-- Component params ', $item->query['option'], ' :', PHP_EOL;
//                print_r($wsaComponentParams);
//        echo ' -->', PHP_EOL;
        //      zoek menuparams en voeg ze samen met componentparams (menu overschrijft component)
        $wsaMenuParams =  new Registry($item->params);
//        echo '<!-- Menu params : ', PHP_EOL;
//        print_r($wsaMenuParams);
//        echo ' -->', PHP_EOL;
        $wsaComponentParams->merge($wsaMenuParams);
        // zet samengestelde component menu parameters in app params.
        $tmp = $app->getParams()->flatten();
        foreach ($tmp as $tmpKey => $tmpVal) {
            $app->getParams()->remove($tmpKey);}
        $app->getParams()->merge($wsaComponentParams);
        echo '<!-- Start with menuid $item->id=' , $item->id, ' $app->getMenu()->getActive()->id :', $app->getMenu()->getActive()->id,  PHP_EOL;
        //       // print_r($item);
        echo ' -->', PHP_EOL;
//
/*
 *  section header html for each item
 */
        echo '<section id="' , $item->bookmark  , '" class="container" >', PHP_EOL;
        echo '<div class="container"><div class="row"><div class="col-lg-8 mx-auto">', PHP_EOL;
        echo '<!-- ' , $item->title , ' -->' , PHP_EOL;
        echo '<!-- ' , ' $item->flink=' , $item->flink, ' $item->link=', $item->link, PHP_EOL, ' $item->query[option]=', $item->query['option'], ' $item->query[view]=', $item->query['view'], ' $item->query[id]=', $item->query['id'], ' -->', PHP_EOL;
            // end section header html
            // foreach ( $item->query as $key => $value) { echo " {$key} => {$value} " , PHP_EOL; }
            // overgenomen uit newsfeeds.php
            JLoader::register($wsaComponent . 'HelperRoute', $wsaJPATH_COMPONENT . '/helpers/route.php');
            JTable::addIncludePath($wsaJPATH_COMPONENT_ADMINISTRATOR . '/tables');
            // einde overgenomen uit newsfeeds.php
            // uit content.php
            JLoader::register($wsaComponent . 'HelperQuery', $wsaJPATH_COMPONENT . '/helpers/query.php');
            JLoader::register($wsaComponent . 'HelperAssociation', $wsaJPATH_COMPONENT . '/helpers/association.php');
            // einde overgenomen uit content.php
            // load default language file for this component to translate labels of form but maybe also other labes 
            Factory::getLanguage()->load($item->query['option']);
            // add file include path for this component.
            BaseDatabaseModel::addIncludePath($wsaJPATH_COMPONENT . '/models', $wsaComponent . 'Model'); 
            // instantiate controller,  propbably that of page component because it will only be instanciated once, but methods are available this way.
            $controller = BaseController::getInstance($wsaComponent);
            // don't use $config = array('ignore_request'=>true) because we want initial to populateState by first call of getState, with some components we may pass filter or other options in the $config array.
            $wsaModel = BaseDatabaseModel::getInstance(ucfirst($item->query['view']), $wsaComponent . 'Model');
            if ($wsaModel) {
                // initial call getState to populate $state, params are from $app->getParams() ie the page components params so we have to overwrite them
                $state = $wsaModel->getState();
                echo '<!-- $wsaModel get instance na eerste getState :', PHP_EOL;
                print_r($state);
                echo ' -->', PHP_EOL;
                $wsaModel->setState('parameters.menu', $wsaMenuParams); // TODO wordt in getModel in BaseController in ModelState gezet indien menu actief is (echter de vraag is of die nu wel aangeroepen wordt) kijk ook naar createModel
                                                                        // $wsaModel->setState($item->query['view'] . '.id', (int) $item->query['id'] ); // haal id uit $item in plaats van uit $input.
                $wsaModel->setState('params', $wsaComponentParams);
                if ($item->query['option'] == 'com_contact') {
                    // add formpaths relative to varible active component path
                    Form::addFormPath($wsaJPATH_COMPONENT . '/models/forms');
                    Form::addFieldPath($wsaJPATH_COMPONENT . '/models/fields');
                    Form::addFormPath($wsaJPATH_COMPONENT . '/model/form');
                    Form::addFieldPath($wsaJPATH_COMPONENT . '/model/field');
                }
                // TODO mabe we can use the controllers dispaly method if we have sufficient paths an properties set to values of this component/ menu-item.
                wsaDisplay(false, array(), $controller, $item->query['view'],  $wsaComponent . 'View', $wsaJPATH_COMPONENT, 'html',  'default', $wsaModel);
                        }
                        else echo '<!-- Model for component: ' , $item->query['option'] , ' not found! -->', PHP_EOL , '<div>', 'Model voor ' , $item->query['option'] ,' niet gevonden', '</div>' , PHP_EOL ;
                        
        echo '</div></div></div>' , PHP_EOL;
        echo '</section>', PHP_EOL;
        // tijdelijke aanpassing $app->input herstellen 
        $input    = clone  $wsaOrgInput;
        
    } // end if
    
} catch (Exception $e) {
        echo '<!-- '. PHP_EOL;
        echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
        echo ' -->', PHP_EOL;
        Factory::getApplication()->enqueueMessage(Text::_('Caught exception: ' .  $e->getMessage()), 'warning');
        
        
        
}
}  // end foreach
/*
 *  end list of sections.
 */
// restore $app->params()
$tmp = $app->getParams()->flatten();
foreach ($tmp as $tmpKey => $tmpVal) {
    $app->getParams()->remove($tmpKey);}
$app->getParams()->merge($wsaOrgAppParams);
// restore input
$input    = clone  $wsaOrgInput;
// restore active menu
if ($wsaOrgActiveMenuItem->id > 0){
    $app->getMenu()->setActive($wsaOrgActiveMenuItem->id);
    echo '<!-- herstelde actief menu id :', $app->getMenu()->getActive()->id ,' -->';
}
echo '<!-- einde onepage sections uit menu -->'. PHP_EOL;

?>