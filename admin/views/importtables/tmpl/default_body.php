<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$user           = JFactory::getUser();
$userId         = $user->get('id');
//$listOrder      = $this->escape($this->status->get('list.ordering'));
//$listDirn       = $this->escape($this->status->get('list.direction'));
//$saveOrder      = $listOrder == 'a.ordering';
?>
<?php foreach($this->items as $i => $item):
	/*$item->max_ordering = 0; //??
	//$ordering       = ($listOrder == 'a.ordering');
	$canCreate      = $user->authorise('core.create',		'com_apl.category.'.$item->catid);
	$canEdit        = $user->authorise('core.edit',			'com_apl.chantier.'.$item->id);
	$canCheckin     = $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
	//$canEditOwn     = $user->authorise('core.edit.own',		'com_apl.chantier.'.$item->id) && $item->created_by == $userId;
	$canChange      = $user->authorise('core.edit.state',	'com_apl.chantier.'.$item->id) && $canCheckin;*/
	?>
	<tr class="row<?php echo $i % 2; ?>">
        <td>
			<?php echo JHtml::_('grid.id', $i, $item->Tables_in_apasdelo2011); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_apl&view=importTable&layout=edit&id=' . $item->Tables_in_apasdelo2011); ?>"><?php echo $item->Tables_in_apasdelo2011; ?></a>    <?php // => administrator/components/com_apl/views/importtable/tmpl/edit.php --via--> administrator/components/com_apl/models/importtable.php ?>
		</td>
		<td>
			
		</td>
	</tr>
<?php endforeach; ?>
