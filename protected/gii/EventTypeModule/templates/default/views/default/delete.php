<?php echo "<?php\n"?>
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
<?php echo "?>\n"?>

<?php echo "<?php\n"?>
	$this->breadcrumbs=array($this->module->id);
	$this->header();
<?php echo "?>\n"?>

<h3 class="withEventIcon" style="background:transparent url(<?php echo '<?php '?>echo $this->assetPath<?php echo '?>'?>/img/medium.png) center left no-repeat;"><?php echo '<?php '?>echo $this->event_type->name<?php echo '?>'?></h3>

<div>
	<div class="cleartall"></div>
</div>

<div id="delete_event">
	<h1>Delete event</h1>
	<div class="alertBox" style="margin-top: 10px;">
		<strong>WARNING: This will permanently delete the event and remove it from view.<br><br>THIS ACTION CANNOT BE UNDONE.</strong>
	</div>
	<p>
		<strong>Are you sure you want to proceed?</strong>
	</p>
	<?php echo "<?php\n"?>
		echo CHtml::form(array('Default/delete/'.$this->event->id), 'post', array('id' => 'deleteForm'));
		echo CHtml::hiddenField('event_id', $this->event->id);
	<?php echo "?>\n"?>
	<div class="buttonwrapper">
		<button type="submit" class="classy red venti" id="et_deleteevent" name="et_deleteevent"><span class="button-span button-span-red">Delete event</span></button>
		<button type="submit" class="classy green venti" id="et_canceldelete" name="et_canceldelete"><span class="button-span button-span-green">Cancel</span></button>
		<img class="loader" src="<?php echo '<?php'?> echo Yii::app()->createUrl('img/ajax-loader.gif')<?php echo '?>'?>" alt="loading..." style="display: none;" />
	</div>
	<?php echo '<?php'?> echo CHtml::endForm()<?php echo "?>\n"?>
</div>

<?php echo '<?php'?> $this->footer()<?php echo '?>'?>
