<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
   bw 30-8-2016:        toegevoegd leesmeer bw wsa-leesmeer in artikel metadata xreference, omdat readmore uit item
in deze component niet wordt gevonden 
   bw 19-10-2016 controle op leesmeer verbeterd omdat bij upgrade naar 3.6.3 (met display errors) bleek dat de oude versie een fout genereerde.(Notice: Trying to get property of non-object in /home/deb53453/domains/asha-s.com/public_html/templates/asha-s/html/com_tags/tag/default_items.php on line 121 als gevolg van $this->item->readmore ipv $this->item[0]->readmore)
   bw 14-04-2017 kleine aanpassingen na vgl v 3.7.0rc2
   bw 30-09-2017 idem na vgl v3.8.0
   bw 7-1-2018 J4 namespaces gebruiken
   bw 16-5-2020 vergeleken met origineel en itemscope en itemprop properties toegevoegd waar die nog ontbraken.
   bw 10-10-2022 onderscheiden V4 en V3 nav erorr Class "Joomla\Component\Tags\Site\Helper\RouteHelper" not found
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;   // this is the same as use Joomla\CMS\Factory as Factory
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;  
use Joomla\CMS\Router\Route;
use Joomla\Component\Tags\Site\Helper\RouteHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HTMLHelper::_('behavior.core');
HTMLHelper::_('formbehavior.chosen', 'select');

// Get the user object.
$user = Factory::getUser();

// Check if user is allowed to add/edit based on tags permissions.
// Do we really have to make it so people can see unpublished tags???
$canEdit      = $user->authorise('core.edit', 'com_tags');
$canCreate    = $user->authorise('core.create', 'com_tags');
$canEditState = $user->authorise('core.edit.state', 'com_tags');
$items        = $this->items;
$n            = count($this->items);

Factory::getDocument()->addScriptDeclaration("
		var resetFilter = function() {
		document.getElementById('filter-search').value = '';
	}
");

?>
<!-- override tag/tmpl/default_items.php -->
<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php if ($this->params->get('show_headings') || $this->params->get('filter_field') || $this->params->get('show_pagination_limit')) : ?>
		<fieldset class="filters btn-toolbar">
			<?php if ($this->params->get('filter_field')) : ?>
				<div class="btn-group">
					<label class="filter-search-lbl element-invisible" for="filter-search">
						<?php echo  Text::_('COM_TAGS_TITLE_FILTER_LABEL') . '&#160;'; ?>
					</label>
					<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo Text::_('COM_TAGS_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo Text::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>" />
					<button type="button" name="filter-search-button" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>" onclick="document.adminForm.submit();" class="btn">
						<span class="icon-search"></span>
					</button>
					<button type="reset" name="filter-clear-button" title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" class="btn" onclick="resetFilter(); document.adminForm.submit();">
						<span class="icon-remove"></span>
					</button>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('show_pagination_limit')) : ?>
				<div class="btn-group pull-right">
					<label for="limit" class="element-invisible">
						<?php echo  Text::_('JGLOBAL_DISPLAY_NUM'); ?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			<?php endif; ?>
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" name="limitstart" value="" />
			<input type="hidden" name="task" value="" />
			<div class="clearfix"></div>
		</fieldset>
	<?php endif; ?>
	<?php if ($this->items === false || $n === 0) : ?>
		<p><?php echo  Text::_('COM_TAGS_NO_ITEMS'); ?></p>
	<?php else : ?>
		<ul class="category list-striped" itemscope itemtype="http://schema.org/ItemList">
			<?php foreach ($items as $i => $item) : ?>
				<?php if ($item->core_state == 0) : ?>
					<li class="system-unpublished cat-list-row<?php echo $i % 2; ?>" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<?php else : ?>
					<li class="cat-list-row<?php echo $i % 2; ?> clearfix" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<?php if (($item->type_alias === 'com_users.category') || ($item->type_alias === 'com_banners.category')) : ?>
						<h3 itemprop="name">
							<?php echo $this->escape($item->core_title); ?>
						</h3>
					<?php else : ?>
						<h3 itemprop="name">
							<a href="<?php echo Route::_($item->link); ?>" itemprop="url">
								<?php echo $this->escape($item->core_title); ?>
							</a>
						</h3>
					<?php endif; ?>
				<?php endif; ?>
				<?php // Content is generated by content plugin event "onContentAfterTitle" ?>
				<?php echo $item->event->afterDisplayTitle; ?>
				<?php $images = json_decode($item->core_images); ?>
				<?php if ($this->params->get('tag_list_show_item_image', 1) == 1 && !empty($images->image_intro)) : ?>
					<a href="<?php echo Route::_($item->link); ?>" itemprop="url">
						<img src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>" itemprop="image" />
					</a>
				<?php endif; ?>
				<?php if ($this->params->get('tag_list_show_item_description', 1)) : ?>
					<?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
					<?php echo $item->event->beforeDisplayContent; ?>
					<span class="tag-body" itemprop="description">
						<?php echo HTMLHelper::_('string.truncate', $item->core_body, $this->params->get('tag_list_item_maximum_characters')); ?>
					</span>
					<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
					<?php echo $item->event->afterDisplayContent; ?>
				<?php endif; ?>

                 <?php //  toegevoegd leesmeer bw als waarde van alternative_readmore is ingevuld, omdat readmore boolean waar ook op getest wordt uit item in deze component niet wordt gevonden ?>
                 <?php  if ( (json_decode($item->core_params)->alternative_readmore > "")
			or (isset($this->item[0]->readmore) and $this->item[0]->readmore)
                              )  :
                  ?>
            <p class="readmore"><a class="btn" href="<?php echo Route::_($item->link); ?>">
		<?php 
                        if (json_decode($item->core_params)->alternative_readmore > "") :
                               	echo  Text::_(json_decode($item->core_params)->alternative_readmore); 
                        else:                  
              			echo  Text::_('COM_CONTENT_READ_MORE');
			endif; ?>
              </a></p> 
                  
<?php endif;  // toegevoeg leesmeer bw einde ?>  
                  
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</form>
