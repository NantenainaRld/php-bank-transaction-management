document.addEventListener("DOMContentLoaded", () => {
    //tbody client
    const tbodyClient = document.getElementById("tbody-client-pret");
    //modal pret
    const modal = document.getElementById("modal-pret");
    const modalB = new bootstrap.Modal(modal);
    //#p info pret
    const pInfoPret = document.getElementById("p-info-pret");
    //#input montant pret
    const inputMontantPret = document.getElementById("input-montant-pret");
    //#input duree
    const duree = document.getElementById("input-duree");
    //#btn save pret
    const btnSavePret = document.getElementById("btn-save-pret");

    //input-search-client
    const inputSearchClient = document.getElementById("input-search-client-pret");


    //hidden num_compte
    const hidden = document.createElement("input");
    hidden.type = "hidden";
    //hiden nom prenoms
    const hiddenNP = document.createElement("input");
    hiddenNP.type = "hidden";

    //tbody pret
    const tbodyPret = document.getElementById("tbody-pret");

    //modal delete pret
    const modalDP = document.getElementById("modal-delete-pret");
    const modalDPB = new bootstrap.Modal(modalDP);

    //hidden code pret
    const hiddenCP = document.createElement("input");
    hiddenCP.type = "hidden";

    ///searh pret
    const codePret = document.getElementById('input-code-pret');
    const num_compte = document.getElementById('input-num-compte');
    const situation = document.getElementById('situation');
    const dateDu = document.getElementById('date-du');
    const dateAu = document.getElementById('date-au');

    //------------------------LIST------------------------

    //function listClientAll
    function listClientAll() {

        //AJAX list client all
        fetch("../../../public/index.php?route=virement/list_client_all/controller")
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX no response virement / list client all");
                }
            })
            .then(data => {
                //reset tbody client
                tbodyClient.innerHTML = "";

                //response list empty
                if (data.message === "list empty") {

                    //tr
                    const tr = document.createElement("tr");

                    //td
                    const td = document.createElement("td");
                    td.colSpan = "4";

                    //alert virement-list-client-empty
                    const alertVLCE = document.querySelector(".alert-virement-list-client-empty");
                    if (!alertVLCE) {
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-info",
                            "alert-virement-list-client-empty");
                        const iAlert = document.createElement("i");
                        iAlert.classList.add("fas", "fa-info-circle", "me-2");
                        const textNode = document.createTextNode("Liste des clients est vide pour le moment .");

                        alert.appendChild(iAlert);
                        alert.appendChild(textNode);
                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClient.appendChild(tr);
                    }
                }
                //list not empty
                else if (data.message === "list not empty") {
                    createTbodyClient(data.list);
                }
                //response error
                else if (data.message === "error") {
                    //tr
                    const tr = document.createElement("tr");

                    //td
                    const td = document.createElement("td");
                    td.colSpan = "4";

                    //alert depot-list-client-empty
                    const alertDELC = document.querySelector(".alert-virement-error-list-clients");
                    if (!alertDELC) {
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning",
                            "alert-virement-error-list-client");
                        alert.innerHTML = `
                            <i class='fas fa-warning me-2'></i>
                            ${data.error_message}
                        `;

                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClient.appendChild(tr);
                    }
                }

            })
            .catch(error => {
                console.error("Erreur ajax pret / list client all : " + error);
            });
    }
    listClientAll();

    //function list pret all
    function listPretAll() {

        //AJAX list pret all
        fetch("../../../public/index.php?route=pret/list_pret_all/controller")
            .then(response => {

                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX didn't respond / list pret all");
                }
            })
            .then(data => {
                //reset tbody pret
                tbodyPret.innerHTML = "";

                //response list pret empty
                if (data === "list empty") {
                    //alert list empty
                    const alertLPE = document.querySelector(".alert-list-pret-empty");
                    if (!alertLPE) {
                        //tr
                        const tr = document.createElement("tr");
                        //td
                        const td = document.createElement("td");
                        td.colSpan = "7";

                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-info");
                        const iAlert = document.createElement("i");
                        iAlert.classList.add("fas", "fa-info-circle", "me-2");
                        const textNode = document.createTextNode("Aucun prêt pour le moment !");

                        alert.appendChild(iAlert);
                        alert.appendChild(textNode);
                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyPret.appendChild(tr);
                    }
                }
                //response list !empty
                else {
                    createPretElement(data);
                }
            })
            .catch(error => {
                console.error("Erreur ajax / list pret all : " + error);
            });
    }
    listPretAll();


    //-----------------------CREATE ELEMENT--------------------------

    //function create tbody client element
    function createTbodyClient(data) {
        //restore tbody
        tbodyClient.innerHTML = "";

        data.forEach(item => {
            //tr
            const tr = document.createElement("tr");


            //td numCompte
            const tdNumCompte = document.createElement("td");
            tdNumCompte.textContent = item.numCompte;
            //td nom prenoms
            const tdNomPrenoms = document.createElement("td");
            tdNomPrenoms.textContent = item.Nom + " " + item.Prenoms;
            //td solde
            const tdSolde = document.createElement("td");
            tdSolde.textContent = item.solde + " Ar";
            //td action
            const tdAction = document.createElement("td");
            const btnTransfert = document.createElement("button");
            btnTransfert.classList.add("btn", "btn-sm", "btn-light"
                , "d-flex", "text-primary", "justify-content-center", "btn-pret");
            const iBtnTransfert = document.createElement("i");
            iBtnTransfert.classList.add("fas", "fa-hand-holding-dollar", "me-2", "mt-1");
            const textNodeActionBtn = document.createTextNode("prêter");

            btnTransfert.appendChild(iBtnTransfert);
            btnTransfert.appendChild(textNodeActionBtn);
            tdAction.appendChild(btnTransfert);


            tr.appendChild(tdNumCompte);
            tr.appendChild(tdNomPrenoms);
            tr.appendChild(tdSolde);
            tr.appendChild(tdAction);
            tbodyClient.appendChild(tr);

        });


        //btn pret clicked/
        const btnPrets = document.querySelectorAll(".btn-pret");
        btnPrets.forEach(btn => {
            btn.addEventListener("click", () => {
                modalB.show();

                // hidden num_compte
                hidden.value = btn.closest("tr").querySelector("td:first-child").textContent.trim();
                // hidden nom prenoms
                hiddenNP.value = btn.closest("tr").querySelector("td:nth-child(2)").textContent.trim();
            });
        });


        //------------------------MODAL ADD PRET------------------------

        //input montant pret keyup
        inputMontantPret.addEventListener("keyup", (event) => {
            if (event.target.value.trim() === "") {
                pInfoPret.innerHTML = "";
            }
            else {
                pInfoPret.innerHTML = `Prêt de <b> ${event.target.value.trim()} Ar
                </b> pour le compte numéro <b> ${hidden.value.trim()} 
                (${hiddenNP.value.trim()})</b> dans <b>
                 ${duree.value.trim()} jours </b>`;
            }
        });
        //input duree keyup
        duree.addEventListener("keyup", (event) => {
            pInfoPret.innerHTML = `Prêt de <b> ${event.target.value.trim()} Ar
                </b> pour le compte numéro <b> ${hidden.value.trim()} 
                (${hiddenNP.value.trim()})</b> dans <b>
                 ${event.target.value.trim()} jours </b>`;
        });
        //btn save pret clicked
        btnSavePret.addEventListener("click", () => {

            //input empty
            if (inputMontantPret.value.trim() === "" || duree.value.trim() === "") {

                //alert input empty
                const alertP = document.querySelector(".alert-pret");

                if (!alertP) {
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning",
                        "alert-dismissible", "alert-pret");
                    const iAlert = document.createElement("i");
                    iAlert.classList.add("fas", "fa-info-circle", "me-2");
                    const btnClose = document.createElement("button");
                    btnClose.type = "button";
                    btnClose.classList.add("btn", "btn-close");
                    btnClose.setAttribute("data-bs-dismiss", "alert");
                    const textNode = document.createTextNode("Les deux champs sont obligatoires !");

                    alert.appendChild(iAlert);
                    alert.appendChild(textNode);
                    alert.appendChild(btnClose);
                    modal.querySelector(".modal-body").prepend(alert);
                }
                else {
                    alertP.innerHTML = `<i class="fas fa-info-circle me-2"></i>Les deux champs sont obligatoires !
                    <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>`;
                }
            }
            //montant < 5000
            else if (inputMontantPret.value < 5000) {

                //alert montant < 5000
                const alertP = document.querySelector(".alert-pret");
                if (!alertP) {
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning",
                        "alert-dismissible", "alert-pret");
                    const iAlert = document.createElement("i");
                    iAlert.classList.add("fas", "fa-info-circle", "me-2");
                    const btnClose = document.createElement("button");
                    btnClose.type = "button";
                    btnClose.classList.add("btn", "btn-close");
                    btnClose.setAttribute("data-bs-dismiss", "alert");
                    const textNode = document.createTextNode("Le montant minimum est 5000 Ar !");

                    alert.appendChild(iAlert);
                    alert.appendChild(textNode);
                    alert.appendChild(btnClose);
                    modal.querySelector(".modal-body").prepend(alert);
                }
                else {
                    alertP.innerHTML = `<i class="fas fa-info-circle me-2"></i>Le montant minimum est 5000 Ar !
                    <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>`;
                }
            }
            //duree < 7
            else if (duree.value < 7) {

                //alert duree < 7
                const alertP = document.querySelector(".alert-pret");
                if (!alertP) {
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning",
                        "alert-dismissible", "alert-pret");
                    const iAlert = document.createElement("i");
                    iAlert.classList.add("fas", "fa-info-circle", "me-2");
                    const btnClose = document.createElement("button");
                    btnClose.type = "button";
                    btnClose.classList.add("btn", "btn-close");
                    btnClose.setAttribute("data-bs-dismiss", "alert");
                    const textNode = document.createTextNode("La durée minimum est 7 jours !");

                    alert.appendChild(iAlert);
                    alert.appendChild(textNode);
                    alert.appendChild(btnClose);
                    modal.querySelector(".modal-body").prepend(alert);
                }
                else {
                    alertP.innerHTML = `<i class="fas fa-info-circle me-2"></i>La durée minimum est 7 jours !
                    <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>`;
                }
            }
            //add pret
            else {

                //AJAX add pret
                fetch("../../../public/index.php?route=pret/add_pret/controller", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        num_compte: hidden.value.trim(),
                        montantPret: inputMontantPret.value.trim(),
                        duree: duree.value.trim()
                    })
                })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        else {
                            console.log("AJAX didn't respond / add pret");
                        }
                    })
                    .then(data => {

                        //response success
                        modalB.hide();

                        if (codePret.value.trim() != ""
                            || num_compte.value.trim() != ""
                            || situation != "tout"
                            || dateDu.value != ""
                            || dateAu.value != ""
                        ) {
                            searchPret();
                            searchClient();
                        }
                        else {
                            listPretAll();
                            listClientAll();
                        }

                        console.log(data);
                    })
                    .catch(error => {
                        console.error("Erreur ajax / add pret");
                    });
            }
        });

    }


    //function create tbody pret element
    function createPretElement(data) {
        //reset tbody
        tbodyPret.innerHTML = "";

        data.forEach(item => {

            //tr
            const tr = document.createElement("tr");

            //td codePret
            const tdCodePret = document.createElement("td");
            tdCodePret.textContent = item.codePret;

            //td montantPret
            const tdMontantPret = document.createElement("td");
            tdMontantPret.textContent = item.montantPret + " Ar";

            //td num_compte
            const tdnum_compte = document.createElement("td");
            tdnum_compte.textContent = item.num_compte;

            //td datePret
            const tddatePret = document.createElement("td");
            let dateHeure = item.datePret.split(" ");
            let date = dateHeure[0].split("-");
            tddatePret.textContent = `${date[2]}/${date[1]}/${date[0]} ${dateHeure[1]}`;

            //td situation
            const tdsituation = document.createElement("td");
            tdsituation.textContent = item.situation;
            if (item.situation === "non remboursé") {
                tdsituation.classList.add("text-secondary");
            }
            else if (item.situation === "tout payé") {
                tdsituation.classList.add("text-success");
            }
            else {
                tdsituation.classList.add("text-primary");
            }

            //td benefice banque
            const tdbeneficeBanque = document.createElement("td");
            tdbeneficeBanque.textContent = item.benefice_banque + " Ar";

            //td duree
            const tdduree = document.createElement("td");
            tdduree.textContent = item.duree + " jours";

            //td actions
            const tdActions = document.createElement("td");
            tdActions.classList.add("d-flex", "gap-2", "mt-2");
            //#btn notify pret
            const btnNotifyPret = document.createElement("button");
            btnNotifyPret.type = "button";
            btnNotifyPret.classList.add("btn", "btn-sm", "btn-light", "btn-notify");
            const ibtnNotifyPret = document.createElement("i");
            ibtnNotifyPret.classList.add("fas", "fa-bell", "text-primary");
            btnNotifyPret.appendChild(ibtnNotifyPret);
            //#btn delete pret
            const btnDeletePret = document.createElement("button");
            btnDeletePret.type = "button";
            btnDeletePret.classList.add("btn", "btn-sm", "btn-light", "btn-delete");
            const ibtnDeletePret = document.createElement("i");
            ibtnDeletePret.classList.add("fas", "fa-trash-alt", "text-danger");
            btnDeletePret.appendChild(ibtnDeletePret);

            tdActions.appendChild(btnNotifyPret);
            tdActions.appendChild(btnDeletePret);


            tr.appendChild(tdCodePret);
            tr.appendChild(tdMontantPret);
            tr.appendChild(tdnum_compte);
            tr.appendChild(tddatePret);
            tr.appendChild(tdsituation);
            tr.appendChild(tdbeneficeBanque);
            tr.appendChild(tdduree);
            tr.appendChild(tdActions);
            tbodyPret.appendChild(tr);
        });

        //btn delete clicked
        const btnDeletePrets = document.querySelectorAll(".btn-delete");
        btnDeletePrets.forEach(btn => {
            btn.addEventListener("click", () => {
                hiddenCP.value = btn.closest("tr").querySelector("td:first-child").textContent;
                modalDPB.show();
            });
        });


        //btn notify pret
        const btnNotifyPrets = document.querySelectorAll(".btn-notify");
        btnNotifyPrets.forEach(btn => {
            btn.addEventListener("click", () => {

                //AJAX sendmail pret
                fetch("../../../public/index.php?route=pret/notify_pret/controller", {
                    method: 'POST',
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        codePret: btn.closest("tr").querySelector("td:first-child").textContent,
                        montantPret: btn.closest("tr").querySelector("td:nth-child(2)").textContent.split(" ")[0],
                        num_compte: btn.closest("tr").querySelector("td:nth-child(3)").textContent,
                        datePret: btn.closest("tr").querySelector("td:nth-child(4)").textContent,
                        benefice_banque: btn.closest("tr").querySelector("td:nth-child(6)").textContent.split(" ")[0],
                        duree: btn.closest("tr").querySelector("td:nth-child(7)").textContent.split(" ")[0]
                    })
                })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        else {
                            console.log("AJAX didn't respond notify pret");
                        }
                    })
                    .then(data => {

                        //response success
                        if (data === "success") {
                            alert("Email envoyé !");
                        }
                        else if (data === "internet error") {
                            window.location.href = "../../../public/index.php?route=internet_error";
                        }
                        else {
                            alert("Erreur d'envoie d'email : " + data);
                        }
                    })
                    .catch(error => {
                        console.error("Erreur catch notify pret : " + error);
                    });
            });
        });
    }


    //btn save pret clicked
    const btnSaveDeletePret = document.getElementById("btn-save-delete-pret");
    btnSaveDeletePret.addEventListener("click", () => {

        //AJAX delete prets
        fetch("../../../public/index.php?route=pret/delete_pret/controller", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ codePret: hiddenCP.value })
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX didn't respond delete pret");
                }
            })
            .then(data => {

                //response delete success
                if (data === "success") {
                    modalDPB.hide();
                    if (codePret.value.trim() != ""
                        || num_compte.value.trim() != ""
                        || situation != "tout"
                        || dateDu.value != ""
                        || dateAu.value != ""
                    ) {
                        searchPret();
                    }
                    else {
                        listPretAll();
                    }
                }
                //response !success
                else {
                    console.log(data);
                }
            })
            .catch(error => {
                console.error("Catch Erreur ajax delete pret  :" + error);
            });
    });


    //----------------SEARCH ---------------
    //inputSearch client
    inputSearchClient.addEventListener("keyup", () => {

        //list-client-all
        if (inputSearchClient.value.trim() === "") {
            listClientAll();
        }
        //search-client
        else {
            searchClient();
        }
    });

    //--function search client
    function searchClient() {
        //AJAX search client
        fetch("../../../public/index.php?route=depot/search_client/controller", {
            method: 'POST',
            headers: { "content-Type": "application/json" },
            body: JSON.stringify({ search: inputSearchClient.value.trim() })
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX no response / depot search client");
                }
            })
            .then(data => {
                tbodyClient.innerHTML = "";

                //response not found
                if (data.message === "not found") {
                    //tr
                    const tr = document.createElement("tr");
                    //td
                    const td = document.createElement("td");
                    td.colSpan = "4";

                    //alert not found
                    const alertDCNF = document.querySelector(".alert-depot-client-not-found");
                    if (!alertDCNF) {
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning",
                            "alert-depot-client-not-found"
                        );
                        const iAlert = document.createElement("i");
                        iAlert.classList.add("fas", "fa-info-circle", "me-2");
                        const txtNode = document.createTextNode("Aucun client n'est trouvé !");

                        alert.appendChild(iAlert);
                        alert.appendChild(txtNode);
                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClient.appendChild(tr);
                    }
                }
                //response found
                else if (data.message === "found") {
                    createTbodyClient(data.list);
                }
                //response error
                else if (data.message === "error") {
                    //tr
                    const tr = document.createElement("tr");
                    //td
                    const td = document.createElement("td");
                    td.colSpan = "4";

                    //alert not found
                    const alertDCNF = document.querySelector(".alert-depot-search-client-error");
                    if (!alertDCNF) {
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning",
                            "alert-depot-search-client-error"
                        );
                        alert.innerHTML = `
                       <i class='fas fa-warning'></i>
                       ${data.error_message}
                   `;

                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClient.appendChild(tr);
                    }
                }
            })
            .catch(error => {
                console.error("Erreur ajax virement search client : " + error);
            });
    }


    //function search pret
    function searchPret() {

        //AJAX search pret
        fetch("../../../public/index.php?route=pret/search_pret/controller", {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                codePret: codePret.value.trim(),
                num_compte: num_compte.value.trim(),
                situation: situation.value.trim(),
                dateDu: dateDu.value.trim(),
                dateAu: dateAu.value.trim(),
            })
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX didn't respond / search pret");
                }
            })
            .then(data => {
                tbodyPret.innerHTML = "";

                //resonse not found
                if (data === "not found") {
                    //tr
                    const tr = document.createElement("tr");
                    //td
                    const td = document.createElement("td");
                    td.colSpan = "8";

                    //alert not found
                    const alertDCNF = document.querySelector(".alert-pret-not-found");
                    if (!alertDCNF) {
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning",
                            "alert-pret-not-found"
                        );
                        const iAlert = document.createElement("i");
                        iAlert.classList.add("fas", "fa-info-circle", "me-2");
                        const textNode = document.createTextNode("Aucun prêt n'est trouvé !");

                        alert.appendChild(iAlert);
                        alert.appendChild(textNode);
                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyPret.appendChild(tr);
                    }
                }
                //response found
                else {
                    createPretElement(data);
                }
            })
            .catch(error => {
                console.error("Erreur ajax depot search pret : " + error);
            });
    }


    codePret.addEventListener("keyup", () => {
        searchPret();
    });
    num_compte.addEventListener("keyup", () => {
        searchPret();

    });
    situation.addEventListener("change", () => {
        searchPret();
    });
    dateDu.addEventListener("change", () => {
        searchPret();
    });
    dateAu.addEventListener("change", () => {
        searchPret();
    });

    const totalB = document.getElementById("total-benefice");

    function totalbenefice() {
        fetch("../../../public/index.php?route=total")
        .then(response => {
            if(response.ok){
                return response.json();
            }
        })
        .then(data =>{
            totalB.innerHTML = `Bénéfice total : <b>${data}</b> Ar`;
        })
        .catch(error =>{
            console.error(error);
        });
    }
    totalbenefice();
});