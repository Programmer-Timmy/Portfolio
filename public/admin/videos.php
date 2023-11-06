<?php
if (!isset($_SESSION['access'])) {
    header('location: ../admin');
}

videos::add();

echo '<script>alert("De video\'s zijn geupdate");
window.location.replace("/admin");
</script>'
?>
