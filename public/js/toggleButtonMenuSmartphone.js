const btnIconMenu = document.querySelector('.icon-menu-barre');
const dropupContent = document.querySelector('.dropup-content');
const containerBody = document.querySelector('.container-body');

btnIconMenu.addEventListener('click', () => { 

    if(dropupContent.style.display === 'none') {
        dropupContent.style.display = 'block';
    } else {
        dropupContent.style.display = 'none';
        containerBody.style.height = 'calc(100vh+160px)';
    }
});