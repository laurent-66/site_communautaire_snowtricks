document
    .querySelectorAll('add_item_link_image.tags li')
    .forEach((illustration) => {
        addTagFormDeleteLink(illustration)
    })

const addTagFormDeleteLink = (item) => {
    const removeFormButton = document.createElement('button');
    removeFormButton.innerText = 'Supprimer l\'illustration';

    item.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the tag form
        item.remove();
    });
}