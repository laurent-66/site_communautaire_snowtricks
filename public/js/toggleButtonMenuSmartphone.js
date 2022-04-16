const btnIconMenu = document.querySelector('.icon-menu-barre');
const dropupContent = document.querySelector('.dropup-content');

btnIconMenu.addEventListener('click', () => { 

    if(dropupContent.style.display === 'none') {
        dropupContent.style.display = 'block';
    } else {
        dropupContent.style.display = 'none';
    }
});