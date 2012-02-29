<?php
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

class UpdateAuthorisationCommand extends CConsoleCommand {
	public function getName() {
		return 'UpdateAuthorisation';
	}

	public function getHelp() {
		return 'Updates the authorisation items declared by classes implementing AuthorisationProvider';
	}

	public function run($args) {
		$include_path = explode(':',get_include_path());
		$base_path = Yii::app()->getBasePath();
		foreach($include_path as $path) {
			if(substr($path, 0, strlen($base_path)) == $base_path) {
				$classes = glob($path.DIRECTORY_SEPARATOR.'*.php');
				foreach($classes as $class_path) {
					$class_name = basename($class_path,'.php');
					$reflection_class = new ReflectionClass($class_name);
					if($reflection_class->implementsInterface('AuthorisationProvider') && !$reflection_class->isAbstract()) {
						foreach($class_name::defined_operations() as $operation) {
							echo "$operation\n";
						}
					}
				}
			}
		}
	}
}
