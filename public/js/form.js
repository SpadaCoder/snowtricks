document.addEventListener('DOMContentLoaded', function () {
    const addVideoButton = document.querySelector('.add-video');
    const videoCollection = document.getElementById('video-collection');
    const videoPrototype = videoCollection.dataset.prototype;

    let videoCount = videoCollection.children.length;

    addVideoButton.addEventListener('click', function () {
        const newVideo = videoPrototype.replace(/__name__/g, videoCount);
        videoCount++;

        const div = document.createElement('div');
        div.innerHTML = newVideo;
        videoCollection.appendChild(div);
    });

    videoCollection.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-video')) {
            event.target.closest('.video').remove();
        }
    });
});