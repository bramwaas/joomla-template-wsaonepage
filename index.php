<?php defined('_JEXEC') or die;
/*
 * @copyright  Copyright (C) 2020 - 2020 AHC Waasdorp. All rights reserved.
 * @license    GNU/GPL, see LICENSE
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 10-5-2020 eerste aanpassingen richting onepage
 * 29-6-2020 inhoud newsfeed werkt voorlopig naar tevredenheid.
 * 13-7-2020 all component types content, tags, newsfeeds, and contac work, introduces module position-9 behind content and display=menu or content
 * 10-8-2020 removed example sections to work with com_wsaonepage for a bs4 onepagemenu with bookmarks to the componenent content of menuitems with #op# in note
*/

// copied from cassiopeia
use Joomla\CMS\Factory;   // this is the same as use Joomla\CMS\Factory as Factory
//use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;   // voor vertalingen???
// end copied from cassiopeia
/** @var JDocumentHtml $this */

$app  = Factory::getApplication();
$lang = Factory::getLanguage();

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu->params->get('pageclass_sfx');

// end copied from cassiopeia

// Get the template, params and id 
$template = $app->getTemplate(true);
$templateparams  = $template->params;
$templatestyleid =  $template->id;  
$displaySitename = htmlspecialchars($templateparams->get('displaySitename')); // 1 yes 2 no 


$bg0Color    	= htmlspecialchars($this->params->get('bg0Color'));

$bg1Image    	= htmlspecialchars($this->params->get('bg1Image'));
$bg1Image_lg    	= htmlspecialchars($this->params->get('bg1Image_lg'));
$bg1Breakpoint_lg    	= htmlspecialchars($this->params->get('bg1Breakpoint_lg'));
$bg1Image_sm    	= htmlspecialchars($this->params->get('bg1Image_sm'));
$bg1Breakpoint_sm    	= htmlspecialchars($this->params->get('bg1Breakpoint_sm'));

$bg1Width    	= htmlspecialchars($this->params->get('bg1Width'));
$bg1Top      	= htmlspecialchars($this->params->get('bg1Top'));
$bg1Left      	= htmlspecialchars($this->params->get('bg1Left'));
$bg1Color    	= htmlspecialchars($this->params->get('bg1Color'));
$bg1ImageW    	= htmlspecialchars($this->params->get('bg1ImageW'));
$bg1ImageH    	= htmlspecialchars($this->params->get('bg1ImageH'));
$bg1Image_lgW  	= htmlspecialchars($this->params->get('bg1Image_lgW'));
$bg1Image_smW  	= htmlspecialchars($this->params->get('bg1Image_smW'));


$wsaCssFilename = strtolower(htmlspecialchars($this->params->get('wsaCssFilename')));
if ($wsaCssFilename > " ")
{$path_parts = pathinfo($wsaCssFilename);
if (path_parts['extension'] <> 'css'){$wsaCssFilename = $wsaCssFilename . '.css';};
}
else
{ $wsaCssFilename = 'template.min.' . $templatestyleid . '.css';}

$twbs_version 		= htmlspecialchars($this->params->get('twbs_version', '4'));
$include_twbs_css	= htmlspecialchars($this->params->get('include_twbs_css', '1'));
$include_twbs_js	= htmlspecialchars($this->params->get('include_twbs_js','1'));
$wsaTime            = htmlspecialchars($this->params->get('wsaTime',''));
$wsaTime 			= strtr($wsaTime, array(' '=> 't', ':' => '' ));
$wsaNavbarExpand = htmlspecialchars($this->params->get('wsaNavbarExpand', 'navbar-expand-md'));
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" prefix="og: http://ogp.me/ns#  fb: http://www.facebook.com/2008/fbml" >
<head>
<jdoc:include type="head" />
<?php
echo '<!-- base is ' . $this->getBase() .' $templatestyleid ='.$templatestyleid .'  -->';


// Add extra metadata
$this->setMetaData( 'X-UA-Compatible', 'IE=edge', true ); // http-equiv = true 
$this->setMetaData( 'viewport', 'width=device-width, initial-scale=1.0, shrink-to-fit=no' );
// stylesheets
$this->addStyleSheet('https://fonts.googleapis.com/css?family=Open+Sans+Condensed:700' , array('version'=>'auto'), array('id'=>'googleapis-fonts.css'));
// bootstrap stylesheets van cdn

// alleen nog twbs 4
if ($include_twbs_css == "1") {
    
	$this->addStyleSheet('https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css', array('version'=>'4.5.0'), 
	    array('id'=>'bootstrap.min.css', 'integrity' => 'sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk', 'crossorigin' => 'anonymous'));
	}
	

// template stijl en scrolling nav van startbootstrap
$this->addStyleSheet('templates/' . $this->template . '/css/' . $wsaCssFilename , array('version'=>$wsaTime), array('id'=>'template.css'));
$this->addStyleSheet('templates/' . $this->template . '/css/' . 'scrolling-nav.css' , array('version'=>$wsaTime), array('id'=>'scrolling-nav.css'));
// Add JavaScript 

//HTMLHelper::_('jquery.framework');  // to be sure that jquery is loaded before dependent javascripts

// alleen nog twbs 4
if ($include_twbs_js == "1") {
//    $this->addScript('https://code.jquery.com/jquery-3.5.1.slim.min.js', array('version'=>'3.5.1'),
//        array('id'=>'jquery.js', 'integrity' => 'sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj', 'crossorigin' => 'anonymous'));
    $this->addScript('https://code.jquery.com/jquery-3.5.1.min.js', array('version'=>'3.5.1'),
        array('id'=>'jquery.js', 'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=', 'crossorigin' => 'anonymous'));
    $this->addScript('https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js', array('version'=>'1.16.0'),
        array('id'=>'popper.js', 'integrity' => 'sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo', 'crossorigin' => 'anonymous'));
    $this->addScript('https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', array('version'=>'4.5.0'),
        array('id'=>'bootstrap.min.js', 'integrity' => 'sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI', 'crossorigin' => 'anonymous'));
}

$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/magnificpopup/MagnificPopupV1-1-0.js', array('version'=>'1-1-0'), array('id'=>'MagnificPopupV1-1-0.js', 'defer'=>'defer'));
$this->addScript($this->baseurl  . '/media/system/js/caption.js' , array('version'=>'auto'), array('id'=>'caption.js', 'defer'=>'defer')); // defer caption.js. 

// van startbootstrap: <!-- Bootstrap core JavaScript -->
//$this->addScript($this->baseurl . '/templates/' . $this->template . '/vendor/jquery/jquery.min.js', array('version'=>'xxx'), array('id'=>'jquery.min.js', 'defer'=>'defer'));
//$this->addScript($this->baseurl . '/templates/' . $this->template . '/vendor/bootstrap/js/bootstrap.bundle.min.js', array('version'=>'4.3.1'), array('id'=>'bootstrap.bundle.min.js', 'defer'=>'defer'));
// <!-- Plugin JavaScript -->
$this->addScript($this->baseurl . '/templates/' . $this->template . '/vendor/jquery-easing/jquery.easing.min.js', array('version'=>'xxx'), array('id'=>'jquery.easing.min.js', 'defer'=>'defer'));
// van template, samengevoegd met scrolling nav van startbootstrap	
// evt ipv ease ui
// <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"     integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="     crossorigin="anonymous"></script>


$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/scrolling-nav.js', array('version'=>'auto'), array('id'=>'scrolling-nav.js', 'defer'=>'defer'));
$this->addScript($this->baseurl . '/templates/' . $this->template . '/js/template.js', array('version'=>'auto'), array('id'=>'template.js', 'defer'=>'defer'));


$this->addScriptDeclaration('jQuery(document).ready(function() {
  jQuery(\'a[rel*="lightbox"], a[data-wsmodal]\').magnificPopup({
type: \'image\'
, closeMarkup : \'<button title="%title%" type="button" class="mfp-close">&nbsp;</button>\'
});})');  
// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8'))
{
	$spanc = "span6  col-md-6" ;
	$spans = "span3  col-md-3";
}
elseif (!$this->countModules('position-7') || !$this->countModules('position-8'))
    
{
	$spanc = "span8  col-md-8";
	$spans = "span4  col-md-4";
}
else
{
	$spanc = "span12  col-12";
}
$hi_mods = ($this->countModules('position-0')? ' hipos0': '')
. ($this->countModules('icons')? ' hiicons': '')
. ($this->countModules('headerleft')? ' hihl': '')
. ($this->countModules('position-4')? ' hipos4': '')
. ($this->countModules('position-5')? ' hipos5': '')
. ($this->countModules('position-6')? ' hipos6': '')
;
$cnt_mods = ($this->countModules('position-1')? ' cntpos1': '')
. ($this->countModules('position-2')? ' cntpos2': '')
. ($this->countModules('position-3')? ' cntpos3': '')
. ($this->countModules('position-7')? ' cntpos7': '')
. ($this->countModules('position-8')? ' cntpos8': '')
. ($this->countModules('message')? ' cntmsg': ''); 
?>

<!--[if lt IE 9]>
<script src="<?php echo $this->baseurl ?>/media/jui/js/html5.js"></script>
<![endif]-->
<!--[if lte IE 7]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template_IEold.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if IE 8]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template_IE8.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if IE 9]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template_IE9.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>
<body id="<?php echo ($itemid ? 'itemid-' . $itemid : ''); ?>"
data-spy="scroll" data-target=".navbar" data-offset="50"
<?php // added from cassiopeia ?>
class="site-grid site <?php echo $pageclass;
	echo ($this->direction == 'rtl' ? ' rtl' : '');
	
?>"

>

<!-- Begin Container-->
	<div id="wrapper" class="container">
<?php if ($bg1Image > " " )
{ echo "\n" . '<img id="img_bg1Image" src="' . $bg1Image . '" alt="Background image content"';
	if ($bg1ImageW > 0 ) {echo "\n\t" . 'width="' . $bg1ImageW .'"';}
	if ($bg1ImageH > 0 ) {echo "\n\t" . 'height="' . $bg1ImageH . '"';}
	if ($bg1ImageW > 0  && (($bg1Image_lg > " " && $bg1Image_lgW > 0) || ($bg1Image_sm > " " && $bg1Image_smW > 0))  )
	{echo "\n\t" . 'srcset="' . $bg1Image . ' ' . $bg1ImageW .'w'   ;
	if ($bg1Image_lgW > 0) {echo ','. $bg1Image_lg .' ' . $bg1Image_lgW . 'w' ; }
	if ($bg1Image_smW > 0) {echo ','. $bg1Image_sm .' ' . $bg1Image_smW . 'w' ; }
	echo '"';
	if ($bg1Breakpoint_lg > 0 || $bg1Breakpoint_sm > 0)
		{echo "\n\t" . 'sizes="';
		if ($bg1Breakpoint_sm > 0 ) {echo '(max-width: ' . $bg1Breakpoint_sm .'px) '.$bg1Image_smW .'px,'; }
		if ($bg1Breakpoint_lg > 0 ) {echo '(min-width: ' . $bg1Breakpoint_lg .'px) '.$bg1Image_lgW .'px,'; }
		echo $bg1ImageW .'px"'; 
		}
	} 
 echo  ' />' . "\n";
}?>

<div id="wrapper1">

		<!-- Begin Header  -->
		<div class="header">
			<div class="header-inner container-content<?php echo $hi_mods; ?>">
				<?php if ($this->countModules('position-0')): ?>
				<div class="pos0 row">
					<jdoc:include type="modules" name="position-0" style="wsaOnepage" display="menu" />
					<div class="clearfix"></div>
				</div><!--End Pos0-->
				<?php endif; ?>
 				<?php if(  $this->countModules('icons'))    : ?>
				<div id="icons" class="iconssm <?php  echo $wsaNavbarExpand;   ?> row">
					<jdoc:include type="modules" name="icons" />
				</div><!--End Icons-->   
				<?php endif; ?>
				<?php if(  $this->countModules('headerleft'))    : ?>
				<div id="headerleft ">
					<div class="inner d-flex justify-content-between row">
						<jdoc:include type="modules" name="headerleft"  />
					<div class="clearfix"></div>
					</div>
				</div><!--einde headerleft-->  
  				<?php endif; ?>
				<?php if ($this->countModules('position-4')): ?>
				<div class="pos4 row">
					<jdoc:include type="modules" name="position-4" style="none" />
					<div class="clearfix"></div>
				</div><!--End Pos4-->
				<?php endif; ?>
				<?php if ($this->countModules('position-5')): ?>
				<div class="pos5 row bigimage">
					<jdoc:include type="modules" name="position-5" style="none" />
					<div class="clearfix"></div>
				</div><!--End Bigimage-->
				<?php endif; ?>
				<?php if ($this->countModules('position-6')): ?>
				<div class="pos6 row">
					<jdoc:include type="modules" name="position-6" style="none" />
					<div class="clearfix"></div>
				</div><!--End Pos6-->
				<?php endif; ?>
				<div class="clearfix"></div>
			</div><!--End Header-Inner-->
		</div><!--End Header-->



		<!-- Begin Container content-->

		<div class="container-content<?php echo $cnt_mods; ?>">
		    	<?php if ($this->countModules('position-1')): ?>
	            	    <jdoc:include type="modules" name="position-1" style="none" />
		    	<?php endif; ?>
			<div class="row">
				<?php if ($this->countModules('position-8')): ?>
				<div id="sidebarleft" class="pos8 <?php echo $spans;?>">
					<jdoc:include type="modules" name="position-8" style="well" /><!--End Position-8-->
				</div><!--End Sidebar Left-->
				<?php endif; ?>
				<div id="content" class="<?php echo $spanc;?>">
					<?php if ($this->countModules('position-2')): ?>
					<div class="pos2">
						<jdoc:include type="modules" name="position-2" style="none" />
						<div class="clearfix"></div>
					</div><!--End Pos2-->
					<?php endif; ?>
					<?php if ($this->countModules('position-3')): ?>
					<div class="pos3">
						<jdoc:include type="modules" name="position-3" style="none" />
						<div class="clearfix"></div>
					</div><!--End Pos3-->
					<?php endif; ?>
					<jdoc:include type="message" />
					<jdoc:include type="component" />
				<?php if ($this->countModules('position-9')): ?>
				<div class="pos9 row">
					<jdoc:include type="modules" name="position-9" style="wsaOnepage"  />
					<div class="clearfix"></div>
				</div><!--End Pos9-->
				<?php endif; ?>
				</div><!--Content -->
				<?php if ($this->countModules('position-7')) : ?>
				<div id="sidebarright" class="pos7 <?php echo $spans;?>">
					<jdoc:include type="modules" name="position-7" style="well" /><!--End Position-7-->
				</div><!--End Sidebar Right-->
				<?php endif; ?>
			</div><!--End Row-->
         
          </div><!--End Container Content-->
          
          
          
    <!--  einde main content menusturing door DIVs met ID -->
          
	<!-- Begin Footer -->
	<div class="footer">
			<hr />
			<jdoc:include type="modules" name="footer" style="none" />
			<p class="pull-right"><a href="#" id="back-top">&uarr; Top</a></p>
			<p>&copy; <?php echo $sitename; ?> <?php echo date('Y');?></p>
	</div>
    <!--End Footer-->
	</div> <!-- end wrapper1 -->
	</div><!--End Container-->
	<?php if ($this->countModules('messageIE')): ?>
	<!--[if lte IE 7]>
	<div class="message-ie"><jdoc:include type="modules" name="messageIE" style="none" /></div>
	<![endif]-->
	<?php endif; ?>
	<jdoc:include type="modules" name="debug" style="none" />
	
	
	
</body>
</html>
