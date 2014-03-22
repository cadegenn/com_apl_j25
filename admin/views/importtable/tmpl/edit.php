<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

// on charge les paramètres
$params = $this->form->getFieldsets('params'); // --> /administrator/components/com_apl/config.xml
$table_requested = (isset($_GET['id']) ? strval($_GET['id']) : "");
?>

<form action="<?php echo JRoute::_('index.php?option=com_apl&layout=edit&id='.$table_requested); ?>" method="post" name="adminForm" id="chantier-form">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_APL_IMPORTTABLE_DETAILS' ); ?></legend>
			<div class="tr"><div class="th">
				<span class="th"><?php echo $this->form->getField("nom_source", '', $_GET['id'])->label; ?></span>
				<span class="td"><?php echo $this->form->getField("nom_source")->input; ?></span>
				<span class="th"><?php echo $this->form->getField("nom_dest")->label; ?></span>
				<span class="td"><?php echo $this->form->getField("nom_dest")->input; ?></span>
			</div></div>
			<hr />
			<div class="tr">
				<div class="th"><?php echo JText::_('COM_APL_IMPORTTABLE_OPTIONS'); ?></div>
				<div class="td"><?php echo $this->form->getField("drop_table")->input." ".$this->form->getField("drop_table")->label; ?></div>
			</div>
		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<?php        echo JHtml::_('sliders.start', 'apl-slider');
             foreach ($params as $name => $fieldset):
                echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');
                if (isset($fieldset->description) && trim($fieldset->description)): ?>
					<p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
				<?php endif;?>
                <fieldset class="panelform" >
						<ul class="adminformlist">
							<?php foreach ($this->form->getFieldset($name) as $field) : ?>
                                <li><?php echo $field->label; ?><?php echo $field->input; ?></li>
							<?php endforeach; ?>
                        </ul>
                </fieldset>
			<?php endforeach; ?>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<div>
		<input type="hidden" name="task" value="chantier.edit" />
				<?php JFactory::getApplication()->setUserState('com_apl.edit.chantier.id', (int) $this->item->id); ?>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<div class="import_table_preview">
	<?php echo var_dump($this->display_import_table_preview($table_requested)); ?>
	<?php //foreach ($this->display_import_table_preview($table_requested) as $key => $value) : ?>
		<?php //echo("key = ".$key." value = ".$value."<br />"); ?>
	<?php //endforeach; ?>
</div>

<pre>$this :
    <?php echo var_dump($this); ?>
</pre>
<pre>
    <?php echo var_dump($params); ?>
</pre>