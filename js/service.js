
document.querySelectorAll(".video-box").forEach(box => {
    box.addEventListener("click", function () {

        let url = this.getAttribute("data-video") + "?autoplay=1";
        document.getElementById("popupVideo").src = url;

        let modal = new bootstrap.Modal(document.getElementById("videoModal"));
        modal.show();
    });
});

document.getElementById("videoModal").addEventListener("hidden.bs.modal", function () {
    document.getElementById("popupVideo").src = "";
});
