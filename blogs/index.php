<?php
    require("../config.php");
    require("../resources.php");
    require("../session.php");

    // Set reference for localization
    $reference = ("../");
    // Clear idempotency key
    unset($_SESSION['idempotency']);
    $siteInfo = $db->query("SELECT * FROM siteSettings");
    $siteInfo = $siteInfo->fetch_assoc();

    if (!isset($_GET['blog'])) {
        header("Location: ../");
        die();
    }
    else {
        $stmt = $db->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->bind_param("i", $_GET['blog']);
        $stmt->execute();
        $blog = $stmt->get_result();
        if($blog->num_rows == 0) {
            header("Location: ../");
            die();
        }
        else {
            $row = $blog->fetch_assoc();

        }
    }

?>
<html>
<head>
    <title>Home - <?php echo $siteInfo['siteName'] ?></title>
	<link rel="icon" type="image/png" href="<?php echo $siteInfo['siteLogo'] ?>">
	<script src="https://kit.fontawesome.com/<?php echo $site['fontAwesomeKit'] ?>.js" crossorigin="anonymous"></script>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet"> 
    <link href="../css/main.css" rel="stylesheet">
	<meta name="theme-color" content="#<?php echo $siteInfo['siteTheme'] ?>">
	<meta name="twitter:card" content="summary">
	<meta name="twitter:creator" content="@xolifydev">
	<meta property="og:url" content="<?php echo $domain ?>">
	<meta property="og:title" content="New Paste - <?php echo $siteInfo['siteName'] ?>">
	<meta property="og:description" content="<?php echo $siteInfo['siteDescription'] ?>">
	<meta property="og:image" content="<?php echo $siteInfo['siteLogo'] ?>">
    <style>
        :root {
            --main-color: <?php echo $siteInfo['siteTheme'] ?>
        }
        .updateBtn:hover {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <section class="navbar">
        <div class="left">
            <a href="<?php echo $reference ?>"><img src="<?php echo $site['logo'] ?>"></a>
        </div>
        <div class="right">
            <a href="<?php echo $reference ?>">Home</a>
            <?php
                if(isset($_SESSION['id'])) {
                    if($_SESSION['admin'] == true) {
            ?>
            <a href="<?php echo $reference ?>admin" class="active">Admin Portal</a>
            <?php
                    }
            ?>
            <form method="POST">
                <button type="submit" name="logout">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
            <?php
                }

                else {
            ?>
            <form method="POST" action="<?php echo $reference ?>">
                <input type="submit" name="login" value="Login">
            </form>
            <?php
                }
            ?>
        </div>
    </section>
    <section class="body"> 
                <div class="b">
                    <?php  
                            $heartCount = $db->query("SELECT * FROM heartslikes WHERE blogId = ".$row['id']." AND which = 'heart'")->num_rows;
                            $likeCount = $db->query("SELECT * FROM heartslikes WHERE blogId= ".$row['id']." AND which='thumb'")->num_rows;
                    ?>
                    <img src="<?php echo $row['image'] ?>" alt="" class="image">
                    <div class="con">
                        
                        <div class="header">
                            <div class="title">
                            <?php echo $row['title'] ?>
                        </div>
                    </div>
                    <div class="content">
                        <?php echo $row['content'] ?>
                    </div>
                    <div class="author">
                        <i class="fa-solid fa-user"></i>
                        <?php echo $row['author'] ?>
                    </div>
                    <div class="date">
                        <i class="fa-solid fa-calendar"></i>
                        <?php echo $row['date'] ?>
                    </div>
                </div>
    </section>
    <script>
       if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        function sortByFlagged() {
            var elements = document.getElementsByClassName("paste");
            var sortElement = document.querySelector(".pastes");
            var sorted = sortElement.dataset.sort;
            console.log(sorted)

            for(var i = 0; i < elements.length; i++) {
                var child = elements[i].children.item(4);

                if(sorted == "no") {
                    if(child.innerHTML != "Yes") {
                        elements[i].classList.add("hidden");
                    }

                    else {
                        elements[i].classList.remove("hidden");
                    }

                    sortElement.dataset.sort = "yes";
                }

                else {
                    elements[i].classList.remove("hidden");
                    sortElement.dataset.sort = "no";
                }
            }
        }
    </script>
</body>
</html>