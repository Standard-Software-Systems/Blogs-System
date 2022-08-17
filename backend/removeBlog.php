<?php 
    require("../config.php");
    require("../resources.php");
    require("../session.php");

    // Set reference for localization
    $reference = "../";
    if(!isset($_SESSION['id'])) {
        header("Location: ../");
        die();
    }
    elseif(!isset($_SESSION['admin']) || (isset($_SESSION['id']) && $_SESSION['admin'] != true)) {
        header("Location: ../");
        die();
    }

    elseif (isset($_POST['removeBlog'])) {
        $stmt = $db->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->bind_param("i", $_POST['removeBlog']);
        $stmt->execute();
        header("Location: ../admin");
    }
?>