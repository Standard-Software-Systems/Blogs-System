<?php
    require("../config.php");
    require("../resources.php");
    require("../session.php");

    // Set reference for localization
    $reference = "../";
    $siteInfo = $db->query("SELECT * FROM siteSettings");
    $siteInfo = $siteInfo->fetch_assoc();

    // If user isn't logged in
    if(!isset($_SESSION['id'])) {
        header("Location: ../");
        die();
    }

    elseif(!isset($_SESSION['admin']) || (isset($_SESSION['id']) && $_SESSION['admin'] != true)) {
        header("Location: ../");
        die();
    }

    // If delete paste received
    elseif(isset($_POST['create']) && isset($_POST['idempotency']) && !empty($_POST['idempotency']) && (!isset($_SESSION['idempotency']) || $_SESSION['idempotency'] != $_POST['idempotency'])) {
        // Set idempotency for previous submission check
        $_SESSION['idempotency'] = $_POST['idempotency'];
        // Prepared remove data (note user's ID is passed for security purposes)
        if(!empty($_POST['title']) && !empty($_POST['content']) && !empty($_POST['image'])) {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $author = $_SESSION['username'];
            $date = date("D M g:i a");
            $image = $_POST['image'];
            $stmt = $db->prepare("INSERT INTO blogs (title, content, author, date, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $title, $content, $author, $date, $image);
            $stmt->execute();
            header("Location: ../admin");
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
    <link href="../css/admin.css" rel="stylesheet">
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
        @media screen and (max-width: 600px), (orientation : portrait) {
            .updateBtn {
                margin-top: 2% !important;
                width: 35% !important;
            }
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
       <div class="createBlog">
        <div class="siteS">

            <h3 class="title">Site Settings</h3>
            <hr style="width: 70%">
            <form method="post" action="../backend/settings.php">
                <label for="">Site Name</label>
                <br>
                <input type="text" name="siteName" placeholder="Site Name" required value="<?php echo $siteInfo['siteName'] ?>">
                <br>
                <label for="">Site Logo</label>
                <br>
                <input type="url" name="siteLogo" placeholder="Site Logo" value="<?php echo $siteInfo['siteLogo'] ?>" required>
                <br>
                <label for="">Site Desc</label>
                <br>
                <textarea name="siteDesc" placeholder="Image Url" required><?php echo $siteInfo['siteDescription'] ?></textarea>
            <br>
            <label for="">Site Theme</label>
            <br>
            <input name="siteTheme" type="color" required value="<?php echo $siteInfo['siteTheme'] ?>" style="width: 30%">
            <br>
            <input type="hidden" name="idempotency" value="<?php echo generateRandomString(16) ?>">
            <input type="submit" name="settings" value="Update" style="width: 20%" class="updateBtn">
        </form>
    </div>
        <h3 class="title">Create a Blog</h3>
        <hr style="width: 70%">
        <form method="post">
            <label for="">Title</label>
            <br>
            <input type="text" name="title" placeholder="Title" required>
            <br>
            <label for="">Content/Desc</label>
            <br>
            <textarea name="content" placeholder="Content" required></textarea>
            <br>
            <label for="">Image Link</label>
            <br>
            <textarea name="image" placeholder="Image Url" required></textarea>
            <br>
            <input type="hidden" name="idempotency" value="<?php echo generateRandomString(16) ?>">
            <input type="submit" name="create" value="Create">
        </form>
        <h3 class="title">Current Blogs</h3>
        <hr style="width: 70%">
              <table class="pastes" data-sort="no" style="margin-bottom: 100px;">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Created</th>
                <th>Author</th>
                <th>Image</th>
                <th class="action"></th>
            </tr>
            <?php
                $pastes = $db->query("SELECT * FROM blogs");

                while($paste = $pastes->fetch_assoc()) {
            ?>
            <tr class="paste">
                <td style="padding-left: .5vw"><a href="../pastes/<?php echo $paste['id'] ?>"><?php echo $paste['id'] ?></a></td>
                <td><?php echo $paste['title'] ?></td>
                <td><?php echo $paste['date'] ?></td>
                <td><?php echo $paste['author'] ?></td>
                <td><a href="<?php echo $paste['image'] ?>" target="_blank">Click Here</a></td>
                <td class="action">
                    <form method="POST" action="../backend/removeBlog.php" class="removeForm">
                        <input type="hidden" name="removeBlog" value="<?php echo $paste['id'] ?>">
                        <a onclick="submitRemove();"><i class="fa-solid fa-xmark"></i></a>
                    </form>
                </td style="padding-right: .5vw">
            </tr>
            <?php
                }
            ?>
       </div>
    </section>
    <script>
        async function submitRemove() {
            await confirm("Are you sure you want to remove this blog?")
            if(confirm) {
                document.querySelector(".removeForm").submit();
            }
        }
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