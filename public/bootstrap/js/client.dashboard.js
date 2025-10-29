document.addEventListener("DOMContentLoaded", () => {
    //btn add client
    const btnAddClient = document.getElementById("btn-add-client");
    //--modal add client
    const modalAC = document.getElementById("modal-add-client");
    const modalACB = new bootstrap.Modal(modalAC);

    //tbody client
    const tbodyClient = document.getElementById('tbody-client');

    //modal update client
    const modalUC = document.getElementById("modal-update-client");
    const modalUCB = new bootstrap.Modal(modalUC);

    //hidden numCompte
    const hiddenNumCompte = document.createElement("input");
    hiddenNumCompte.type = "hidden";

    //modal delete client
    const modalDC = document.getElementById("modal-delete-client");
    const modalDCB = new bootstrap.Modal(modalDC);

    //input search client
    const inputSearchClient = document.getElementById("input-search-client");


    //------------------BTN ADD CLIENT CLICKED-------------------
    btnAddClient.addEventListener("click", () => {

        //show modal add client
        modalACB.show();
    });
    //------------------BTN SAVE ADD CLIENT CLICKED--------------
    const btnSaveAddClient = document.getElementById("btn-save-add-client");
    //--------------------input nom
    const nom = document.getElementById("add-nom");
    nom.addEventListener('keyup', () => {
        nom.value = nom.value.replace(/\s+/g, " ");
    });
    const nomError = document.getElementById("add-nom-error");
    //--------------------input prenoms
    const prenoms = document.getElementById("add-prenoms");
    prenoms.addEventListener('keyup', () => {
        prenoms.value = prenoms.value.replace(/\s+/g, " ");
    });
    //--------------------input tel
    const tel = document.getElementById("add-tel");
    tel.addEventListener('keyup', () => {
        tel.value = tel.value.replace(/\s+/g, "");
    });
    const telError = document.getElementById("add-tel-error");
    //--------------------input email
    const emailClient = document.getElementById("add-email");
    emailClient.addEventListener('keyup', () => {
        emailClient.value = emailClient.value.replace(/\s+/g, "");
    });
    const emailClientError = document.getElementById("add-email-error");

    btnSaveAddClient.addEventListener("click", () => {

        //nom empty
        if (nom.value.trim() === "") {
            nomError.classList.replace("d-none", "d-block");
        }
        //nom !empty
        else {
            nomError.classList.replace("d-block", "d-none");

            //tel !valid
            if (!tel.checkValidity()) {
                telError.classList.replace('d-none', 'd-block');
            }
            //tel valid
            else {
                telError.classList.replace('d-block', 'd-none');

                //email !valid
                if (!emailClient.checkValidity()) {
                    emailClientError.classList.replace('d-none', 'd-block');
                }
                //email valid
                else {
                    emailClientError.classList.replace('d-block', 'd-none');

                    //AJAX client / add client
                    fetch("../../../public/index.php?route=add_client/controller", {
                        method: 'POST',
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            Nom: nom.value.trim().replace(/\s+/g, " "),
                            Prenoms: prenoms.value.trim().replace(/\s+/g, " "),
                            Tel: tel.value.trim().replace(/\s+/g, ""),
                            emailClient: emailClient.value.trim().replace(/\s+/g, "")
                        })
                    })
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            }
                            else {
                            }
                            console.log("AJAX no respone / add client");
                        })
                        .then(data => {

                            //ajax response success
                            if (data === "success") {

                                //alert add-client-success
                                const alert = document.createElement('div');
                                alert.classList.add("alert", "alert-success", "fade",
                                    "show", "alert-dismissible", "mt-2");
                                alert.setAttribute("role", "alert");
                                alert.innerHTML = `
                                        <i class='fas fa-check-circle me-2'></i>
                                        Un <b>nouveau client</b> a été ajouté avec succès .
                                        <button type='button' class='btn btn-close'
                                            data-bs-dismiss='alert'></button>
                                    `;
                                //insert alert
                                const divTable = document.querySelector(".table-container");
                                divTable.parentNode.insertBefore(alert, divTable);

                                //hide modal
                                modalACB.hide();

                                //refresh listClientAll
                                listClientAll();
                            }
                            //ajax response !success
                            else {

                                //alert add client !success
                                const alert = document.createElement('div');
                                alert.classList.add("alert", "alert-warning", "fade",
                                    "show", "alert-dismissible", "my-2");
                                alert.setAttribute("role", "alert");
                                alert.innerHTML = `
                                        <i class='fas fa-warning me-2'></i>
                                        ${data}
                                        <button type='button' class='btn btn-close'
                                            data-bs-dismiss='alert'></button>
                                    `;

                                modalUC.querySelector(".modal-body").insertBefore(alert,)
                            }
                        })
                        .catch(error => {
                            console.error("Erreur add client : " + error);
                        });
                }
            }
        }
    });

    //-------------LIST CLIENT ALL----------------
    //function listClientAll
    function listClientAll() {
        //AJAX list client all
        fetch("../../../public/index.php?route=list_client_all/controller")
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX didn't respond / list client all");
                }
            })
            .then(data => {
                //reset tbody-client
                tbodyClient.innerHTML = "";

                //response list empty
                if (data.message === "list empty") {
                    //tr
                    const tr = document.createElement("tr");
                    //td
                    const td = document.createElement("td");
                    td.colSpan = "6";

                    //alert list empty
                    const alertLCE = document.querySelector(".alert-list-client-empty");
                    if (!alertLCE) {
                        //alert
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-info", "fade",
                            "show", "alert-list-client-empty");
                        alert.setAttribute("role", "alert");
                        alert.innerHTML = `
                                    <i class='fas fa-info-circle me-2'></i>
                                    Liste des clients est vide pour le moment .
                                `;

                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClient.appendChild(tr);
                    }
                }
                // response list not empty
                else if (data.message === "list not empty") {
                    createElementTable(data.list);
                }
                //error
                else if (data.message === "error") {
                    //tr
                    const tr = document.createElement("tr");
                    //td
                    const td = document.createElement("td");
                    td.colSpan = "6";

                    //alert list empty
                    const alertLCError = document.querySelector(".alert-list-client-error");
                    if (!alertLCError) {
                        //alert
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning", "fade",
                            "show", "alert-list-client-error");
                        alert.setAttribute("role", "alert");
                        alert.innerHTML = `
                                    <i class='fas fa-warning me-2'></i>
                                    ${data.error_message} .
                                `;

                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClient.appendChild(tr);
                    }
                }
            })
            .catch(error => {
                console.error("Erreur ajax list client all : " + error);
            });
    }
    listClientAll();

    //----------------------CREATE TBODY-----------------------

    //element of modal update
    const h6UpdateClientNumCompte = document.getElementById('h6-update-client-numCompte');
    const nomUC = document.getElementById("update-nom");
    const prenomsUC = document.getElementById("update-prenoms");
    const telUC = document.getElementById("update-tel");
    const emailUC = document.getElementById("update-email");
    const soldeUC = document.getElementById("update-solde");

    //function createElementTable
    function createElementTable(data) {
        //reset tbody
        tbodyClient.innerHTML = "";

        //create element
        data.forEach(item => {
            //tr
            const tr = document.createElement("tr");

            //td numCompte
            const tdNumCompte = document.createElement("td");
            tdNumCompte.textContent = item.numCompte;
            //td Nom et prénoms
            const tdNomPrenoms = document.createElement("td");
            tdNomPrenoms.textContent = item.Nom + " " + item.Prenoms;
            //td Tel
            const tdTel = document.createElement("td");
            tdTel.textContent = item.Tel;
            //td emailClient
            const tdEmailClient = document.createElement("td");
            tdEmailClient.textContent = item.emailClient;
            //td solde
            const tdSolde = document.createElement("td");
            tdSolde.textContent = item.solde + " Ar";

            //td actions
            const tdActions = document.createElement("td");
            tdActions.classList.add("gap-2", "d-flex");
            //--btn update
            const btnUpdate = document.createElement("button");
            btnUpdate.classList.add("text-primary", "btn-light", "btn", "btn-update-client");
            const iBtnUpdate = document.createElement("i");
            iBtnUpdate.classList.add("fas", "fa-user-edit");
            btnUpdate.appendChild(iBtnUpdate);
            //#btn delete
            const btnDelete = document.createElement("button");
            btnDelete.classList.add("text-danger", "btn-light", "btn", "btn-delete-client");
            const iBtnDelete = document.createElement("i");
            iBtnDelete.classList.add("fas", "fa-trash-alt");
            btnDelete.appendChild(iBtnDelete);

            tdActions.appendChild(btnUpdate);
            tdActions.appendChild(btnDelete);

            tr.appendChild(tdNumCompte);
            tr.appendChild(tdNomPrenoms);
            tr.appendChild(tdTel);
            tr.appendChild(tdEmailClient);
            tr.appendChild(tdSolde);
            tr.appendChild(tdActions);
            tbodyClient.appendChild(tr);
        });

        //-------------------BTN UPDATE CLICKED-----------------
        const btnUpdate = document.querySelectorAll(".btn-update-client");
        btnUpdate.forEach(btn => {
            btn.addEventListener("click", () => {

                //tr
                const tr = btn.closest("tr");

                //hidden numCompte
                hiddenNumCompte.value = tr.querySelector("td:first-child").textContent.trim();

                //numCompte h6
                h6UpdateClientNumCompte.innerHTML = `Numéro :
                 <b>${hiddenNumCompte.value}</b>`;

                //nomPrenoms
                const nomPrenoms = tr.querySelector("td:nth-child(2)").textContent.trim().split(" ");

                //nomUpdate value
                nomUC.value = nomPrenoms[0];

                //prenomsUpdate value
                if (nomPrenoms.length > 1) {
                    prenomsUC.value = nomPrenoms.slice(1).join(" ");
                }

                //telUpdate value
                telUC.value = tr.querySelector("td:nth-child(3)").textContent.trim();

                //emailUC value
                emailUC.value = tr.querySelector("td:nth-child(4)").textContent.trim();

                //soldeUC value
                soldeUC.value = tr.querySelector("td:nth-child(5)").textContent.trim().split(" ")[0];

                //show modal update client
                modalUCB.show();
            });
        });

        //-------------------BTN DELETE CLICKED-----------------
        const btnDelete = document.querySelectorAll(".btn-delete-client");
        btnDelete.forEach(btn => {
            btn.addEventListener("click", () => {

                //hidden numCompte
                hiddenNumCompte.value = btn.closest("tr").querySelector("td:first-child").textContent.trim();

                //show modal confirm delete
                const p = modalDC.querySelector("p");
                p.innerHTML = "Toute les <b>transactions</b> de ce compte numéro <b>" + hiddenNumCompte.value.trim() + "</b> seront <b>supprimées</b> aussi !";
                modalDCB.show();

            });
        });

    }

    //-------------------BTN SAVE UPDATE CLIENT-----------------
    const btnSaveUpdateClient = document.getElementById("btn-save-update-client");
    //--------------------input nomUC
    nomUC.addEventListener('keyup', () => {
        nomUC.value = nomUC.value.replace(/\s+/g, " ");
    });
    const nomUCError = document.getElementById("update-nom-error");
    //--------------------input prenomsUC
    prenomsUC.addEventListener('keyup', () => {
        prenomsUC.value = prenomsUC.value.replace(/\s+/g, " ");
    });
    //--------------------input telUC
    telUC.addEventListener('keyup', () => {
        telUC.value = telUC.value.replace(/\s+/g, "");
    });
    const telUCError = document.getElementById("update-tel-error");
    //--------------------input emailUC
    emailUC.addEventListener('keyup', () => {
        emailUC.value = emailUC.value.replace(/\s+/g, "");
    });
    const emailUCError = document.getElementById("update-email-error");
    //--------------------input soldeUC
    soldeUC.addEventListener("change", () => {
        if (soldeUC.value < 0) {
            soldeUC.value = 0;
        }
    });
    const soldeUCError = document.getElementById("update-solde-error");

    btnSaveUpdateClient.addEventListener("click", () => {

        //nomUC empty
        if (nomUC.value.trim() === "") {
            nomUCError.classList.replace("d-none", "d-block");
        }
        //nomUC !empty
        else {
            nomUCError.classList.replace("d-block", "d-none");

            //telUC !valid
            if (!telUC.checkValidity()) {
                telUCError.classList.replace('d-none', 'd-block');
            }
            //telUC valid
            else {
                telUCError.classList.replace('d-block', 'd-none');

                //emailUC !valid
                if (!emailUC.checkValidity()) {
                    emailUCError.classList.replace('d-none', 'd-block');
                }
                //emailUC valid
                else {
                    emailUCError.classList.replace('d-block', 'd-none');

                    //soldeUC !valid
                    if (!soldeUC.checkValidity()) {
                        soldeUCError.classList.replace('d-none', 'd-block');
                    }
                    //sodleUC valid
                    else {
                        soldeUCError.classList.replace('d-block', 'd-none');

                        //AJAX update client
                        fetch("../../../public/index.php?route=update_client/controller", {
                            method: 'POST',
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({
                                numCompte: hiddenNumCompte.value.trim(),
                                Nom: nomUC.value.trim(),
                                Prenoms: prenomsUC.value.trim(),
                                Tel: telUC.value.trim(),
                                emailClient: emailUC.value.trim(),
                                solde: soldeUC.value.trim()
                            })
                        })
                            .then(response => {
                                if (response.ok) {
                                    return response.json();
                                }
                                else {
                                    console.log("AJAX no response / update client");
                                }
                            })
                            .then(data => {

                                //ajax response success
                                if (data === "success") {

                                    //alert update-client-success
                                    const alert = document.createElement('div');
                                    alert.classList.add("alert", "alert-success", "fade",
                                        "show", "alert-dismissible", "mt-2");
                                    alert.setAttribute("role", "alert");
                                    alert.innerHTML = `
                                                <i class='fas fa-check-circle me-2'></i>
                                                Les informations  du client numéro <b>${hiddenNumCompte.value}</b>
                                                 ont été modifée avec succès .
                                                <button type='button' class='btn btn-close'
                                                    data-bs-dismiss='alert'></button>
                                            `;
                                    //insert alert
                                    const divTable = document.querySelector(".table-container");
                                    divTable.parentNode.insertBefore(alert, divTable);

                                    //hide modal
                                    modalUCB.hide();

                                    if (inputSearchClient.value.trim() === "") {
                                        //#refresh listClientAll
                                        listClientAll();
                                    }
                                    else {
                                        //#refresh searchClient
                                        searchClient();
                                    }
                                }
                                //ajax response !success
                                else {

                                    //alert update client !success
                                    const alert = document.createElement('div');
                                    alert.classList.add("alert", "alert-warning", "fade",
                                        "show", "alert-dismissible", "my-2");
                                    alert.setAttribute("role", "alert");
                                    alert.innerHTML = `
                                        <i class='fas fa-warning me-2'></i>
                                        ${data}
                                        <button type='button' class='btn btn-close'
                                            data-bs-dismiss='alert'></button>
                                    `;

                                    //modal body
                                    const modalUCBody = modalUC.querySelector(".modal-body");
                                    modalUCBody.insertBefore(alert, modalUCBody.querySelector("#div-first-child"));
                                }

                            })
                            .catch(error => {
                                console.error("Erreur update client : " + error);
                            });
                    }
                }
            }
        }

    });
    //----------------BTN SAVE DELETE CLIENT----------
    const btnSaveDeleteClient = document.getElementById("btn-save-delete-client");
    btnSaveDeleteClient.addEventListener("click", () => {

        //AJAX delete client
        fetch("../../../public/index.php?route=delete_client/controller", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ numCompte: hiddenNumCompte.value.trim() })
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX no response / delete client");
                }
            })
            .then(data => {

                // //delete success
                if (data === "success") {

                    //alert delete-client-success
                    const alert = document.createElement('div');
                    alert.classList.add("alert", "alert-success", "fade",
                        "show", "alert-dismissible", "mt-2");
                    alert.setAttribute("role", "alert");
                    alert.innerHTML = `
                                 <i class='fas fa-check-circle me-2'></i>
                                 Le client avec le numéro du compte 
                                 <b>${hiddenNumCompte.value}</b>
                                  a été supprimé avec succès .
                                 <button type='button' class='btn btn-close'
                                     data-bs-dismiss='alert'></button>
                             `;
                    //insert alert
                    const divTable = document.querySelector(".table-container");
                    divTable.parentNode.insertBefore(alert, divTable);

                    //hide modal
                    modalDCB.hide();

                    if (inputSearchClient.value.trim() === "") {
                        //#refresh listClientAll
                        listClientAll();
                    }
                    else {
                        //#refresh searchClient
                        searchClient();
                    }
                }
                else {

                    //alert delete-client-error
                    const alert = document.createElement('div');
                    alert.classList.add("alert", "alert-warning", "fade",
                        "show", "alert-dismissible", "my-2");
                    alert.setAttribute("role", "alert");
                    alert.innerHTML = `
                                        <i class='fas fa-warning me-2'></i>
                                        ${data}
                                        <button type='button' class='btn btn-close'
                                            data-bs-dismiss='alert'></button>
                                    `;
                    modalDC.querySelector(".modal-body").prepend(alert);
                }
            })
            .catch(error => {
                console.error("Erreur ajax delete client : " + error);
            });
    });



    //---------------------INPUT SEARCH CLIENT----------------------

    //inputSearch keyup
    inputSearchClient.addEventListener("keyup", () => {

        //list all
        if (inputSearchClient.value.trim() === "") {
            listClientAll();
        }
        //search client
        else {
            searchClient();
        }

    });

    //function search client
    function searchClient() {

        tbodyClient.innerHTML = "";

        //AJAX search client
        fetch("../../../public/index.php?route=search_client/controller", {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ searchValue: inputSearchClient.value.trim() })
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX didn't respond / search client");
                }
            })
            .then(data => {

                //not found
                if (data.message === "found") {
                    createElementTable(data.list);
                }
                //found
                else if (data.message === "not found") {

                    //tr
                    const tr = document.createElement("tr");

                    //td
                    const td = document.createElement("td");
                    td.colSpan = "6";

                    //alert client-not-found
                    const alertCNF = document.querySelector(".alert-client-not-found");
                    if (!alertCNF) {
                        //alert
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning", "fade",
                            "show", "alert-client-not-found");
                        alert.setAttribute("role", "alert");
                        alert.innerHTML = `
                                   <i class='fas fa-info-circle me-2'></i>
                                   Aucun résultat trouvé .
                               `;

                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClient.appendChild(tr);
                    }
                }
                //error
                else if (data.message === "error") {
                    //tr
                    const tr = document.createElement("tr");

                    //td
                    const td = document.createElement("td");
                    td.colSpan = "6";

                    //alert -search-client-error
                    const alertCNF = document.querySelector(".alert-search-client-error");
                    if (!alertCNF) {
                        //alert
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning", "fade",
                            "show", "alert-search-client-error");
                        alert.setAttribute("role", "alert");
                        alert.innerHTML = `
                                   <i class='fas fa-warning me-2'></i>
                                   ${data.error_message} .
                               `;

                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClient.appendChild(tr);
                    }
                }
            })
            .catch(error => {
                console.error("Erreur ajax / search client : " + error);
            });
    }

    //--------------BTN SEARCH---------
    const btnSearch = document.getElementById("btn-search");
    btnSearch.addEventListener('click', () => {
        searchClient();
    });


    const totalB = document.getElementById("total-client");

    function totalClient() {
        fetch("../../../public/index.php?route=total_client")
        .then(response => {
            if(response.ok){
                return response.json();
            }
        })
        .then(data =>{
            totalB.innerHTML = `Total client : <b>${data}</b> clients`;
        })
        .catch(error =>{
            console.error(error);
        });
    }
    totalClient();
});