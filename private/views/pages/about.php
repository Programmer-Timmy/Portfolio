<?php
$tz = new DateTimeZone('Europe/Brussels');
$age = DateTime::createFromFormat('d/m/Y', '03/08/2005', $tz)
    ->diff(new DateTime('now', $tz))
    ->y;
$timeAtZuiderKruis = DateTime::createFromFormat('d/m/Y', '17/11/2013', $tz)
    ->diff(new DateTime('now', $tz))
    ->y;
?>
<div class="container">
    <div class="welcome">
        <h1>About Me!</h1>
    </div>

    <div class="border row" style="padding-bottom: 40px">
        <div class="col-lg-4" style="justify-content: center">
            <?= ImageOptimizer::responsiveImage('img/profielfoto.JPG', 'Tim van der Kloet profile photo', [
                'id' => 'foto',
                'lazy' => false
            ]); ?>
        </div>
        <div id="tekst" class="col-lg-8">
            <h6>Hello, my name is Tim, and I am a <?= $age; ?>-year-old studying software development at HU University of Applied Sciences Utrecht.</h6>
            <p>
                I am currently focused on learning various programming languages, such as Java, Python, TypeScript, and
                APIs. I am passionate about software development and excited about the possibilities it offers.
                <br><br>
                When I'm not studying, I enjoy playing games and sailing during the summer months. In the winter, I
                enjoy repairing and painting boats, which is a fun and creative activity. Additionally, I have been a
                member of the scouting group Het <a href="https://www.zuiderkruis.nl/" target="_blank">ZuiderKruis</a>
                for <?= $timeAtZuiderKruis ?> years, which has allowed me to develop important teamwork and leadership
                skills.
            </p>
            <br>

            <!-- Skills Section -->
            <h6>Skills</h6>
            <div>
                <span>🌐</span>
                <a href="#"><img src="https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&labelColor=gray"
                                 alt="HTML5"></a>
                <a href="#"><img
                            src="https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=1572B6&labelColor=gray"
                            alt="CSS3"></a>
                <a href="#"><img
                            src="https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&labelColor=gray"
                            alt="JavaScript"></a>
                <a href="#"><img src="https://img.shields.io/badge/jQuery-0769AD?style=flat&logo=jquery&labelColor=gray"
                                 alt="jQuery"></a>
                <a href="#"><img src="https://img.shields.io/badge/SCSS-CC6699?style=flat&logo=sass&labelColor=gray"
                                 alt="SCSS"></a>
                <a href="#"><img
                            src="https://img.shields.io/badge/WordPress-21759B?style=flat&logo=wordpress&labelColor=gray"
                            alt="WordPress"></a>
            </div>
            <br>
            <div>
                <span>⚙️</span>
                <a href="#"><img src="https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&labelColor=gray"
                                 alt="PHP"></a>
                <a href="#"><img src="https://img.shields.io/badge/Python-3776AB?style=flat&logo=python&labelColor=gray"
                                 alt="Python"></a>
                <a href="#"><img src="https://img.shields.io/badge/C%23-239120?style=flat&logo=c%23&labelColor=gray"
                                 alt="C#"></a>
                <a href="#"><img
                            src="https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&labelColor=gray"
                            alt="Laravel"></a>
                <a href="#"><img
                            src="https://img.shields.io/badge/Symfony-000000?style=flat&logo=symfony&labelColor=gray"
                            alt="Symfony"></a>
            </div>
            <br>
            <div>
                <span>🔧</span>
                <a href="#"><img src="https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&labelColor=gray"
                                 alt="MySQL"></a>
                <a href="#"><img
                            src="https://img.shields.io/badge/MariaDB-003545?style=flat&logo=mariadb&labelColor=gray"
                            alt="MariaDB"></a>
                <a href="#"><img
                            src="https://img.shields.io/badge/PHPMyAdmin-4D4D4D?style=flat&logo=phpmyadmin&labelColor=gray"
                            alt="PHPMyAdmin"></a>
            </div>
            <br>
            <div>
                <span>🎮</span>
                <a href="#"><img
                            src="https://img.shields.io/badge/Unreal%20Engine-313131?style=flat&logo=unreal-engine&labelColor=gray"
                            alt="Unreal Engine"></a>
                <a href="#"><img src="https://img.shields.io/badge/Unity-000000?style=flat&logo=unity&labelColor=gray"
                                 alt="Unity"></a>
            </div>
            <br>
            <div>
                <span>🐧</span>
                <a href="#"><img src="https://img.shields.io/badge/Ubuntu-E95420?style=flat&logo=ubuntu&labelColor=gray"
                                 alt="Ubuntu"></a>
                <a href="#"><img src="https://img.shields.io/badge/Webmin-00FF00?style=flat&logo=webmin&labelColor=gray"
                                 alt="Webmin"></a>
            </div>
            <br>
            <div>
                <span>🔄</span>
                <a href="#"><img src="https://img.shields.io/badge/Git-F05032?style=flat&logo=git&labelColor=gray"
                                 alt="Git"></a>
                <a href="#"><img src="https://img.shields.io/badge/GitHub-181717?style=flat&logo=github&labelColor=gray"
                                 alt="GitHub"></a>
            </div>
            <br>

            <!-- Projects Section -->
            <h6>Projects & Experience</h6>
            <p>
                I have worked on several projects during my studies and free time, including a personal portfolio
                website, a small recipe-sharing website, and a Satisfactory Planner. Additionally, I have experience
                with WordPress and have created several websites and plugins. I have also developed a converter tool,
                which allows converting Python dataframes into HTML tables and provides an easy way to generate HTML
                pages using Python.
            </p>
            <br>

            <!-- Vision Section -->
            <h6>My Vision on Work</h6>
            <p>
                As I pursue my studies in software development, my vision is to become a versatile and innovative
                professional in the tech industry. I am enthusiastic about leveraging my programming skills to
                contribute to cutting-edge projects that make a positive impact on society. I aspire to continuously
                learn and adapt to emerging technologies, fostering a dynamic and collaborative approach to
                problem-solving. Ultimately, I aim to create software solutions that not only meet technical
                requirements but also address real-world challenges, making a meaningful difference in the world of
                technology.
            </p>
            <br>

            <!-- Goals Section -->
            <h6>What I Want to Achieve</h6>
            <p>
                Looking forward, I aim to achieve proficiency in a wide range of programming languages and frameworks,
                allowing me to tackle diverse and complex projects. I envision being an integral part of innovative
                teams, where creativity and collaboration drive the development of impactful solutions. Additionally, I
                aspire to contribute to open-source projects, share knowledge within the developer community, and
                continuously refine my skills to stay at the forefront of the ever-evolving tech landscape.
            </p>
            <br>

            <h6>Thank you for taking the time to view my portfolio.</h6>
            <p>
                Please find my Curriculum Vitae (CV) attached for your reference.
                To download my CV, please click <a href="doc/CV.pdf" download="CV.pdf">here</a>.
            </p>
            <br>
        </div>
    </div>
</div>


