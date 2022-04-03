const illustrations = document.querySelectorAll('ul.illustrations.li');
const videos = document.querySelectorAll('ul.videos.li');

if ( illustrations.length > 0 ) {

    illustrations.forEach((illustration) => {
        addTagFormDeleteLink(illustration)
    })

}

if ( videos.length > 0 ) {

    videos.forEach((video) => {
        addTagFormDeleteLink(video)
    })

}

function addTagFormDeleteLink(item) {

    const removeFormButton = document.createElement('button');
    removeFormButton.innerText = 'Supprimer le media';

    item.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the tag form
        item.remove();
    });
}