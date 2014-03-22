<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_apl&task=chantiers.edit&id=' . $item->id); ?>"><?php echo $item->nom; ?></a>
		</td>
		<td>
			<?php echo $item->lieu; ?>
		</td>
		<td>
			<?php echo $item->pays; ?>
		</td>
	</tr>
<?php endforeach; ?>
