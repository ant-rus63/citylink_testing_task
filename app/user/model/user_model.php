<?php

/**
 * This class is a Model for module `User`.
 *
 * @package    CityTravel Test Task
 * @author     Anton Litvinov <antlitvinov@gmail.com>
 */

class User_model extends Model{

      private  $dbHost;
      private  $dbUser;
      private  $dbPassw;
      private  $dbName;

      private $adminEmail;
      private $emailSubject;

      const USER_ROLE_ID = 1; /* ID role "Клиент" (by default) */
      const SYSTEM_EMAIL = ''; /* <From> field */


	public function __construct(){
            parent::__construct();
            $configsArray = parse_ini_file("config.ini", true);
            $this->dbHost = $configsArray['database_config']['host'];
            $this->dbUser = $configsArray['database_config']['user'];
            $this->dbPassw = $configsArray['database_config']['password'];
            $this->dbName = $configsArray['database_config']['dbname'];
            $this->adminEmail = $configsArray['mail_config']['admin_email'];
            $this->emailSubject = $configsArray['mail_config']['subject'];
	}
	
	
	public function checkUserData($login, $password){
            $userID = 0;
            $active = 1;
            $sql = "SELECT `ID` FROM `users` WHERE `UserName` = ? AND `Password` = ? AND `Active` = ? LIMIT 1";
            $mysqli = new mysqli($this->dbHost, $this->dbUser, $this->dbPassw, $this->dbName);
            $mysqli->query("SET NAMES 'utf-8'");

            /* Curse you, SQL-injections! :) */
            if ($stmt = $mysqli->prepare($sql)) {
                  $stmt->bind_param("ssi", $login, md5($password), $active);
                  $stmt->execute();
                  $stmt->bind_result($userID);
                  $stmt->fetch();
                  $stmt->close();
            }
            $mysqli->close();
            return $userID;

	}


      public function getUserData($userID){
            $sql = "SELECT    `users`.`ID`
                              , `users`.`UserName`
                              , `users`.`Password`
                              , `users`.`City`
                              , `users`.`RoleID`
                              , `users`.`Active`
                              , `user_roles`.`RoleName`
                    FROM `users`
                    LEFT JOIN `user_roles` ON `users`.`RoleID` = `user_roles`.`ID`
                    WHERE users.ID = ".intval($userID)." LIMIT 1";
            $mysqli = new mysqli($this->dbHost, $this->dbUser, $this->dbPassw, $this->dbName);
            $mysqli->query("SET NAMES 'utf-8'");
            if ($result = $mysqli->query($sql)) {
                  $data =  $result->fetch_object();
                  return $data;
            }   else
                  return 0;
      }


      public function addUserData($login, $password, $city, $roleID = user_model::USER_ROLE_ID){
            $result = 0;
            $sql = "INSERT INTO `users`  (`UserName`, `Password`, `City`, `RoleID`, `Active`) VALUES (?, ?, ?, ?, 1)";
            $mysqli = new mysqli($this->dbHost, $this->dbUser, $this->dbPassw, $this->dbName);
            $mysqli->query("SET NAMES 'utf-8'");
            if ($stmt = $mysqli->prepare($sql)) {
                  $stmt->bind_param("sssi", $login, md5($password), $city, $roleID);
                  $result = $stmt->execute();
                  $stmt->close();
            }
            $lastInsertID = ($result)? $mysqli->insert_id : 0;
            $mysqli->close();
            return $lastInsertID;
      }


      public function updateUserData($userID, $paramName, $paramValue){
            $userID = intval($userID);
            $sql = '';
            $sttmTypes = '';
            $sttmValue = '';
            $result = 0;
            switch($paramName){
                  case 'login':
                        $sql = "UPDATE `users` SET `UserName` = ? WHERE `ID`= ?";
                        $sttmTypes = 'si';
                        $sttmValue = $paramValue;
                        break;

                  case 'password':
                        $sql = "UPDATE `users` SET `Password` = ? WHERE `ID`= ?";
                        $sttmTypes = 'si';
                        $sttmValue = md5($paramValue);
                        break;

                  case 'city':
                        $sql = "UPDATE `users` SET `City` = ? WHERE `ID`= ?";
                        $sttmTypes = 'si';
                        $sttmValue = $paramValue;
                        break;

                  case 'role':
                        $sql = "UPDATE `users` SET `RoleID` = ? WHERE `ID`= ?";
                        $sttmTypes = 'ii';
                        $sttmValue = intval($paramValue);
                        break;

                  case 'active':
                        $sql = "UPDATE `users` SET `Active` = ? WHERE `ID`= ?";
                        $sttmTypes = 'ii';
                        $sttmValue = intval($paramValue);
                        break;
            }
            $mysqli = new mysqli($this->dbHost, $this->dbUser, $this->dbPassw, $this->dbName);
            $mysqli->query("SET NAMES 'utf-8'");
            if ($stmt = $mysqli->prepare($sql)) {
                  $stmt->bind_param($sttmTypes, $sttmValue, $userID);
                  $stmt->execute();
                  $stmt->bind_result($result);
                  $stmt->close();
            }
            $mysqli->close();
            return $result;
      }


      public function checkIfUserExistsByLogin($login){
            $count = 0;
            $sql = "SELECT count(*) AS `Count` FROM `users` WHERE `UserName` = ?";
            $mysqli = new mysqli($this->dbHost, $this->dbUser, $this->dbPassw, $this->dbName);
            $mysqli->query("SET NAMES 'utf-8'");
            if ($stmt = $mysqli->prepare($sql)) {
                  $stmt->bind_param("s", $login);
                  $stmt->execute();
                  $stmt->bind_result($count);
                  $stmt->fetch();
                  $stmt->close();
            }
            $mysqli->close();
            return ($count > 0)? 1 : 0;
      }


      public function getUserRolesList(){
            $data = array();
            $sql = "SELECT `ID`, `RoleName` FROM `user_roles` WHERE `Active` = 1";
            $mysqli = new mysqli($this->dbHost, $this->dbUser, $this->dbPassw, $this->dbName);
            $mysqli->query("SET NAMES 'utf-8'");
            if ($result = $mysqli->query($sql)) {
                  while($res = $result->fetch_object()){
                        $data[] =  $res;
                  }
            }
            $mysqli->close();
            return $data;
      }
	
	
	public function getCitiesByLetter($letter){
			$file = 'cities.dat';
			$fo = fopen($file, 'r');
			$content = fread($fo, filesize($file));
			fclose($fo);
			$citiesArray = explode ("\r",$content);
			$matchedArray = array();
			for($i=0; $i<count($citiesArray); $i++){
				if(strstr(mb_strtoupper($citiesArray[$i], "UTF-8"), mb_strtoupper($letter, "UTF-8"))) $matchedArray[] = $citiesArray[$i];

			}
			return $matchedArray;
	}


      public function getEmailParams($userName = null){
            $file = 'view/user/email.html';
            $fo = fopen($file, 'r');
            $content = fread($fo, filesize($file));
            if(!is_null($userName )){
                  $content = str_replace('[[username]]', $userName, $content);
            }
            fclose($fo);
            $headers = 'Content-type: text/html; charset=utf-8 \r\n';
            $headers .= 'From: New User Registration <'. User_model::SYSTEM_EMAIL .'>\r\n';

            return array('To' => $this->adminEmail,
                         'Subject'    => $this->emailSubject,
                         'Body' => $content,
                         'Headers' => $headers);
      }


}
?>