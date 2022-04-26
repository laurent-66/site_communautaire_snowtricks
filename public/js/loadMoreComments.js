    const element = document.querySelector('#load-more');
    const containerfigures = document.querySelector('#container-figures');
    const loader = document.querySelector('.loading');
    const arrowUpLinkEnabled = document.querySelector(".linkEnabled");
    const arrowUpLinkDisabled = document.querySelector(".linkDisabled");

    element.addEventListener('click', async (e) => { 
    loader.style.display='block';	
    const totalPage = e.target.dataset.totalPage;
    const nextPage = e.target.dataset.nextPage;
    const urlToCallAjax = e.target.dataset.commentAjax;

    //Asynchrone response ajax
    const response = await fetch(`${urlToCallAjax}?page=${nextPage}`);

    console.log(response);
    

    if(response.ok) {
        loader.style.display='none';
        const data = await response.json();
        // if all page display then disabled button
        if((Number(nextPage)) === Number(totalPage)) {
            element.disabled = true;
        }
        // else increment to one the attribute value "data-next-page" in tag button
        element.dataset.nextPage = Number(nextPage) + 1;

        //add comment bellow page
        containerfigures.innerHTML += data.html;

        //define the quantity of comment
        let quantityComments = 0;
        for (let i = 0; i < containerfigures.children.length; i++) {
            quantityComments ++;
        }

        if( quantityComments > 5) {
            arrowUpLinkEnabled.style.display='block';
            arrowUpLinkDisabled.style.display='none';
        } 
    } 

    }); 