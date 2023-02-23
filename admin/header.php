<?php
 echo '<header><div class="container">
        <a href="javascript:void(0);" class="icon" onclick="MyFunction()">
        <i class="fa fa-bars"></i>
        </a>
        <a href="logout" class="icon" style="display:block;">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>';
        if ($_SERVER['REQUEST_URI'] == '/admin/users') {
            echo '<a href="?add=account" class="icon" style="display:block; margin-right:40px;">
                <i class="fa-solid fa-plus"></i>
            </a>';
        } elseif ($_SERVER['REQUEST_URI'] == '/admin/projects') {
            echo '<a href="add-project" class="icon" style="display:block; margin-right:40px;">
                <i class="fa-solid fa-plus"></i>
            </a>';
        }

        echo'
        <nav id="nav">
        <ul>
            <li><a href="/">Portfolio</a></li>
            <li><a href="../admin">Home</a></li>
            <li><a href="projects">Projects</a></li>';
        if (isset($_SESSION["admin"])) {
            echo '<li><a href="users">Users</a></li>';
        }
        echo '
        </ul>
        </nav></div></header>';