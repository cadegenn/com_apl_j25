<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo JText::_('COM_APL_IMPORTED_LABEL'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_APL_NOM_LABEL'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_APL_STATUS_LABEL'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_APL_CATEGORIE_LABEL'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_APL_CHANTIER_LIEU_LABEL'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_APL_PAYS_LABEL'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_APL_HEADING_ID'); ?>
	</th>
</tr>
