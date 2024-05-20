<?php
class database
{
 function opencon(){
return new PDO ('mysql:host=localhost;dbname=loginmethods','root', '');
}   
    function check($username, $password){
$con=$this->opencon();
$query = "SELECT * from user WHERE username='".$username."'&& passwords='".$password."'";
return  $con->query($query)->fetch();
 
    }
    function signup($username, $password, $firstname, $lastname, $birthday, $sex){
        $con = $this->opencon();
        $query = $con->prepare("SELECT username FROM user WHERE username = ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();
 
        if ($existingUser){
            return false;
        }
        return $con->prepare("INSERT INTO user (username, passwords, firstname, lastname, birthday, sex) VALUES (?,?,?,?,?,?)") ->execute([$username, $password, $firstname, $lastname, $birthday, $sex]);
    }
    function signupUser($username, $password, $firstName, $lastName, $birthday, $sex) {
        $con = $this->opencon();
   
        $query = $con->prepare("SELECT username FROM user WHERE username = ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();
        if ($existingUser){
            return false;
        }
        $query = $con->prepare("INSERT INTO user (username, passwords, firstname, lastname, birthday, sex) VALUES (?, ?, ?, ?, ?,?)");
        $query->execute([$username, $password, $firstName, $lastName, $birthday,$sex]);
        return $con->lastInsertId();
    }function insertAddress($user_id, $city, $province, $street, $barangay) {
        $con = $this->opencon();
        return $con->prepare("INSERT INTO user_address (user_id, user_add_city, user_add_province, user_add_street, user_add_barangay) VALUES (?, ?, ?, ?, ?)")
            ->execute([$user_id, $city, $province, $street, $barangay]);
    }

    function view ()
    {
        $con = $this->opencon();
        return $con->query("SELECT user.user_id, user.username, user.passwords, user.firstname, user.lastname, user.birthday, user.sex, CONCAT(user_address.user_add_street,' ', user_address.user_add_barangay,' ', user_address.user_add_city,' ', user_address.user_add_province) AS address FROM user JOIN user_address ON user.user_id=user_address.user_id;")
        ->fetchALL();
    }
    function delete($id)
    {
        try {
            $con = $this->opencon();
            $con->beginTransaction();

            $qeury = $con->prepare("DELETE FROM user_address WHERE user_id =?");
            $qeury->execute([$id]);

            $query2 = $con->prepare("DELETE FROM user WHERE user_id =?");
            
            $con->commit();
            return true;
        } catch(PDOException $e) {
            $con->rollBack();
            return false;
        }
    }

          function viewdata($id) {
              try {
                 $con = $this->opencon();
                 $query = $con->prepare("SELECT user.user_id, user.username, user.passwords, user.firstname, user.lastname, user.birthday, user.sex, user_address.user_add_street, user_address.user_add_barangay, user_address.user_add_city, user_address.user_add_province FROM user JOIN user_address ON user.user_id=user_address.user_id WHERE user.user_id = ?");
                 $query->execute([$id]);
                 return $query->fetch();
               } catch(PDOException $e) {
              return [];
          }

    }

    function updateUser($user_id, $firstName, $lastName, $birthday, $sex, $username, $password) {
        try {
            $con = $this->opencon();
            $con ->beginTransaction();
            $query = $con->prepare("UPDATE user SET firstname=?, lastname=?, birthday=?, sex=?, username=?, passwords=? WHERE user_id=?");
            $query->execute([$firstName, $lastName, $birthday, $sex, $username, $password, $user_id]);
            $con ->commit();
            return true;
        } catch(PDOException $e) {
            $con->rollBack();
            return false;
    }
}

function updateUserAddress($user_id, $street, $barangay, $city, $province ) {
    try {
        $con = $this->opencon();
        $con ->beginTransaction();
        $query = $con->prepare("UPDATE useraddress SET street=?, barangay=?, city=?, province=? WHERE user_id=?");
        $query->execute([$street, $barangay, $city, $province, $user_id]);
        $con ->commit();
        return true;
    } catch(PDOException $e) {
        $con->rollBack();
        return false;
}
}

}