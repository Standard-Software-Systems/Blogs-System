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
    elseif(isset($_POST['settings']) && isset($_POST['idempotency']) && !empty($_POST['idempotency']) && (!isset($_SESSION['idempotency']) || $_SESSION['idempotency'] != $_POST['idempotency'])) {
        // Set idempotency for previous submission check
        $_SESSION['idempotency'] = $_POST['idempotency'];
        // Prepared remove data (note user's ID is passed for security purposes)
        if(!empty($_POST['siteName']) && !empty($_POST['siteDesc']) && !empty($_POST['siteLogo'])) {
            $siteName = $_POST['siteName'];
            $siteDescription = $_POST['siteDesc'];
            $siteLogo = $_POST['siteLogo'];
            $siteTheme = $_POST['siteTheme'];
            $stmt = $db->prepare("UPDATE siteSettings SET siteName = ?, siteDescription = ?, siteLogo = ?, siteTheme = ? WHERE id = '1'");
            $stmt->bind_param("ssss", $siteName, $siteDescription, $siteLogo, $siteTheme);
            $stmt->execute();
            header("Location: ../admin");
            die();
        }
    }

?>