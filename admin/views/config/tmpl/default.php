<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
 
// Import library dependencies
JLoader::register('aplFunctions', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/functions.php');

// load tooltip behavior
JHtml::_('behavior.tooltip');
?>

    <?php
    
    if (isset($_GET['id'])) {
    }
?>

<form action="<?php echo JRoute::_('index.php?option=com_velo&view=importV2Chantiers'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead><?php //echo $this->loadTemplate('head');?></thead>
		<tfoot><?php //echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php //echo $this->loadTemplate('body');?></tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<pre>
    <?php //echo var_dump($_POST); ?>
    <?php //echo var_dump($_GET); ?>
    <?php //echo var_dump($this); ?>
</pre>