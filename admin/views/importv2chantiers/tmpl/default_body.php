<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

/*$user           = JFactory::getUser();
$userId         = $user->get('id');
//$listOrder      = $this->escape($this->published->get('list.ordering'));
//$listDirn       = $this->escape($this->published->get('list.direction'));
//$saveOrder      = $listOrder == 'a.ordering';
 * 
 */
?>

<?php foreach($this->items as $i => $item):
	$found = false;
	//echo ("<pre>"); var_dump($this->imported); echo ("</pre>");
	foreach ($this->imported as $j => $imported) {
		if ($item->id == $imported->id) { $found = true; break; }
	}
	$item->max_ordering = 0; //??
	/*//$ordering       = ($listOrder == 'a.ordering');
	$canCreate      = $user->authorise('core.create',		'com_apl.category.'.$item->categorie);
	$canEdit        = $user->authorise('core.edit',			'com_apl.chantier.'.$item->id);
	$canCheckin     = $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
	//$canEditOwn     = $user->authorise('core.edit.own',		'com_apl.chantier.'.$item->id) && $item->created_by == $userId;
	$canChange      = $user->authorise('core.edit.state',	'com_apl.chantier.'.$item->id) && $canCheckin;
	 * 
	 */
	?>
	<tr class="row<?php echo $i % 2; ?>">
        <td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td class="center">
			<?php echo JHtml::_('jgrid.published', $found, $i, 'chantiers.', 0, 'cb', '', ''); ?>
		</td>
		<td>
			<span class='<?php echo ($found ? "grey" : "blue"); ?>'><?php echo stripslashes($item->nom); ?></span>
		</td>
		<td class="center">
			<?php //echo $item->visible; ?>
			<?php //echo JHtml::_('jgrid.published', $item->published, $i, 'chantier.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
			<?php echo JHtml::_('jgrid.published', $item->visible, $i, 'chantiers.', 0, 'cb', '', ''); ?>
		</td>
		<td>
			<?php echo $item->categorie; ?>
		</td>
		<td>
			<?php echo stripslashes($item->lieu); ?>
		</td>
		<td>
			<?php echo stripslashes($item->pays); ?>
		</td>
		<td>
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>
