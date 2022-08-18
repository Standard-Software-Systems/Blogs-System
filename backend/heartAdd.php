<?php  
    require("../config.php");
    require("../resources.php");
    require("../session.php");
    
    // Set reference for localization
    $reference = "../";
    echo $_GET['blogId'];
    if (!isset($_POST['heart'])) {
            header("Location: ../");
            die();
        }

        $data;
        else if(!isset($_SESSION['id'])) {
            $data = array("success" => false, "message" => "You must be logged in to heart a blog.");
            header("Content-Type: application/json");
            echo json_encode($data);
            die();
        } 
        else {
            if(!isset($_GET['blogId'])) {
                $data = array("success" => false, "message" => "There was an error, please contact the developers.");
                header("Content-Type: application/json");
                echo json_encode($data);
                die();
            }   
            else {  
            $stmt = $db->prepare("INSERT INTO heartslikes (userid, which, blogId) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_SESSION['id'], "heart", $_GET['blogId']);
            $stmt->execute();
            }
        }
?>