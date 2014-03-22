<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
 
// load tooltip behavior
JHtml::_('behavior.tooltip');

$user           = JFactory::getUser();
$userId         = $user->get('id');
$listOrder      = $this->escape($this->state->get('list.ordering'));
$listDirn       = $this->escape($this->state->get('list.direction'));
//$saveOrder      = $listOrder == 'a.ordering';

?>

<form action="<?php echo JRoute::_('index.php?option=com_apl&view=chantiers'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltrt">
			<!-- PUBLISHED FILTER -->
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			<!-- CATEGORIES FILTER -->
			<select name="filter_categorie_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORIE');?></option>
				<?php echo JHtml::_('select.options', $this->categories, 'value', 'text', $this->state->get('filter.categorie_id'));?>
			</select>
			<!-- PAYS FILTER -->
			<select name="filter_countrycode_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PAYS');?></option>
				<?php echo JHtml::_('select.options', $this->pays, 'value', 'text', $this->state->get('filter.countrycode_id'));?>
			</select>
			<!-- USER FILTER -->
			<select name="filter_author_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_AUTHOR');?></option>
				<?php echo JHtml::_('select.options', $this->authors, 'value', 'text', $this->state->get('filter.author_id'));?>
			</select>
		</div>
	</fieldset>
	<table class="adminlist">
		<thead><tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
			</th>			
			<th>
				<?php //echo JText::_('COM_APL_NOM_LABEL'); ?>
				<?php echo JHtml::_('grid.sort', 'COM_APL_NOM_LABEL', 'LOWER(ch.nom)', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php //echo JText::_('COM_APL_STATUS_LABEL'); ?>
				<?php echo JHtml::_('grid.sort', 'COM_APL_CHANTIER_DATETEXT_LABEL', 'date_debut', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php //echo JText::_('COM_APL_STATUS_LABEL'); ?>
				<?php echo JHtml::_('grid.sort', 'COM_APL_STATUS_LABEL', 'published', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php //echo JText::_('COM_APL_CATEGORIE_LABEL'); ?>
				<?php echo JHtml::_('grid.sort', 'COM_APL_CATEGORIE_LABEL', 'LOWER(categorie)', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php //echo JText::_('COM_APL_CHANTIER_LIEU_LABEL'); ?>
				<?php echo JHtml::_('grid.sort', 'COM_APL_CHANTIER_LIEU_LABEL', 'LOWER(lieu)', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php //echo JText::_('COM_APL_PAYS_LABEL'); ?>
				<?php echo JHtml::_('grid.sort', 'COM_APL_PAYS_LABEL', 'LOWER(pays)', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php //echo JText::_('COM_APL_HEADING_ID'); ?>
				<?php echo JHtml::_('grid.sort', 'COM_APL_HEADING_ID', 'id', $listDirn, $listOrder); ?>
			</th>
		</tr></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<pre>
    <?php //echo var_dump($_POST); ?>
    <?php //echo var_dump($_GET); ?>
    <?php //echo var_dump($this); ?>
</pre>