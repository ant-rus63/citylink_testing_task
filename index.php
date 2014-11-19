<?php
/**
 * This is a point of enter. Requires controllers.
 * This application uses Model-View-Controller structure
 *
 * @package    CityTravel Test Task
 * @author     Anton Litvinov <antlitvinov@gmail.com>
 */

 
error_reporting(E_ALL);


/* get data from mod_rewrite and parsing. Param1 is a module, Param2 is a method */
$path = explode('/', $_GET['path']);

$module = (isset($path[0]) && $path[0])? $path[0] : 'user';
$action = (isset($path[1]) && $path[1])? $path[1] : 'index';

require_once('app/controller.php');
require_once('app/model.php');

require_once('app/db.php');
$db = new DB('localhost', 'root', '', 'citytravel');
$sql = 'SELECT * FROM users WHERE active = ?';
$par = 1;
print_r($db->getResultArray($sql, array($par), 'i'));

switch($module) {
	case 'user':
		
	default:
		require_once('app/user/controller/User.php');
		$controller = new User;
		
		switch($action) {
			case 'register':
				$controller->register();
				break;
				
			case 'auth':
				$controller->auth();
				break;

                  case 'logout':
                        $controller->logout();
                        break;
				
			case 'cities':
				$controller->cities();
				break;

                  case 'update':
                        $controller->updateUserData();
                        break;
				
			default:
				$controller->index();
				break;
		}
		break;
}
?>