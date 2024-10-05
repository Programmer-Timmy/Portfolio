<?php
global $site;
?>

<footer>
    <div class="container">
        <nav>
            <ul class="footer-menu">
                <li><a href="/home">Home</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/contact">Contact</a></li>
                <li><a href="/projects">Projects</a></li>
                <li><a href="/videos">Video's</a></li>
            </ul>
        </nav>
        <p>&copy; <?php echo date('Y') . ' ' . $site['siteName']; ?>. All rights reserved.</p>
        <ul class="social-links">
            <!-- Load social icons lazily -->
            <li><a href="https://www.linkedin.com/in/tim-van-der-kloet" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fa-brands fa-linkedin lazyload"></i></a></li>
            <li><a href="https://github.com/Programmer-Timmy" target="_blank" rel="noopener noreferrer" aria-label="GitHub"><i class="fa-brands fa-github lazyload"></i></a></li>
            <li><a href="mailto:Tim.vanderkloet@gmail.com" aria-label="Email"><i class="fa-solid fa-envelope lazyload"></i></a></li>
        </ul>
    </div>
</footer>

<!-- Defer non-critical JavaScript -->
<script defer src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
        crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V"
        crossorigin="anonymous"></script>

<!-- Lazy load fonts and icons -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var lazyIcons = document.querySelectorAll('.lazyload');
        lazyIcons.forEach(function(icon) {
            var iconClass = icon.className.replace('lazyload', '').trim();
            icon.className = iconClass;
        });
    });
</script>
