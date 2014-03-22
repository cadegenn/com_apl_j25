<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_apl&layout=edit&id='.(int) $this->item->id); ?>"
      method="post" name="adminForm" id="chantier-form">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_APL_CHANTIER_DETAILS' ); ?></legend>
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset() as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
				<!-- <pre><?php //var_dump($field);?></pre> -->
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<div>
		<input type="hidden" name="task" value="chantier.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
