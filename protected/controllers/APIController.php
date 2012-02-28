<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

class APIController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
 
	private $errors = array();

	public function filters()
	{
		return array('accessControl');
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@','?')
			),
			// non-logged in can't view anything
			array('deny',
				'users'=>array('?')
			),
		);
	}

	public function beforeAction() {
		if (!isset($_REQUEST['apiuser'])) {
			$this->error('Missing API user');
		}
		if (!isset($_REQUEST['apikey'])) {
			$this->error('Missing API key');
		}

		$user = User::model()->find('username = :username and api_key = :api_key',array(':username'=>$_REQUEST['apiuser'],':api_key'=>$_REQUEST['apikey']));

		if (!preg_match('/^[0-9a-f]{40}$/',$_REQUEST['apikey']) || !$user) {
			$this->error('Authentication failed');
		}

		$identity = new UserIdentity($user->username, '');
		if (!$identity->authenticate_api()) {
			$this->error('Authentication failed');
		}

		Yii::app()->user->login($identity,0);
	}

	public function missingAction($model) {
		$this->beforeAction();

		/* Yii's routing puts things into $_GET that shouldn't be there. this reverts them. */
		if ($_SERVER['QUERY_STRING']) {
			$_GET = array();
			foreach (explode('&',$_SERVER['QUERY_STRING']) as $item) {
				$key = preg_replace('/=.*$/','',$item);
				$value = preg_replace('/^.*=/','',$item);
				$_GET[$key] = rawurldecode($value);
			}
		}

		if (in_array($model,Yii::app()->params['api_allowed_models'])) {
			$args = $this->getMethodArgs($model);

			return $this->api($model,$args);
		}

		$this->error("The method '$model' does not exist");
	}

	public function getMethodArgs($method) {
		$request = preg_replace('/\?.*$/','',$_SERVER['REQUEST_URI']);

		$args = array();
		$start = false;

		foreach (explode('/',$request) as $el) {
			if ($el == $method) {
				$start = true;
			} else if ($start) {
				$args[] = $el;
			}
		}

		return $args;
	}

	public function error($msg) {
		$this->send(array(
			'result' => 'error',
			'message' => $msg
		));
	}

	public function success($data) {
		$this->send(array(
			'result' => 'success',
			'data' => $data
		));
	}

	public function send($data) {
		$user = Yii::app()->session['user'];
		OELog::log("[API] User $user->username logged out");
		Yii::app()->user->logout();

		die(json_encode($data));
	}

	public function api($model, $args) {
		if (!empty($args)) {
			$object_id = $args[0];
		}

		$model = ucfirst($model);

		switch (@$_SERVER['REQUEST_METHOD']) {
			case 'GET':
				if (isset($object_id)) {
					if ($obj = $model::model()->findByPk($object_id)) {
						return $this->success($obj->to_array());
					} else {
						return $this->error($model.' not found');
					}
				} else {
					$where = '';
					$values = array();
					$m = new $model;

					foreach ($_GET as $key => $value) {
						if (!in_array($key,array('apiuser','apikey'))) {
							if ($m->hasAttribute($key)) {
								if ($where) $where.= ' and ';
								$where .= $key.' = ?';
								$values[] = $value;
							} else {
								$this->error("$model model has no '$key' property.");
							}
						}
					}

					$results = array();
					foreach ($model::model()->findAll($where,$values) as $result) {
						$results[] = $result->to_array();
					}
					return $this->success($results);
				}
				exit;
			case 'POST':
				foreach ($model::Model()->getRequiredFields() as $field) {
					if (!isset($_POST[$field])) {
						$this->error("Missing required field '$field'");
					}
				}

				$obj = new $model;

				foreach ($_POST as $key => $value) {
					if (in_array($key,array('id','last_modified_user_id','last_modified_date','created_user_id','created_date'))) {
						$this->error("$model property '$key' cannot be set using the API.");
					} else if (!$obj->hasAttribute($key)) {
						$this->error("$model property '$key' does not exist.");
					} else {
						$obj->{$key} = $value;
					}
				}

				if ($obj->save()) {
					return $this->success($obj->id);
				}

				return $this->error("Failed to create $model object: ".print_r($obj->getErrors(),true));

			case 'PUT':
				if (!isset($object_id)) {
					$this->error("Missing $model ID");
				}

				if (!$obj = $model::model()->findByPk((integer)$object_id)) {
					$this->error("$model not found");
				}

				foreach ($_POST as $key => $value) {
					if (in_array($key,array('id','last_modified_user_id','last_modified_date','created_user_id','created_date'))) {
						$this->error("$model property '$key' cannot be set using the API.");
					} else if (!$obj->hasAttribute($key)) {
						$this->error("$model property '$key' does not exist.");
					} else {
						$munge_method = "munge{$model}".ucfirst($key);
						if (method_exists($this,$munge_method)) {
							$obj->{$key} = $this->{$munge_method}($value);
						} else {
							$obj->{$key} = $value;
						}
					}
				}

				if ($obj->save()) {
					return $this->success($model." updated");
				}

				return $this->error("Failed to update $model object $object_id: ".print_r($obj->getErrors(),true));

			case 'DELETE':
				if (!isset($object_id)) {
					$this->error("Missing $model ID");
				}

				if (!$obj = $model::model()->findByPk((integer)$object_id)) {
					$this->error("$model not found");
				}

				if ($obj->delete()) {
					return $this->success($model." deleted");
				}

				return $this->error("Failed to delete $model object $object_id: ".print_r($obj->getErrors(),true));

			default:
				$this->error('Unknown request method.');
		}
	}
}
?>
