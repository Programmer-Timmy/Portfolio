<?php

class HtmlTemplateRenderer {

    /**
     * Get the project card for the home page
     *
     * @param $project
     * @return false|string
     */
    public static function getProjectCard($project): false|string {
        ob_start(); // Start output buffering
        ?>
        <div class="col-md-6 col-xl-4">
            <div class="project-home">
                <div class="position-relative">
                    <div class="badgeContainer position-absolute">
                        <?php if ($project->in_progress): ?>
                            <i class="in-progress p-2 border border-light rounded-circle fa-solid fa-person-digging"></i>
                        <?php endif; ?>
                        <?php if ($project->pinned): ?>
                            <i class="pinned p-2 border border-light rounded-circle fa-solid fa-thumbtack"></i>
                        <?php endif; ?>
                    </div>

                    <a href="project?id=<?= $project->id ?>">
                        <img src="https://portfolio.timmygamer.nl/<?= $project->img ?>" class="img-size" alt="" loading="lazy">
                        <?php if ($project->project_languages): ?>
                            <languagesSection
                                    class="languages position-absolute bottom-0 start-50 translate-middle-x p-1 w-100">
                                <?php foreach ($project->project_languages as $language): ?>
                                    <?= self::get_language_badge($language) ?>
                                <?php endforeach; ?>
                            </languagesSection>
                        <?php endif; ?>
                    </a>
                </div>

                <?php if ($project->github): ?>
                    <h1>
                        <a class="github text-decoration-none" href="<?= $project->github ?>">
                            <i class="fa fa-github" aria-hidden="true"></i>
                        </a>
                        <?= $project->name ?>
                    </h1>
                <?php else: ?>
                    <h1><?= $project->name ?></h1>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean(); // Return the buffered content
    }

    public static function get_language_badge($language): string {
        ob_start(); // Start output buffering
        ?>
        <span class="badge bg-primary mx-1" style="background-color: <?= $language->color ?> !important; color: black;"><?= $language->name ?><?php if ($language->percentage):?> | <?= $language->percentage * 1 ?>%<?php endif?></span>
        <?php
        return ob_get_clean(); // Return the buffered content
    }
}