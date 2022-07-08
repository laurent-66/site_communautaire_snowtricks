const element = document.querySelector('#load-more');
const containerfigures = document.querySelector('#container-figures');
const itemsComment = document.querySelector(".items-comment");
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
            let quantityComments = containerfigures.children.length;

            if( quantityComments > 4) {
                arrowUpLinkEnabled.style.display='block';
            } 

        } 
}) 