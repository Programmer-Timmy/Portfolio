<?php
$tz = new DateTimeZone('Europe/Brussels');
$age = DateTime::createFromFormat('d/m/Y', '03/08/2005', $tz)
    ->diff(new DateTime('now', $tz))
    ->y;
?>

<div class="welcome">
    <h1>About me!</h1>
</div>
<div class="border" style="padding-bottom: 40px">
    <div>
        <img src="img/profielfoto.JPG" id="foto" alt="">
    </div>
    <div id="tekst">
        <h2>Hello, my name is Tim, and I am a <?php echo $age ?>-year-old studying software development at ICT Campus school.</h2><br>
        <h3>My current focus is on learning various programming languages, such as HTML, CSS, PHP, jQuery, and JavaScript. I am passionate about software development and excited about the possibilities it offers.<br><br>
            When I'm not studying, I enjoy playing games and sailing during the summer months. In the winter, I enjoy repairing and painting boats, which is a fun and creative activity. Additionally, I have been a member of the scouting group Het ZuiderKruis for seven years, which has allowed me to develop important teamwork and leadership skills.</h3><br>
        <!-- Added sections -->
        <h2>My Vision on Work</h2><br>
        <h3>As I pursue my studies in software development, my vision is to become a versatile and innovative professional in the tech industry. I am enthusiastic about leveraging my programming skills to contribute to cutting-edge projects that make a positive impact on society. I aspire to continuously learn and adapt to emerging technologies, fostering a dynamic and collaborative approach to problem-solving. Ultimately, I aim to create software solutions that not only meet technical requirements but also address real-world challenges, making a meaningful difference in the world of technology.</h3>
        <br>
        <h2>What I Want to Achieve</h2>
        <br>
        <h3>Looking forward, I aim to achieve proficiency in a wide range of programming languages and frameworks, allowing me to tackle diverse and complex projects. I envision being an integral part of innovative teams, where creativity and collaboration drive the development of impactful solutions. Additionally, I aspire to contribute to open-source projects, share knowledge within the developer community, and continuously refine my skills to stay at the forefront of the ever-evolving tech landscape.</h3>
        <br>
        <h2>Thank you for taking the time to view my portfolio.</h2><br>
        <h3>Please find my Curriculum Vitae (CV) attached for your reference.
            To download my CV, please click
            <a href="doc/CV.pdf" download="CV.pdf">here</a>.</h3><br>
    </div>
</div>