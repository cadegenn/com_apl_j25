<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width="5">
		<?php echo JText::_('COM_APL_CHANTIERS_HEADING_ID'); ?>
	</th>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo JText::_('COM_APL_CHANTIER_NOM_LABEL'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_APL_CHANTIER_LIEU_LABEL'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_APL_CHANTIER_PAYS_LABEL'); ?>
	</th>
</tr>
