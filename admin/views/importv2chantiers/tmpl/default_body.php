<?php
/**
 * @package		com_apl_j25
 * @subpackage	importv2chantiers
 * @brief		MVC to import data from old database
 * @copyright	Copyright (C) 2010 - 2014 DEGENNES Charles-Antoine <cadegenn@gmail.com>
 * @license		Affero GNU General Public License version 3 or later; see LICENSE.txt
 * 
 * @TODO		
 */

/** 
 *  Copyright (C) 2012-2014 DEGENNES Charles-Antoine <cadegenn@gmail.com>
 *  com_apl is a joomla! 2.5 component [http://www.apasdeloup.org]
 *  
 *  This file is part of com_apl.
 * 
 *     com_apl is free software: you can redistribute it and/or modify
 *     it under the terms of the Affero GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     com_apl is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     Affero GNU General Public License for more details.
 * 
 *     You should have received a copy of the Affero GNU General Public License
 *     along with com_apl.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
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
			<span class='<?php echo ($found ? "grey" : "blue"); ?>'><?php echo APLFunctions::escapeString($item->nom); ?></span>
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
