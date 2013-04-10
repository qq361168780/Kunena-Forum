<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

$user = JFactory::getUser();

?>

<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->listOrdering; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=plugins'); ?>" method="post" name="adminForm" id="adminForm">
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $this->listOrdering; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirection; ?>" />
			<?php echo JHtml::_('form.token'); ?>

			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN');?></label>
					<input type="text" name="filter_search" id="filter_search" class="filter" placeholder="<?php echo JText::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>" value="<?php echo $this->filterSearch; ?>" title="<?php echo JText::_('COM_KUNENA_CATEGORIES_FIELD_INPUT_SEARCHCATEGORIES'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button class="btn tip" type="submit" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT'); ?>"><i class="icon-search"></i> <?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?></button>
					<button class="btn tip" type="button" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?>" onclick="jQuery('.filter').val('');jQuery('#adminForm').submit();"><i class="icon-remove"></i> <?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERRESET'); ?></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo KunenaLayout::factory('pagination/limitbox')->set('pagination', $this->pagination); ?>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
					<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
						<?php echo JHtml::_('select.options', $this->sortDirectionFields, 'value', 'text', $this->listDirection);?>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
						<?php echo JHtml::_('select.options', $this->sortFields, 'value', 'text', $this->listOrdering);?>
					</select>
				</div>
				<div class="clearfix"></div>
			</div>
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>
						<th width="1%" class="hidden-phone">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'enabled', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th class="title">
							<?php echo JHtml::_('grid.sort', 'Title', 'name', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="15%" class="nowrap hidden-phone">
							<?php echo JHtml::_('grid.sort', 'Element', 'element', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="10%" class="hidden-phone center">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access', $this->listDirection, $this->listOrdering); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'extension_id', $this->listDirection, $this->listOrdering); ?>
						</th>
					</tr>
					<tr>
						<td class="hidden-phone">
						</td>
						<td class="nowrap center">
							<label for="filter_enabled" class="element-invisible"><?php echo JText::_('All');?></label>
							<select name="filter_enabled" id="filter_enabled" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', $this->publishedOptions(), 'value', 'text', $this->filterEnabled, true); ?>
							</select>
						</td>
						<td class="nowrap">
							<label for="filter_title" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_title" id="filter_title" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterTitle; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
						</td>
						<td class="nowrap center">
							<label for="filter_element" class="element-invisible"><?php echo JText::_('COM_KUNENA_FIELD_LABEL_SEARCHIN'); ?></label>
							<input class="input-block-level input-filter filter" type="text" name="filter_element" id="filter_element" placeholder="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" value="<?php echo $this->filterElement; ?>" title="<?php echo JText::_('COM_KUNENA_SYS_BUTTON_FILTERSUBMIT') ?>" />
						</td>
						<td class="nowrap center">
							<label for="filter_access" class="element-invisible"><?php echo JText::_('All');?></label>
							<select name="filter_access" id="filter_access" class="select-filter filter" onchange="Joomla.orderTable()">
								<option value=""><?php echo JText::_('COM_KUNENA_FIELD_LABEL_ALL');?></option>
								<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->filterAccess, true); ?>
							</select>
						</td>
						<td class="nowrap center hidden-phone">
						</td>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="10">
							<?php echo KunenaLayout::factory('pagination/footer')->set('pagination', $this->pagination); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					foreach ($this->items as $i => $item) :
					$canEdit    = $user->authorise('core.edit', 'com_plugins');
					$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_plugins') && $canCheckin;
					?>
					<tr>
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->extension_id); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->enabled, $i, 'plugins.', $canChange); ?>
						</td>
						<td>
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'plugins.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_kunena&task=plugin.edit&extension_id='.(int) $item->extension_id); ?>">
									<?php echo $item->name; ?></a>
							<?php else : ?>
									<?php echo $item->name; ?>
							<?php endif; ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo $this->escape($item->element);?>
						</td>
						<td class="small hidden-phone center">
							<?php echo $this->escape($item->access_level); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->extension_id;?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
