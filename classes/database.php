<?php
class database
{
    function opencon()
    {
        return new PDO('mysql:host=localhost;dbname=phpoop221','root','');
    }
 
    function check($username, $password) {
        // Open database connection
        $con = $this->opencon();
    
        // Prepare the SQL query
        $stmt = $con->prepare("SELECT * FROM userss WHERE user = ?");
        $stmt->execute([$username]);
    
        // Fetch the user data as an associative array
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // If a user is found, verify the password
        if ($user && password_verify($password, $user['pass'])) {
            return $user;
        }
    
        // If no user is found or password is incorrect, return false
        return false;
    }
    function signup($firstname, $lastname, $birthday, $sex, $email, $username, $password,  $profilePicture){
        $con = $this->opencon();
        $query = $con->prepare("SELECT user FROM userss WHERE user= ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();

        if ($existingUser) {
            return false;
        }

        return $con->prepare("INSERT INTO userss (firstname, lastname, birthday, sex, user, pass, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?)")
        ->execute([$firstname, $lastname, $birthday, $sex, $email, $username, $password, $profilePicture]);
    }

    function signupUser($firstname, $lastname, $birthday, $sex, $email, $username, $password, $profilePicture)
    {
        $con = $this->opencon();
        // Save user data along with profile picture path to the database
        $con->prepare("INSERT INTO userss (firstname, lastname, birthday, sex, email, user, pass, user_profile_picture) VALUES (?,?,?,?,?,?,?,?)")->execute([$firstname, $lastname, $birthday, $sex, $email, $username, $password, $profilePicture]);
        return $con->lastInsertId();
        }
    
        function insertAddress($user_id, $street, $barangay, $city, $province) {
            $con = $this->opencon();
     
            return $con->prepare("INSERT INTO users_address (user_id, user_street, user_barangay, user_city, user_province) VALUES (?, ?, ?, ?, ?)")->execute([$user_id, $street, $barangay, $city, $province]);
    }
        function view (){
            $con = $this->opencon();
            return $con -> query("SELECT userss.user_id, userss.firstname, userss.lastname, userss.birthday, userss.sex, userss.user, userss.user_profile_picture, CONCAT(users_address.user_street,' ', users_address.user_barangay,' ', users_address.user_city,' ', users_address.user_province) AS Address FROM users_address INNER JOIN userss ON userss.user_id = users_address.user_id;") -> fetchAll();
    }
    function delete($id){
        try{
            $con = $this->opencon();
            $con->beginTransaction();
           $query= $con->prepare("DELETE FROM users_address WHERE user_id = ?");
            $query->execute([$id]);
            $query2= $con->prepare("DELETE FROM userss WHERE user_id = ?");
            $query2->execute([$id]);
            $con->commit();
            return true;
        } catch (PDOException $e) {
     $con->rollBack();
     return false;
        }
    }
    function viewData($id){
        try{
            $con = $this->opencon();
            $query=$con->prepare("SELECT
            userss.user_id, userss.firstname, userss.lastname, userss.birthday, userss.sex, userss.user, userss.pass, userss.user_profile_picture, users_address.user_street, users_address.users_barangay, users_address.user_city, users_address.user_province FROM users_address INNER JOIN userss ON users_address.user_id = userss.user_id WHERE userss.user_id=?");
            $query->execute([$id]);
            return $query->fetch();
            }
        catch (PDOException $e) {
            return [];
    }
}
    function updateUser($id, $firstName, $lastName, $birthday, $sex, $username, $password) {
        try{
            $con = $this->opencon();
            $query= $con->prepare("UPDATE userss SET firstname=?, lastname=?, birthday=?, sex=?, user=?, pass=? WHERE user_id=?");
            return $query->execute([$firstName, $lastName, $birthday, $sex, $username, $password, $id]);
            //Update Succesful
        } catch (PDOException $e) {
            // Handle the exception 
            return false; // Update failed
    }   
}
    function updateUserAddress($id, $street, $barangay, $city, $province) {
        try{
            $con = $this->opencon();
            $query= $con->prepare("UPDATE user_address SET user_street=?, user_barangay=?, user_city=?, user_province=?  WHERE user_id=?");
            return $query->execute([$street, $barangay, $city, $province, $id]);
            //Update Successful
        } catch (PDOException $e) {
            //Handle the exception
            return false; // Update failed
}
    }
    function getusercount()
{
    $con = $this->opencon();
    return $con->query("SELECT SUM(CASE WHEN user_sex = 'Male' THEN 1 ELSE 0 END) AS male_count,
    SUM(CASE WHEN sex = 'Female' THEN 1 ELSE 0 END) AS female_count FROM userss;")->fetch();
}

function checkEmailExists($email) {
    $con = $this->opencon();
    $query = $this->$con->prepare("SELECT email FROM userss WHERE email = ?");
    $query->execute([$email]);
    return $query->fetch();
}

function validateCurrentPassword($userId, $currentPassword) {
    // Open database connection
    $con = $this->opencon();

    // Prepare the SQL query
    $query = $con->prepare("SELECT pass FROM userss WHERE user_id = ?");
    $query->execute([$userId]);

    // Fetch the user data as an associative array
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // If a user is found, verify the password
    if ($user && password_verify($currentPassword, $user['pass'])) {
        return true;
    }

    // If no user is found or password is incorrect, return false
    return false;
}
function updatePassword($userId, $hashedPassword) {
    $con = $this->opencon();
    $query = $con->prepare("UPDATE userss SET pass = ? WHERE user_id = ?");
    return $query->execute([$hashedPassword, $userId]);
}
}
