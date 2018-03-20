<?php
header("Access-Control-Allow-Origin: *");
   // Define database connection parameters

   $hn      = 'localhost';          // localhost
   $un      = 'root';               // user ( like phpmyadmin access ) 
   $pwd     = 'password';           // password 
   $db      = 'projectName';        // database name collection
   $cs      = 'utf8';

   // Set up the PDO parameters
   $dsn 	= "mysql:host=" . $hn . ";port=3306;dbname=" . $db . ";charset=" . $cs;
   $opt 	= array(
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                       );
   // Create a PDO instance (connect to the database)
   $pdo 	= new PDO($dsn, $un, $pwd, $opt);
   // Retrieve the posted data
   $json    =  file_get_contents('php://input');
   $obj     =  json_decode($json);
   $key     =  strip_tags($obj->key);

   // Determine which mode is being requested
   switch($key)
   {
      // Add a new user using post method with key create 
      case "create":
         // Sanitise URL supplied values
         $firstName     = filter_var($obj->firstName, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
         $lastName      = filter_var($obj->lastName, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
         $password      = filter_var($obj->password, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);

         // Attempt to run PDO prepared statement
         try {
            $sql 	= "INSERT INTO User(firstName, lastName, password ) VALUES(:firstName, :lastName, :password )";
            $stmt 	= $pdo->prepare($sql);
            $stmt->bindParam(':firstName', $firstName , PDO::PARAM_STR);
            $stmt->bindParam(':lastName' , $lastName  , PDO::PARAM_STR);
            $stmt->bindParam(':password' , $password  , PDO::PARAM_STR);
            $stmt->execute();
            echo json_encode(array('message' => 'Congratulations the record ' . $firstName. ' was added to the database'));
         }
         // Catch any errors in running the prepared statement
         catch(PDOException $e)
         {
            echo $e->getMessage();
         }
      break;

      // Update an existing user 
      case "update":
         // Sanitise URL supplied values
         $id 		     = filter_var($obj->id, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
         $name 		     = filter_var($obj->name, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);

         // Attempt to run PDO prepared statement
         try {
            $sql 	= "UPDATE user SET name = :name, password= :password WHERE id = :ID";
            $stmt 	=	$pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            echo json_encode('Congratulations the record ' . $name . ' was updated');
         }
         // Catch any errors in running the prepared statement
         catch(PDOException $e)
         {
            echo $e->getMessage();
         }
      break;

         // delate a user
      case "delete":
         // Sanitise supplied record ID for matching to table record
         $id	=	filter_var($obj->id, FILTER_SANITIZE_NUMBER_INT);
         // Attempt to run PDO prepared statement
         try {
            $pdo 	= new PDO($dsn, $un, $pwd);
            $sql 	= "DELETE FROM user WHERE id = :id";
            $stmt 	= $pdo->prepare($sql);
            $stmt->bindParam(':id', $id , PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode('Congratulations the user' . $name . ' was delated');
         }

         // Catch any errors in running the prepared statement
         catch(PDOException $e)
         {
            echo $e->getMessage();
         }
      break;
   }
?>
