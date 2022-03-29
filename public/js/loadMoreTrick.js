    const element = document.querySelector('#load-more');
    const containerfigures = document.querySelector('#container-figures');
    const loader = document.querySelector('.loading');
    const arrowUpLinkEnabled = document.querySelector(".linkEnabled");
    const arrowUpLinkDisabled = document.querySelector(".linkDisabled");

    element.addEventListener('click', async (e) => { 
    loader.style.display='block';	
    const totalPage = e.target.dataset.totalPage;
    const nextPage = e.target.dataset.nextPage;
    const urlToCallAjax = e.target.dataset.figureAjax;

    //Asynchrone response ajax
    const response = await fetch(`${urlToCallAjax}?page=${nextPage}`);

    if(response.ok) {
        loader.style.display='none';
        const data = await response.json();
        // if all page display thon disabled button
        if((Number(nextPage)) === Number(totalPage)) {
            element.disabled = true;
        }
        // else increment to one the attribute value "data-next-page" in tag button
        element.dataset.nextPage = Number(nextPage) + 1;
        //add figures bellow page
        containerfigures.innerHTML += data.html;

        //define the quantity of trick
        let quantityCards = 0;
        for (let i = 0; i < containerfigures.children.length; i++) {
            cardsByrow = containerfigures.children[i].children.length;
            quantityCards += cardsByrow;
        }

        if( quantityCards > 12) {
            arrowUpLinkEnabled.style.display='block';
            arrowUpLinkDisabled.style.display='none';
        } 
    } 

    });