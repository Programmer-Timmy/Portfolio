<?php
$projects = Projects::loadprojects("3");
?>

<div class="welcome">
    <h1>Welcome!</h1>
</div>
<div class="borderp" style="color: red">
    <?php
    if ($projects) {
        foreach ($projects as $project) {
            $popupdata = [$project->path, $project->name, $project->description, $project->guest_password, $project->guest_username];
            $json = json_encode($popupdata);
            echo '
            <div class=\'project-home\' ">
            <div style="background: white;">
                <a href=\'#\' onclick=\'showPopup(' . $json . ')\'>
                    <img src=\' ' . $project->img . ' \' class=\'img-size\' alt=\'\'>
                </a>
            </div>';

            if ($project->github) {
                echo "<h1><a class='github' href=" . $project->github . "><i class='fa fa-github' aria-hidden='true'></i></a>" . $project->name . "</h1>";
            } else {
                echo "<h1>" . $project->name . "</h1>";
            }
            echo "</div>";
        }
    } else {
        echo "<h1>Helaas geen projecten</h1>";
    }
    ?>
</div>
<?php require_once '../private/views/Popups/ProjectPopup.php' ?>