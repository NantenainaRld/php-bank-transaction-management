document.addEventListener("DOMContentLoaded", () => {
    //tbody client
    const tbodyClient = document.getElementById("tbody-client");

    //modal virement
    const modal = document.getElementById("modal-virement");
    const modalB = new bootstrap.Modal(modal);
    //--hidden num_compteE
    const hiddenNumCompteE = document.createElement("input");
    hiddenNumCompteE.type = "hidden";
    //--h6 num_compteE
    const h6NumCompteE = document.getElementById("h6-num-compte-e");

    //input-search-client
    const inputSearchClient = document.getElementById("input-search-client-virement");


    // //#input recipient
    // const inputRecipient = document.getElementById("input-recipient");
    // //#form text recipient
    // const recipientNomPrenoms = document.getElementById("form-text-recipient");
    // ///#input montantVirement
    // const inputMontantVirement = document.getElementById("input-montant-virement");
    // //#btn save virement
    // const btnSaveVirement = document.getElementById("btn-save-virement");
    // //hiden num_compteE
    // const hidden = document.createElement("input");
    // hidden.type = "hidden";
    // //input search client
    // const inputSearchClientVirement = document.getElementById("input-search-client-virement");

    //tbody virement
    const tbodyVirement = document.getElementById("tbody-virement");


    //input code virement
    const inputCodeVirement = document.getElementById("input-code-virement");
    //input num_CompteE
    const inputNum_compteE = document.getElementById("input-num-compteE");
    //input num_compteB
    const inputNum_compteB = document.getElementById("input-num-compteB");
    //date du
    const dateDu = document.getElementById("date-du");
    //date au
    const dateAu = document.getElementById("date-au");

    //----------------------------LIST ALL-------------------------
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
                console.error("Erreur ajax virement / list client all : " + error);
            });
    }
    listClientAll();

    //----------------------------CREATE TBODY---------------------
    //function create-tbody-client
    function createTbodyClient(data) {
        //reset tbody
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
            const btnDeposite = document.createElement("button");
            btnDeposite.classList.add("btn", "btn-sm", "btn-light"
                , "d-flex", "text-primary", "justify-content-center", "btn-virement");
            const iBtnDeposite = document.createElement("i");
            iBtnDeposite.classList.add("fas", "fa-exchange-alt", "me-2", "mt-1");
            const txtNodeBtnDeposite = document.createTextNode("transféfer");

            btnDeposite.appendChild(iBtnDeposite);
            btnDeposite.appendChild(txtNodeBtnDeposite);
            tdAction.appendChild(btnDeposite);

            tr.appendChild(tdNumCompte);
            tr.appendChild(tdNomPrenoms);
            tr.appendChild(tdSolde);
            tr.appendChild(tdAction);
            tbodyClient.appendChild(tr);
        });

        //----------------------BTN VIREMENT CLICKED---------------
        const btnVirement = document.querySelectorAll(".btn-virement");
        btnVirement.forEach(btn => {
            btn.addEventListener("click", () => {
                //hidden num_compteE
                hiddenNumCompteE.value = btn.closest("tr").querySelector("td:first-child").textContent.trim();

                //h6 num_compteE
                h6NumCompteE.innerHTML = `Envoyeur : <b>${hiddenNumCompteE.value}</b>`;

                //show modal virement
                modalB.show();
            });
        });

    }

    //------------------------------BTN SQVE VIREMENT CLICKED------------------
    const btnSaveVirement = document.getElementById('btn-save-virement');
    //-------------------------------input recipient
    const inputRecipient = document.getElementById("input-recipient");
    const pRecipientInfo = document.getElementById("p-recipient-info");
    inputRecipient.addEventListener("change", () => {

        //AJAX recipient info
        fetch("../../../public/index.php?route=virement/recipient_info/controller",
            {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    num_compteE: hiddenNumCompteE.value,
                    num_compteB: inputRecipient.value.trim()
                })
            }
        )
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX no response virement / recipeint info");
                }
            })
            .then(data => {
                //response found
                if (data.message === "found") {
                    if (pRecipientInfo.classList.contains("text-danger")) {
                        pRecipientInfo.classList.replace("text-danger", "text-secondary");
                    }
                    pRecipientInfo.innerHTML = `à <b>${data.nomPrenoms}</b>`;
                }
                //response not found
                else if (data.message === "not found") {
                    if (pRecipientInfo.classList.contains("text-secondary")) {
                        pRecipientInfo.classList.replace("text-secondary", "text-danger");
                    }
                    pRecipientInfo.innerHTML = `Destinataire introuvable`;
                }
                //response error
                else if (data.message === "error") {
                    if (pRecipientInfo.classList.contains("text-secondary")) {
                        pRecipientInfo.classList.replace("text-secondary", "text-danger");
                    }
                    pRecipientInfo.innerHTML = `${data.error_message}`;
                }
            })
            .catch(error => {
                console.error("Erreur ajax virement / recipient info : " + error);
            });
    });
    //-------------------------------input montantVirement
    const inputMontantVirement = document.getElementById("input-montant-virement");
    const pMontantVirementInvalid = document.getElementById("p-montant-virement-invalid");
    inputMontantVirement.addEventListener("change", () => {
        if (inputMontantVirement.value < 50) {
            pMontantVirementInvalid.classList.replace("d-none", "d-block");
        }
        else {
            pMontantVirementInvalid.classList.replace("d-block", "d-none");
        }
    });
    btnSaveVirement.addEventListener("click", () => {

        //AJAX add virement
        fetch("../../../public/index.php?route=virement/add_virement/controller", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                num_compteE: hiddenNumCompteE.value.trim(),
                num_compteB: inputRecipient.value.trim(),
                montantVirement: inputMontantVirement.value.trim()
            })
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX no response virement / add virement");
                }
            })
            .then(data => {
                //response input empty
                if (data.message === "input empty") {
                    //alert input empty
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning", "alert-dismissible");
                    alert.setAttribute("role", "alert");
                    alert.innerHTML = `
                        <i class='fas fa-info-circle me-2'></i>
                        Les <b>deux champs</b> sont obligatoires .
                        <button class='btn-close' data-bs-dismiss='alert'></button>
                    `;

                    //insert alert
                    const divFirst = document.getElementById("div-first");
                    divFirst.parentNode.insertBefore(alert, divFirst);
                }
                //response num_compteB !exist
                else if (data.message === "recipient not found") {
                    //alert input empty
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning", "alert-dismissible");
                    alert.setAttribute("role", "alert");
                    alert.innerHTML = `
                         <i class='fas fa-info-circle me-2'></i>
                         ${data.message_value}
                         <button class='btn-close' data-bs-dismiss='alert'></button>
                     `;

                    //insert alert
                    const divFirst = document.getElementById("div-first");
                    divFirst.parentNode.insertBefore(alert, divFirst);
                }
                //response error
                else if (data.message === "error") {
                    //alert input empty
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning", "alert-dismissible");
                    alert.setAttribute("role", "alert");
                    alert.innerHTML = `
                          <i class='fas fa-warning me-2'></i>
                          ${data.error_message}
                          <button class='btn-close' data-bs-dismiss='alert'></button>
                      `;

                    //insert alert
                    const divFirst = document.getElementById("div-first");
                    divFirst.parentNode.insertBefore(alert, divFirst);
                }
                //response montant invalid
                else if (data.message === "montant invalid") {
                    //alert input empty
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning", "alert-dismissible");
                    alert.setAttribute("role", "alert");
                    alert.innerHTML = `
                           <i class='fas fa-info-circle me-2'></i>
                           ${data.message_value}
                           <button class='btn-close' data-bs-dismiss='alert'></button>
                       `;

                    //insert alert
                    const divFirst = document.getElementById("div-first");
                    divFirst.parentNode.insertBefore(alert, divFirst);
                }
                //solde !sufficient
                else if (data.message === "solde !sufficient") {
                    //alert input empty
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning", "alert-dismissible");
                    alert.setAttribute("role", "alert");
                    alert.innerHTML = `
                           <i class='fas fa-info-circle me-2'></i>
                           ${data.message_value}
                           <button class='btn-close' data-bs-dismiss='alert'></button>
                       `;

                    //insert alert
                    const divFirst = document.getElementById("div-first");
                    divFirst.parentNode.insertBefore(alert, divFirst);
                }
                //error add
                else if (data.message === "error add") {
                    //alert input empty
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning", "alert-dismissible", "my-2");
                    alert.setAttribute("role", "alert");
                    alert.innerHTML = `
                           <i class='fas fa-warning me-2'></i>
                           ${data.error_message}
                           <button class='btn-close' data-bs-dismiss='alert'></button>
                       `;

                    //insert alert
                    const divFirst = document.getElementById("div-first");
                    divFirst.parentNode.insertBefore(alert, divFirst);
                }
                //success
                else if (data.message === "success") {
                    //hide modal
                    modalB.hide();

                    //alert input empty
                    const alert = document.createElement("div");
                    //update solde success
                    if (data.update_solde === "success") {

                        alert.classList.add("alert", "alert-success", "alert-dismissible", "my-2");
                        alert.setAttribute("role", "alert");
                        alert.innerHTML = `
                           <i class='fas fa-check-circle me-2'></i>
                           ${data.message_value}
                           <button class='btn-close' data-bs-dismiss='alert'></button>
                       `;
                    }
                    //update solde error
                    else {
                        alert.classList.add("alert", "alert-warning", "alert-dismissible", "my-2");
                        alert.setAttribute("role", "alert");
                        alert.innerHTML = `
                           <i class='fas fa-warning me-2'></i>
                           ${data.update_solde}
                           <button class='btn-close' data-bs-dismiss='alert'></button>
                       `;
                    }

                    //insert alert
                    const tableVirement = document.getElementById("table-virement");
                    tableVirement.parentNode.insertBefore(alert, tableVirement);

                    //refresh listClientAll
                    if (inputSearchClient.value === "") {
                        listClientAll();
                    }
                    //refresh search client
                    else {
                        searchClient();
                    }
                    //refresh listirement All
                    listVirementAll();
                }
            })
            .catch(error => {
                console.error("Errreur ajax virement / add virement : " + error);
            });
    });


    //++++++++++++++++LIST VIREMENT

    //function list virement all
    function listVirementAll() {
        //AJAX list virement
        fetch("../../../public/index.php?route=virement/list_virement_all/controller")
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX didn't respond list virement all");
                }
            })
            .then(data => {

                //reset tbody virement
                tbodyVirement.innerHTML = "";

                //response list empty
                if (data === "list empty") {

                    //alert empty
                    const alertVE = document.querySelector(".alert-virement-empty");
                    if (!alertVE) {
                        //tr
                        const tr = document.createElement("tr");
                        //td
                        const td = document.createElement("td");
                        td.colSpan = "6";

                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-info");
                        const iAlert = document.createElement("i");
                        iAlert.classList.add("fas", "fa-info-circle", "me-2");
                        const textNode = document.createTextNode("Aucun virement pour le moment !");

                        alert.appendChild(iAlert);
                        alert.appendChild(textNode);
                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyVirement.appendChild(tr);
                    }
                }
                //list !empty
                else {
                    createVirementElement(data);
                }
            })
            .catch(error => {
                console.error("Erreur ajax / list virement all : " + error);
            })

    }
    listVirementAll()

    //function create tbody virement
    function createVirementElement(data) {

        //reste tbody
        tbodyVirement.innerHTML = "";

        data.forEach(item => {

            //tr
            const tr = document.createElement("tr");

            //td codeVirement
            const tdCodeVirement = document.createElement("td");
            tdCodeVirement.textContent = item.codeVirement;

            //td envoyeur
            const tdNum_compteE = document.createElement("td");
            tdNum_compteE.textContent = item.num_compteE;

            //td recipient
            const tdNum_compteB = document.createElement("td");
            tdNum_compteB.textContent = item.num_compteB;

            //td montantVirement
            const tdMontantVirement = document.createElement("td");
            tdMontantVirement.textContent = item.montantVirement + " Ar";

            //td dateVirement
            let dateHeure = item.dateVirement.split(" ");
            date = dateHeure[0].split("-");
            const tdDateVirement = document.createElement("td");
            tdDateVirement.textContent = `${date[2]}/${date[1]}/${date[0]} ${dateHeure[1]}`;

            //td actions
            const tdActions = document.createElement("td");
            tdActions.classList.add("d-flex", "pt-4");
            //#btn print virement
            const btnPrint = document.createElement("buttton");
            btnPrint.classList.add("btn", "btn-sm", "btn-light",
                "text-primary", "btn-print", "me-2");
            const iBtnPrint = document.createElement("i");
            iBtnPrint.classList.add("fas", "fa-print");
            btnPrint.appendChild(iBtnPrint);

            tdActions.appendChild(btnPrint);
            //#btn delete virement
            const btnDelete = document.createElement("buttton");
            btnDelete.classList.add("btn", "btn-sm", "btn-light",
                "text-danger", "btn-delete");
            const iBtnDelete = document.createElement("i");
            iBtnDelete.classList.add("fas", "fa-trash-alt");
            btnDelete.appendChild(iBtnDelete);

            tdActions.appendChild(btnPrint);
            tdActions.appendChild(btnDelete);

            tr.appendChild(tdCodeVirement);
            tr.appendChild(tdNum_compteE);
            tr.appendChild(tdNum_compteB);
            tr.appendChild(tdMontantVirement);
            tr.appendChild(tdDateVirement);
            tr.appendChild(tdActions);
            tbodyVirement.appendChild(tr)
        });



        //--------------CLICK TBODY VIREMENT-------------

        //btn print clicked
        const btnPrints = document.querySelectorAll(".btn-print");
        btnPrints.forEach(btn => {
            btn.addEventListener("click", () => {

                //href print virement
                window.location.href = "../../../public/index.php?route=virement/print_virement/controller&code_virement=" + btn.closest("tr").querySelector("td:first-child").textContent.trim();
            });
        });

        //btn delete clicked
        const btnDeletes = document.querySelectorAll(".btn-delete");
        btnDeletes.forEach(btn => {
            btn.addEventListener("click", () => {

                //AJAX delete virement
                fetch("../../../public/index.php?route=virement/delete_virement/controller",
                    {
                        method: "DELETE",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ codeVirement: btn.closest("tr").querySelector("td:first-child").textContent.trim() })
                    }
                )
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        else {
                            console.log("AJAX didn't respond / delete depot");
                        }
                    })
                    .then(data => {

                        //response success
                        if (data === "success") {
                            if (inputCodeVirement.value.trim() === ""
                                || inputNum_compteE.value.trim() === ""
                                || inputNum_compteB.value.trim() === ""
                                || dateDu.value.trim() === ""
                                || dateAu.value.trim() === "") {
                                listVirementAll();
                            }
                            else {
                                searchVirement();
                            }
                        }
                        else {
                            console.log(data);
                        }
                    })
                    .catch(error => {
                        console.error("Erreur ajax / delete virement");
                    })
            });
        });
    }


    //-----------------------------SEARCH-----------------------------

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


    //search virement

    //btn reset clicked
    const btnReset = document.getElementById("btn-reset");
    btnReset.addEventListener("click", () => {
        window.location.reload();
    });

    //input code virement keyup
    inputCodeVirement.addEventListener("keyup", () => {
        searchVirement();
    });
    //input num_compteE keyup
    inputNum_compteE.addEventListener("keyup", () => {
        searchVirement();
    });
    //input num_compteB keyup
    inputNum_compteB.addEventListener("keyup", () => {
        searchVirement();
    });
    //date du change
    dateDu.addEventListener("change", () => {
        searchVirement();
    });
    //date au change
    dateAu.addEventListener("change", () => {
        searchVirement();
    });



    //function search virement
    function searchVirement() {

        //AJAX search virement
        fetch("../../../public/index.php?route=virement/search_virement/controller", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                codeVirement: inputCodeVirement.value.trim(),
                num_compteE: inputNum_compteE.value.trim(),
                num_compteB: inputNum_compteB.value.trim(),
                dateDu: dateDu.value.trim(),
                dateAu: dateAu.value.trim()
            })
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX didn't respond / search virement");
                }
            })
            .then(data => {
                //reset tbody virement
                tbodyVirement.innerHTML = "";

                //response not found
                if (data === "not found") {

                    //alert VNF
                    const alertVNF = document.querySelector(".alert-virement-not-found");
                    if (!alertVNF) {
                        //tr
                        const tr = document.createElement("tr");
                        //td
                        const td = document.createElement("td");
                        td.colSpan = "6";

                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning", "alert-virement-not-found");
                        const iAlert = document.createElement("i");
                        iAlert.classList.add("fas", "fa-info-circle", "me-2");
                        const texteNode = document.createTextNode("Aucun virement n'est trouvé !");

                        alert.appendChild(iAlert);
                        alert.appendChild(texteNode);
                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyVirement.appendChild(tr);
                    }
                }
                //respons found
                else {
                    createVirementElement(data);
                }
            })
            .catch(error => {
                console.error("Erreur ajax / search virement : " + error);
            });
    }
});