const btnSeeMedias = document.querySelector('.btn-see-media');
const btnHiddenMedias = document.querySelector('.btn-hidden-media');
const mediasList = document.querySelector('.medias-list-smartphone');
const carrouselDesktop = document.querySelector('.carrousel-desktop');

btnSeeMedias.addEventListener('click', () => { 
    mediasList.style.display = 'block';
    btnSeeMedias.style.display ='none';
    btnHiddenMedias.style.display = 'block';
});

btnHiddenMedias.addEventListener('click', () => { 
    mediasList.style.display = 'none';
    btnHiddenMedias.style.display = 'none';
    btnSeeMedias.style.display ='block';
});