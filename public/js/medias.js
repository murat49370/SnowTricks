window.onload = () => {
    //Gestion du lien supprimer
    let links = document.querySelectorAll("[data-delete]")

    //On boucle sur links
    for(link of links)
    {
        //On ecoute le clic
        link.addEventListener("click", function(e)
        {
            // On empêche la naviagation
            e.preventDefault()

            // On demande comfirmation
            if(confirm("Voulez-vous supprimer ce media?"))
            {
                // On envoie une requete ajax vers le href du lien avec la méthode delete
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    //On recupere la reponse en json
                    response => response.json()
                ).then(data => {
                    if(data.success)
                       this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}

