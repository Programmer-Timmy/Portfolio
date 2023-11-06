<header>
    <div class="container">
        <a href="javascript:void(0);" class="icon" onclick="MyFunction()">
            <i class="fa fa-bars"></i>
        </a>
        <a href="logout" class="icon" id="hicon1" style="display:block;">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>
        <?php
        if ($_SERVER['REQUEST_URI'] == '/admin/users') {
            echo '<a href="?add=account" class="icon hicon2" style="display:block;">
                <i class="fa-solid fa-plus"></i>
            </a>';
        } elseif ($_SERVER['REQUEST_URI'] == '/admin/projects') {
            echo '<a href="add-project" class="icon hicon2" style="display:block;">
                <i class="fa-solid fa-plus"></i>
            </a>';
        }
        ?>
        <nav id="nav">
            <ul>
                <li><a href="/">Portfolio</a></li>
                <li><a href="../admin">Home</a></li>
                <li><a href="projects">Projects</a></li>
                <li><a href="videos">Add video's</a></li>
                <?php
                if (isset($_SESSION["admin"])) {
                    echo '<li><a href="users">Users</a></li>';
                }
                ?>
            </ul>
        </nav>
    </div>
</header>