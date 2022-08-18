<?php
    require("config.php");
    require("resources.php");
    require("session.php");
    
    $blogs = $db->query("SELECT * FROM blogs");
    $siteInfo = $db->query("SELECT * FROM siteSettings LIMIT 1")->fetch_assoc();

    if(isset($_POST['heart'])) {
        if(!isset($_SESSION['id'])) {
            echo "<script>confirm('You must be logged in to heart a blog.');</script>";
        } else {
            $p = $db->prepare("SELECT * FROM heartslikes WHERE userid = ? AND blogId = ? AND which = 'heart'");
            $p->bind_param("ss", $_SESSION['id'], $_POST['blogId']);
            $p->execute();
            $p = $p->get_result();
            
            if($p->num_rows <= 0) {
                $type = "heart";
                $heart = $db->prepare("INSERT INTO heartslikes (userid, which, blogId) VALUES (?, ?, ?)");
                $heart->bind_param("sss",$_SESSION['id'], $type, $_POST['blogId']);
                $heart->execute();
            } else {
                echo "<script>confirm('You can not heart a blog twice.');</script>";
            }
        }
    }
        if(isset($_POST['thumb'])) {
            if(!isset($_SESSION['id'])) {
                echo "<script>confirm('You must be logged in to like a blog.');</script>";
            } else { 
                $p = $db->prepare("SELECT * FROM heartslikes WHERE userid = ? AND blogId = ? AND which = 'thumb'");
                $p->bind_param("ss", $_SESSION['id'], $_POST['blogId']);
                $p->execute();
                $p = $p->get_result();

                if($p->num_rows <= 0) {
                    $type = "thumb";
                    $like = $db->prepare("INSERT INTO heartslikes (userid, which, blogId) VALUES (?, ?, ?)");
                    $like->bind_param("sss", $_SESSION['id'], $type, $_POST['blogId']);
                    $like->execute();
                } else {
                    echo "<script>confirm('You can not like a blog twice.');</script>";
                }
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
    <link href="./css/main.css" rel="stylesheet">
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
            if ($blogs->num_rows > 0) {
                while($blog = $blogs->fetch_assoc()) {
                    $heartCount = $db->query("SELECT * FROM heartslikes WHERE blogId = ".$blog['id']." AND which = 'heart'")->num_rows;
                    $likeCount = $db->query("SELECT * FROM heartslikes WHERE blogId= ".$blog['id']." AND which='thumb'")->num_rows;

                    $string = strlen($blog['content']) <= 100 ? $blog['content'] : substr($blog['content'], 0, 90) . "...";
            ?>
                <div class="Blogs">
                    <img src="<?php echo $blog['image'] ?>" alt="" class="image">
                    <div class="con">
                        
                        <div class="header">
                            <div class="title">
                            <?php echo $blog['title'] ?>
                        </div>
                    </div>
                    <div class="content">
                        <?php echo $string ?>
                    </div>
                    <div class="more">
                        <a href="blogs/index.php?blog=<?php echo $blog['id'] ?>">
                            <i class="fa-solid fa-arrow-right"></i>
                            Read More
                        </a>
                    </div>
                    <div class="author">
                        <i class="fa-solid fa-user"></i>
                        <?php echo $blog['author'] ?>
                    </div>
                    <div class="date">
                        <i class="fa-solid fa-calendar"></i>
                        <?php echo $blog['date'] ?>
                    </div>
                    <div class="thumb" style="margin-left:.5%;">
                    <form method="POST">
                    <input type="hidden" name="blogId" value="<?php echo $blog['id'] ?>">
                        <button type="submit" name="thumb">
                        <i class="fa-solid fa-thumbs-up" style="color: var(--main-color);"></i>
                        <?php echo $likeCount ?>
                        </button>
                    </form>
                    </div>
                    <div class="heart" style="margin-left:.5%;">
                    <form method="POST">
                    <input type="hidden" name="blogId" value="<?php echo $blog['id'] ?>">
                    <button type="submit" name="heart">
                    <i class="fa-solid fa-heart" style="color: var(--main-color);"></i>
                        <?php echo $heartCount ?>
                        </button>
                    </form>
                    </div>
                </div>
            <?php 
                }
            } 
            
            else { 
                ?>
                    <div class="noBlogs">
                        <div class="header">
                            <div class="title">
                                No Blogs Found
                            </div>
                    </div>
        <?php 
                }
        ?>
    </section>
    <section class="footer">
    </section>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }   
        function addHeart(id) {
            fetch(`backend/addHeart.php?blogId=${id}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert("Added to hearts");
                }
                else {
                    alert("Error");
                }
            })
        }
    </script>
</body>
</html>