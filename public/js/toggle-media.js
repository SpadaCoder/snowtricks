document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("toggle-media-btn");
    const mediaThumbnails = document.getElementById("media-thumbnails");

    toggleButton.addEventListener("click", function (event) {
        // Prévenir la soumission du formulaire
    event.preventDefault();
        if (mediaThumbnails.style.display === "none") {
            mediaThumbnails.style.display = "block";
            toggleButton.textContent = "Masquer les médias";  // Change le texte du bouton
        } else {
            mediaThumbnails.style.display = "none";
            toggleButton.textContent = "Voir les médias";  // Remet le texte du bouton
        }
    });
});