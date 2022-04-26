window.onload = async (e) => { 

    //Asynchrone response ajax
    const response = await fetch('https://picsum.photos/v2/list');

    if(response.ok) {
        const data = await response.json();
        console.log(data);

    }


    const url = e.target.dataset.url;
    //todo si url contient https
    
    //renvoi en string sur l'attribut src le lien d'illustartion fixture 


}