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
 * 6-6-2020 eerste geslaagde poging om content artikelen op te halen.
 * 9-6-2020 newsfeeds op algemene manier trachten op te halen.
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;   // this is the same as use Joomla\CMS\Factory as Factory
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry; // for new Registry
use Joomla\CMS\MVC\Model\BaseDatabaseModel;  // JModelLegacy
use Joomla\CMS\MVC\Model\FormModel;  // JModelForm

use Joomla\CMS\Component\ComponentHelper;  //tbv algemene renderComponent
use Joomla\CMS\MVC\Controller\BaseController; 

//use Joomla\CMS\HTML\HTMLHelper;
//use Joomla\CMS\Plugin\PluginHelper;
//use Joomla\CMS\Document\HtmlDocument;
//use Joomla\CMS\Document\Renderer\Html\ModulesRenderer;


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
$wsaOrgInputArray = $input->getArray(array());
$wsaOrgActiveMenuIdmenu     = $app->getMenu()->getActive();
$wsaOrgMenuQueryArray = $wsaOrgActiveMenuIdmenu->query;
$wsaOrgDocumentViewType = $document->getType();  //= html is altijd goed, dus niets mee doen
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
	
	 function wsaRenderComponent($option, $params = array())
	 { // tijdelijke kopie van ComponentHelper::renderComponent($item->query['option']);
	    $app = \JFactory::getApplication();
	    
	    // Load template language files.
	    $template = $app->getTemplate(true)->template;
	    $lang = \JFactory::getLanguage();
	    $lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
	    || $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, true);
	    
	    if (empty($option))
	    {
	        throw new MissingComponentException(\JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'), 404);
	    }
	    
	    if (JDEBUG)
	    {
	        \JProfiler::getInstance('Application')->mark('beforeRenderComponent ' . $option);
	    }
	    
	    // Record the scope
	    $scope = $app->scope;
	    
	    // Set scope to component name
	    $app->scope = $option;
	    
	    // Build the component path.
	    $option = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
	    $file = substr($option, 4);
	    
	    // Define component path.
	    if (!defined('JPATH_COMPONENT'))
	    {
	        define('JPATH_COMPONENT', JPATH_BASE . '/components/' . $option);
	    }
	    
	    if (!defined('JPATH_COMPONENT_SITE'))
	    {
	        define('JPATH_COMPONENT_SITE', JPATH_SITE . '/components/' . $option);
	    }
	    
	    if (!defined('JPATH_COMPONENT_ADMINISTRATOR'))
	    {
	        define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/' . $option);
	    }
	    
	    // $path = JPATH_COMPONENT . '/' . $file . '.php';
	    $path = JPATH_BASE . '/components/' . $option . '/'  . $file . '.php';
	    $contents = $path;
	    
	    // If component is disabled throw error
	    /*
	    if (!static::isEnabled($option) || !file_exists($path))
	    {
	        throw new MissingComponentException(\JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'), 404);
	    }
	    */
	    
	    // Load common and local language files.
	    $lang->load($option, JPATH_BASE, null, false, true) || $lang->load($option, JPATH_COMPONENT, null, false, true);
	    
	    // Handle template preview outlining.
	    $contents = null;
	    
	    // Execute the component.
	    $contents = wsaExecuteComponent($path);
	    
	    // Revert the scope
	    $app->scope = $scope;
	    
	    if (JDEBUG)
	    {
	        \JProfiler::getInstance('Application')->mark('afterRenderComponent ' . $option);
	    }
	    
	    return $contents;
	}
	/**
	 * Execute the component.
	 *
	 * @param   string  $path  The component path.
	 *
	 * @return  string  The component output
	 *
	 * @since   1.7
	 */
	function wsaExecuteComponent($path)
	{
	    ob_start();
	    require_once $path;
	    
	    return ob_get_clean();
	}
	
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
// voorlopig extra acties voor onepage sections
echo '<!-- onepage sections uit menu -->'. PHP_EOL;
echo '<!-- ';
// vastleggen variabelen huidige controller in verband met verwijderen verkeerde controller
echo 'controller: '. substr(5,$wsaOrgMenuQueryArray['option']) .PHP_EOL;
if ($controller = BaseController::getInstance(substr(5,$wsaOrgMenuQueryArray['option'])) ) 
{
$tmpOrgControllerVars = $controller->getProperties(FALSE);
//$controller->__destruct();  // functie bestaat niet
echo 'controller->getProperties(FALSE):' . PHP_EOL;
print_r($tmpOrgControllerVars);
echo '$controller->get(name): ' , $controller->get('name') , PHP_EOL ;
echo ' -->', PHP_EOL;
}

foreach ($list as $i => &$item) {
    echo '<!-- item->type=' , $item->type , ' item->level=' , $item->level ,  ' $item->title=' , $item->title , ' $item->flink=' , $item->flink,   ' $item->bookmark=' , $item->bookmark,' -->', PHP_EOL;  
    if ($item->type=='component' && $item->level==1) {
        echo '<section id="' , $item->bookmark  , '" class="container" >', PHP_EOL;
        echo '<!-- ';
 //       print_r($item);
        echo ' -->', PHP_EOL;
        echo '<div class="container"><div class="row"><div class="col-lg-8 mx-auto">', PHP_EOL;
        echo '<p>' , $item->title , '</p>' , PHP_EOL;
        echo '<p>' , ' $item->flink=' , $item->flink , ' $item->link=' , $item->link ,  PHP_EOL
        , ' $item->query[option]=' , $item->query['option'] , ' $item->query[view]=' , $item->query['view'] , ' $item->query[id]=' , $item->query['id'] , PHP_EOL ; 
//        foreach ( $item->query as $key => $value) {             echo " {$key} => {$value} " , PHP_EOL;         }
            switch ($item->query[view])
            {
                case 'article' :
                case 'featured' :    
        { 
            // voorbeeld modules / mod_articles_latest en https://stackoverflow.com/questions/19765160/loading-an-article-into-a-components-template-in-joomla
            // kijk ook naar components/com_content/models/articles
            $wsaModel=BaseDatabaseModel::getInstance('Articles', 'ContentModel', array('ignore_request'=>true));
//            $wsaModel=JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request'=>true));  // één artikel
            // Set application parameters in model
//            $app       = JFactory::getApplication();
            $wsaappParams = $app->getParams();
            $wsaModel->setState('params', $wsaappParams);
            $wsaModel->setState('list.ordering', 'a.publish_up');
            $wsaModel->setState('list.direction', 'DESC');
            
            if ($item->query['id'] > '0') {
            $wsaModel->setState('filter.article_id', (int) $item->query['id'] ); // or use array of ints for multiple articles
            }
            else // featured
            {
                $wsaModel->setState('list.start', 0);
                $wsaModel->setState('list.limit', 5);
                //  Featured switch
                $wsaModel->setState('filter.featured', 'only');
            }
            $wsaModel->setState('filter.published', 1);
            $wsaModel->setState('load_tags', true); // not available for Article model
            $wsaModel->setState('show_associations', true);
            $wsaContentItems=$wsaModel->getItems(); 
            //            $wsaContentItem=$wsaModel->getItem($item->query['id']); // Indien één Artikel gekozen met Article ipv Articles
            $wsaContentItem=$wsaContentItems[0];
//            foreach ($wsaContentItems as &$wsaContentItem)            {}; // als er meer artikelen zijn
                echo '<!-- ';
//                   print_r($article);
            echo ' -->', PHP_EOL;
            echo '<h3>', $wsaContentItem->title, '</h3>' , PHP_EOL ;
            echo '<div>', $wsaContentItem->introtext, '</div>' , PHP_EOL ;
            echo '<div>', $wsaContentItem->fulltext, '</div>' , PHP_EOL ;
       
            
        }
        break;
                case 'contact':
       
 { 
            // voorbeeld modules / mod_articles_latest en https://stackoverflow.com/questions/19765160/loading-an-article-into-a-components-template-in-joomla
            // kijk ook naar components/com_content/models/articles
 JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_contact/models', 'ContactModel'); // Is waarschijnlijk overbodig om com_content op te kunnen halen
     
             $wsaModel=FormModel::getInstance('Contact', 'ContactModel', array('ignore_request'=>true));
            //            $wsaModel=JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request'=>true));  // één artikel
            // Set application parameters in model
            //            $app       = JFactory::getApplication();
            if ($wsaModel) {
            $wsaappParams = $app->getParams();
            $wsaModel->setState('params', $wsaappParams);
            $wsaModel->setState('contact.id', (int) $item->query['id'] ); 
            $wsaModel->setState('load_tags', true); // not available for Article model
            $wsaModel->setState('show_associations', true);
//            $wsaContentItems=$wsaModel->getItems();
            $wsaContentItem=$wsaModel->getItem($item->query['id']); // Indien één Artikel gekozen met Article ipv Articles
//            $wsaContentItem=$wsaContentItems[0];
            //            foreach ($wsaContentItems as &$wsaContentItem)            {}; // als er meer artikelen zijn
            echo '<!-- ';
            //                   print_r($wsaContentItem);
            echo ' -->', PHP_EOL;
            echo '<h3>', $wsaContentItem->title, '</h3>' , PHP_EOL ;
            echo '<div>', $wsaContentItem->name, '</div>' , PHP_EOL ;
            echo '<div>', $wsaContentItem->email, '</div>' , PHP_EOL ;
            echo '<div>', $wsaContentItem->alias, '</div>' , PHP_EOL ;
            }
            else echo '<div>', 'Model voor Contact niet gevonden', '</div>' , PHP_EOL ;
            
         
        }
        break;
                case 'newsfeed':
                    {
                        
                        echo '<h3>',  $item->title, '</h3>' , PHP_EOL ;
                        echo '<div>', ' $item->bookmark=' , $item->bookmark, ' $item->query[option]=' , $item->query['option'] ,' .</div>' , PHP_EOL ;
                        echo '<!-- Newsfeed:', PHP_EOL ;
                        echo '-->', PHP_EOL ;
                        // aangepaste versie van componentpath ed in variabelen in plaats van constantes.
                            $wsaJPATH_COMPONENT = JPATH_BASE . '/components/' . $item->query['option'];
                            $wsaJPATH_COMPONENT_SITE = JPATH_SITE . '/components/' . $item->query['option'];
                            $wsaJPATH_COMPONENT_ADMINISTRATOR = JPATH_ADMINISTRATOR . '/components/' . $item->query['option'];
                      
                        // voorbeeld modules / mod_articles_latest en https://stackoverflow.com/questions/19765160/loading-an-article-into-a-components-template-in-joomla
                        // kijk ook naar components/com_content/models/articles
 //                       Uit newsfeeds.php
/*                         JLoader::register('NewsfeedsHelperRoute', JPATH_COMPONENT . '/helpers/route.php');
                        JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
                        
                        $controller = JControllerLegacy::getInstance('Newsfeeds');
                        $controller->execute(JFactory::getApplication()->input->get('task'));
                        $controller->redirect(); */
//                      einde uit newsfeeds.php 
                        
                            BaseDatabaseModel::addIncludePath($wsaJPATH_COMPONENT . '/models', 'NewsfeedsModel'); // Is waarschijnlijk overbodig om com_content op te kunnen halen
                        // controller beschikbaar maken, is waarschijnlijk die van de hoofdcomponent, omdat hij maar een keer wordt geinstancieerd, maar basisfuncties zijn zo beschikbaar.
                        $controller = BaseController::getInstance('Newsfeeds');

/*                         -                        // verwijderen verkeerde controller
                        -                        $controller = BaseController::getInstance('Newsfeeds');
                        -
                        -                        // welke view ?
                        -                        $document = Factory::getDocument();
                        -                        $viewType = $document->getType();
                        -                        $viewName = $controller->get('input')->get('view', $controller->get('default_view'));
                        -                        $viewLayout = $controller->get('input')->get('layout', 'default', 'string');
                        -
                        -                        echo ' voor aanpassingeen $viewType=' , $viewType , ' $viewName' , $viewName , ' $viewLayout' , $viewLayout , PHP_EOL;
                        -
                        -
                        -                        $controller->set('basePath',  '/home/deb120151/domains/waasdorpsoekhan.nl/public_html/components/com_newsfeeds');
                        -                        $controller->set('default_view',  'newsfeed');
                        -                        $controller->set('name',  'newsfeeds');
                        -                        $controller->set('model_prefix',  'NewsfeedsModel');
                        -                        $controller->set('paths',  array('view' => '/home/deb120151/domains/waasdorpsoekhan.nl/public_html/components/com_newsfeeds/views/' ));
                        -                        echo 'Newsfeeds $controller->getProperties(FALSE); :' . PHP_EOL;
                        -                        print_r($controller->getProperties(FALSE));
                        -               echo PHP_EOL;
                        -                         // tijdelijk aanpassen $app->input
                        -
                        -                        foreach ($wsaOrgMenuQueryArray as $tmpKey => $tmpVal) {
                            -                            $app->input->set($tmpKey,NULL);
                        
 */ 
                        $wsaModel=BaseDatabaseModel::getInstance('Newsfeed', 'NewsfeedsModel', array('ignore_request'=>true));
                        //            $wsaModel=JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request'=>true));  // één artikel
                        // Set application parameters in model
                        //            $app       = JFactory::getApplication();
                        if ($wsaModel) {
                            $wsaappParams = $app->getParams();
                            $wsaModel->setState('params', $wsaappParams);
                            $wsaModel->setState('newsfeed.id', (int) $item->query['id'] );
                            $wsaModel->setState('load_tags', true); // not available for Article model
                            $wsaModel->setState('show_associations', true);
                            //            $wsaContentItems=$wsaModel->getItems();
                            $wsaContentItem=$wsaModel->getItem($item->query['id']); // Indien één Artikel gekozen met Article ipv Articles
                            //            $wsaContentItem=$wsaContentItems[0];
                            //            foreach ($wsaContentItems as &$wsaContentItem)            {}; // als er meer artikelen zijn
                            echo '<!-- $wsaContentItem ', PHP_EOL;
                                        print_r($wsaContentItem);
                            echo ' -->', PHP_EOL;
                            echo '<h3>', $wsaContentItem->title, '</h3>' , PHP_EOL ;
                            echo '<div>', $wsaContentItem->name, '</div>' , PHP_EOL ;
 //                           echo '<div>', $wsaContentItem->email, '</div>' , PHP_EOL ;
 //                           echo '<div>', $wsaContentItem->alias, '</div>' , PHP_EOL ;

/*                             -                        foreach ($item->query as $tmpKey => $tmpVal) {
                                -                            $app->input->set($tmpKey,$tmpVal);
                                -                        }
                                -
                                -                        echo '$app->input:' . PHP_EOL;
                                -
                                -                        print_r($app->input);
                                - //                       echo '$wsaOrgInputArray:' . PHP_EOL;
                                - //                       print_r($wsaOrgInputArray);
                                - //                       echo '$wsaOrgMenuQueryArray:' . PHP_EOL;
                                - //                       print_r($wsaOrgMenuQueryArray );
                                - //
                                -                        $viewName = $controller->get('input')->get('view', $controller->get('default_view'));
                                -                        $viewLayout = $controller->get('input')->get('layout', 'default', 'string');
                                -                        echo ' Na aanpassingeen $viewType=' , $viewType , ' $viewName=' , $viewName , ' $viewLayout=' , $viewLayout , PHP_EOL;
                                -
                                -                        echo ' -->', PHP_EOL;
                                -                         // $wsaComponent = ComponentHelper::renderComponent($item->query['option']);
                                -                        echo '<!-- ';
                                -                        //                                   print_r($wsaComponent);
                                -                        echo ' -->', PHP_EOL;
                                -                        $wsaComponent = wsaRenderComponent ($item->query['option']);
                                -                        echo $wsaComponent;
                                -                        // tijdelijke aanpassing $app->input herstellen
                                -                         foreach ($item->query  as $tmpKey => $tmpVal) {
                                    -                            $app->input->set($tmpKey,NULL);
                                    -                        }
                                    -                        $app->input->set ('Itemid', $Itemid); // waarschijnlijk overbodig
                                    -                        foreach ($wsaOrgMenuQueryArray as $tmpKey => $tmpVal) {
                                        -                            $app->input->set($tmpKey,$tmpVal);
                                        -                        }
 */                        
                        
                        
                        
                        }
                        else echo '<div>', 'Model voor Newsfeed niet gevonden', '</div>' , PHP_EOL ;
                        
                    }
       break;             
                default:
                    {  
                        echo '<h3>',  $item->title, '</h3>' , PHP_EOL ;
                        echo '<div>', ' $item->bookmark=' , $item->bookmark, ' $item->query[option]=' , $item->query['option'] ,' nog onbekend option type, component inhoud niet verwerkt.</div>' , PHP_EOL ;
                    }
       
            } // end switch
        echo '</p>' , PHP_EOL;
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