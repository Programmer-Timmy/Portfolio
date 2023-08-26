function showPopup(path, img, name, description) {
    event.preventDefault();
    document.getElementById("popup-title").textContent = name;
    document.getElementById("popup-description").innerHTML = description;
    document.getElementById("show-button").setAttribute("href",path);
    document.getElementById("popup").style.top = "50%";
}

function closePopup() {
    console.log("closePopup() called");
    document.getElementById("popup").style.top = "-400px";

}