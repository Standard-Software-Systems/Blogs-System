<?php
    require("config.php");
    require("resources.php");
    require("session.php");
    $sql = "SELECT * FROM blogs";
    $result = $db->query($sql);
    $siteInfo = $db->query("SELECT * FROM siteSettings");
    $siteInfo = $siteInfo->fetch_assoc();

?>
<html>
<head>
    <title>Home - <?php echo $siteInfo['siteName'] ?></title>
	<link rel="icon" type="image/png" href="<?php echo $siteInfo['siteLogo'] ?>">
	<script src="https://kit.fontawesome.com/<?php echo $site['fontAwesomeKit'] ?>.js" crossorigin="anonymous"></script>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet"> 
    <link href="./css/main.css?rnd=2" rel="stylesheet">
	<meta name="theme-color" content="#<?php echo $siteInfo['siteTheme'] ?>">
	<meta name="twitter:card" content="summary">
	<meta name="twitter:creator" content="@xolifydev">
	<meta property="og:url" content="<?php echo $domain ?>">
	<meta property="og:title" content="New Paste - <?php echo $siteInfo['siteName'] ?>">
	<meta property="og:description" content="<?php echo $siteInfo['siteDescription'] ?>">
	<meta property="og:image" content="<?php echo $siteInfo['siteLogo'] ?>">
    <style>
        :root {
            --main-color: #<?php echo $site['color'] ?>
        }
    </style>
</head>
<body>
    <section class="navbar">
        <div class="left">
            <img src="<?php echo $site['logo'] ?>">
        </div>
        <div class="right">
            <a href="" class="active">Home</a>
            <?php
                if(isset($_SESSION['id'])) {
                    if($_SESSION['admin'] == true) {
            ?>  
            <a href="admin">Admin Portal</a>
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
            <form method="POST">
                    <button type="submit" name="login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    </button>
            </form>
            <?php
                }
            ?>
        </div>
    </section>
    <section class="body" style="color: white;">
       <?php 
                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) { ?>
                        <div class="Blogs">
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
                      </div>
                        
                   <?php }
                  } else { ?>
                    <div class="noBlogs">
                        <div class="header">
                            <div class="title">
                                No Blogs Found
                            </div>
                    </div>
            <?php } ?>
    </section>
    <section class="footer">
    </section>
</body>
</html>