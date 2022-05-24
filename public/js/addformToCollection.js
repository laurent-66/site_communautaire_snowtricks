
const addItemDeleteLink = (item) => {
    const removeFormButton = document.createElement('button');
    removeFormButton.innerText = 'Supprimer le media';

    item.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the tag form
        item.remove();
    });
}



const addFormToCollection = (e) => {

    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
        /__name__/g,
        collectionHolder.dataset.index
        );

    collectionHolder.appendChild(item);
    addItemDeleteLink(item)
    collectionHolder.dataset.index++;


    };


    document.querySelectorAll('.add_item_link_image')
    .forEach(btn => btn.addEventListener("click", addFormToCollection));

    document.querySelectorAll('.add_item_link_video')
    .forEach(btn => btn.addEventListener("click", addFormToCollection));