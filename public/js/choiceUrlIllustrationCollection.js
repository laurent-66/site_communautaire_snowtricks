

window.onload = async (e) => { 

const url = e.target.dataset.url;

    //Asynchrone response ajax
    const response = await fetch('https://picsum.photos/v2/list');

    if(response.ok) {
        const data = await response.json();
        console.log(data);

    }

}