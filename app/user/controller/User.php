<?php

/**
 * This class is a Controller for module `User`.
 * URL like [/user/]
 *
 * @package    CityTravel Test Task
 * @author     Anton Litvinov <antlitvinov@gmail.com>
 */

class User extends Controller{

	private $userID;
	private $model;

      const ERROR_LOGIN_PASSWORD = 'Неверное имя/пароль';
      const ERROR_CAPTCHA = 'Неверная капча';
      const ERROR_LOGIN_EXISTS = 'Такой логин уже существует';
      const ERROR_REGISTRATION_FAILED = 'Регистрация не удалась';

	
	public function __construct(){
        parent::__construct();
        session_start();
		require_once('app/user/model/user_model.php');
		$this->model = new User_model();
		$this->userID = (isset($_SESSION['UserID']) && $_SESSION['UserID'])? intval($_SESSION['UserID']) : 0;
	}



	public function index(){
		if(!$this->userID) header('Location: /user/auth');
		$userData = $this->model->getUserData($this->userID);

            /*requiring view*/
            require_once('app/user/view/common/header.php');
            require_once('app/user/view/user/cabinet.php');
            require_once('app/user/view/common/footer.php');
	}
	
	
	
	
	public function register(){
            $errorRegisterArray = array();
		if($_POST){
		       if($this->checkCaptcha($_POST['captcha'])){
                         if(!$this->model->checkIfUserExistsByLogin($_POST['login'])){
                               if($insertedUserID = $this->model->addUserData($_POST['login'], $_POST['password'], $_POST['city'])){
                                     /* SUCCESSFULLY */
                                     $this->email($_POST['login']);
                                     $_SESSION['UserID'] = $insertedUserID;
                                     $_SESSION['captcha'] = null;
                                     header('Location: /user');
                               } else{
                                     $errorRegisterArray[] = User::ERROR_REGISTRATION_FAILED;
                               }

                         }  else{
                               $errorRegisterArray[] = User::ERROR_LOGIN_EXISTS;
                         }

                   } else {
                         $errorRegisterArray[] = User::ERROR_CAPTCHA;
                   }
		}

            $userRolesList = $this->model->getUserRolesList();
            $captcha = $this->generateCaptcha();

            /*requiring view*/
		require_once('app/user/view/common/header.php');
		require_once('app/user/view/user/register.php');
		require_once('app/user/view/common/footer.php');
	}
	
	
	
	
	public function auth(){
		if($this->userID) header('Location: /user');
		$attemptNumber = (isset($_COOKIE['AttemptNumber']) && $_COOKIE['AttemptNumber'])? intval($_COOKIE['AttemptNumber']) : 0;
		$errorLoginArray = array();

		if($_POST){
                  $attemptVerified = 1;
                  if(isset($_SESSION['captcha']) && $_SESSION['captcha']){
                        $enteredCaptchaVal = (isset($_POST['captcha']) && $_POST['captcha'])? $_POST['captcha'] : 0;
                        $attemptVerified = $this->checkCaptcha($enteredCaptchaVal);
                  }
			$this->userID = $this->model->checkUserData($_POST['login'], $_POST['password']);
			if($this->userID && $attemptVerified){
				setcookie('AttemptNumber', 0);
                        $_SESSION['UserID'] = $this->userID;
				header('Location: /user');
			} elseif(!$attemptVerified) {
                        setcookie('AttemptNumber', ($attemptNumber + 1) );
                        $errorLoginArray[] = User::ERROR_CAPTCHA;
                  } else{
				setcookie('AttemptNumber', ($attemptNumber + 1) );
                        $errorLoginArray[] = User::ERROR_LOGIN_PASSWORD;
			}
		}
            $_SESSION['captcha'] = null;
		if($attemptNumber >= 2) $captcha = $this->generateCaptcha();

            /*requiring view*/
		require_once('app/user/view/common/header.php');
		require_once('app/user/view/user/auth.php');
		require_once('app/user/view/common/footer.php');
	}



      public function logout(){
            $_SESSION['UserID'] = null;
            $_SESSION['captcha'] = null;
            header('Location: /user/auth');
      }



      /**
       * Captha generation method.
       * It using library Simple-Php-Captha
       * https://github.com/claviska/simple-php-captcha
       */
	private function generateCaptcha(){
		require_once ("lib/captcha/simple-php-captcha.php");
            $_SESSION['captcha'] = simple_php_captcha( array(
                                                             'min_length' => 5,
                                                             'max_length' => 7,
                                                             'characters' => 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz23456789',
                                                             'min_font_size' => 18,
                                                             'max_font_size' => 28,
                                                             'color' => '#333',
                                                             'angle_min' => 10,
                                                             'angle_max' => 10,
                                                             'shadow' => true,
                                                             'shadow_color' => '#fff',
                                                             'shadow_offset_x' => -1,
                                                             'shadow_offset_y' => 1
            ));
            return $_SESSION['captcha']['image_src'];
	}


      /**
       * Check captcha value entered by user (I did checking case insensitive)
       *
       * @param string $enteredValue Value of captcha entered by user
       * @return bool Is entered value true
       */
      private function checkCaptcha($enteredValue){
            return (strtolower($enteredValue)==strtolower($_SESSION['captcha']['code']));
      }
	
	
	
	
	public function cities(){
			if($_POST){
				$citiesArray = $this->model->getCitiesByLetter($_POST['letter']);
                        header('Content-Type: application/json');
				echo json_encode($citiesArray);
			} else {
				header('Location: /user');
			}
	}


      public function updateUserData(){
            if($_POST && $this->userID){
                 echo ($this->model->updateUserData($this->userID, $_POST['param'], $_POST['value']))?   1 : 0;

            }  else header('Location: /user');
      }



      /**
       * Sending mail to Admin about new registration
       * Using HTML in mail body.
       * HTML getting from template.
       * Mask for template: [[userName]]
       *
       * @param string $userName
       * @return bool If email sent
       */
	private function email($userName){
	      $emailParamsArray = $this->model->getEmailParams($userName);
            return mail($emailParamsArray['To'], $emailParamsArray['Subject'], $emailParamsArray['Body'], $emailParamsArray['Headers']);
	}
	
}

?>