function showPopup(json) {
    event.preventDefault();
    document.getElementById("login-title").style.display = "none";
    document.getElementById("guest_password").style.display = "none";
    document.getElementById("guest_username").style.display = "none";
    document.getElementById("popup-title").textContent = json[1];
    document.getElementById("popup-description").innerHTML = json[2];
    document.getElementById("show-button").setAttribute("href", json[0]);

    if (json[4] !== null) {
        document.getElementById("login-title").style.display = "block";
        document.getElementById("guest_username").style.display = "block";
        document.getElementById("guest_username").innerHTML = "Guest username: " + json[4];
    }
    if (json[3] !== null) {
        document.getElementById("login-title").style.display = "block";
        document.getElementById("guest_password").style.display = "block";
        document.getElementById("guest_password").innerHTML = "Guest password: " + json[3];
    }
    document.getElementById("popup").style.top = "50%";

}

function closePopup() {
    document.getElementById("popup").style.top = "-400px";
    document.getElementById("login-title").style.display = "none";
    document.getElementById("guest_password").style.display = "none";
    document.getElementById("guest_username").style.display = "none";
}