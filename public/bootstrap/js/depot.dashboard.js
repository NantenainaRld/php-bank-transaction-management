document.addEventListener("DOMContentLoaded", () => {
    //tbody client depot
    const tbodyClientDepot = document.getElementById("tbody-client-depot");

    //modal deposite
    const modalDeposite = document.getElementById("modal-deposite");
    const modalDepositeB = new bootstrap.Modal(modalDeposite);
    //--h6 num_compte
    const h6NumCompteDeposite = document.getElementById('h6-numCompte-deposite');

    //hidden num_compte
    const hiddenNumCompte = document.createElement("input");
    hiddenNumCompte.type = "hidden";

    //tbody depot
    const tbodyDepot = document.getElementById("tbody-depot");

    //modal update depot
    const modalUpdateDepot = document.getElementById("modal-update-depot");
    const modalUpdateDepotB = new bootstrap.Modal(modalUpdateDepot);
    //--h6-code-depot
    const h6CodeDepot = document.getElementById("h6-code-depot");
    //--montant-depot-update
    const inputMontantDepotUpdate = document.getElementById('input-montant-depot-update');
    //--date-depot-update
    const inputDateDepotUpdate = document.getElementById('input-date-depot-update');

    //hidden codeDepot
    const hiddenCodeDepot = document.createElement("input");
    hiddenCodeDepot.type = "hidden";

    //input-code-depot-search
    const inputCodeDepotSearch = document.getElementById("input-code-depot-search");
    //input-depot-num-compte-search
    const searchNumCompteDepot = document.getElementById("input-depot-num-compte-search");
    //input-date-du
    const dateDu = document.getElementById("date-du");
    //input-date-au
    const dateAu = document.getElementById("date-au");


    //----------------------LIST ALL---------------------
    //function list-client-all
    function listClientAll() {

        //AJAX list client all
        fetch("../../../public/index.php?route=depot/list_client_all/controller")
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX no response / depot list client all");
                }
            })
            .then(data => {
                //reset tbody-client-depot
                tbodyClientDepot.innerHTML = "";

                //response list empty
                if (data.message === "list empty") {

                    //tr
                    const tr = document.createElement("tr");

                    //td
                    const td = document.createElement("td");
                    td.colSpan = "4";

                    //alert depot-list-client-empty
                    const alertDLCE = document.querySelector(".alert-depot-list-client-empty");
                    if (!alertDLCE) {
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-info",
                            "alert-depot-list-client-empty");
                        const iAlert = document.createElement("i");
                        iAlert.classList.add("fas", "fa-info-circle", "me-2");
                        const textNode = document.createTextNode("Liste des clients est vide pour le moment .");

                        alert.appendChild(iAlert);
                        alert.appendChild(textNode);
                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClientDepot.appendChild(tr);
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
                    const alertDELC = document.querySelector(".alert-depot-error-list-client");
                    if (!alertDELC) {
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning",
                            "alert-depot-error-list-client");
                        alert.innerHTML = `
                            <i class='fas fa-warning me-2'></i>
                            ${data.error_message}
                        `;

                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyClientDepot.appendChild(tr);
                    }
                }

            })
            .catch(error => {
                console.error("Erreur ajax / depot list client all : " + error);
            });
    }
    listClientAll();

    //function list-depot-all
    function listDepotAll() {
        //AJAX list depot all
        fetch("../../../public/index.php?route=depot/list_depot_all/controller")
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("JAX no resposne / listd depot all");
                }
            })
            .then(data => {
                //reset tbody
                tbodyDepot.innerHTML = "";

                //response list empty
                if (data.message === "list empty") {
                    //tr
                    const tr = document.createElement("tr");

                    //td
                    const td = document.createElement("td");
                    td.colSpan = "5";

                    //alert list empty
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-info");
                    alert.innerHTML = `
                        <i class='fas fa-info-circle me-2'></i>
                        Liste des dépôts est vide pour le moment .
                    `;
                    td.appendChild(alert);
                    tr.appendChild(td);
                    tbodyDepot.appendChild(tr);
                }
                //response list not empty
                else if (data.message === "list not empty") {
                    createTbodyDepot(data.list);
                }
                //error
                else if (data.message === "error") {
                    //tr
                    const tr = document.createElement("tr");

                    //td
                    const td = document.createElement("td");
                    td.colSpan = "5";

                    //alert list empty
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-warning");
                    alert.innerHTML = `
                        <i class='fas fa-warning me-2'></i>
                        ${data.error_message}
                    `;
                    td.appendChild(alert);
                    tr.appendChild(td);
                    tbodyDepot.appendChild(tr);
                }
            })
            .catch(error => {
                console.error("Erreur ajax / list depot all : " + error);
            });
    }
    listDepotAll();


    //----------------------CREATE TBODY---------------------------
    //function create-tbody-client
    function createTbodyClient(data) {
        //reset tbody
        tbodyClientDepot.innerHTML = "";

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
                , "d-flex", "text-primary", "justify-content-center", "btn-deposite");
            const iBtnDeposite = document.createElement("i");
            iBtnDeposite.classList.add("fas", "fa-arrow-down", "me-2", "mt-1");
            const txtNodeBtnDeposite = document.createTextNode("déposer");

            btnDeposite.appendChild(iBtnDeposite);
            btnDeposite.appendChild(txtNodeBtnDeposite);
            tdAction.appendChild(btnDeposite);

            tr.appendChild(tdNumCompte);
            tr.appendChild(tdNomPrenoms);
            tr.appendChild(tdSolde);
            tr.appendChild(tdAction);
            tbodyClientDepot.appendChild(tr);
        });

        //--------------------BTN DEPOSITE CLICKED----------
        const btnDeposite = document.querySelectorAll(".btn-deposite");
        btnDeposite.forEach(btn => {
            btn.addEventListener("click", () => {

                //hidden-num_compte
                hiddenNumCompte.value = btn.closest("tr").querySelector("td:first-child").textContent.trim();

                //h6 value
                h6NumCompteDeposite.innerHTML = `
                 Numéro : <b>${hiddenNumCompte.value}</b>
                `;

                modalDepositeB.show();
            });
        });
    }

    //function create depot element
    function createTbodyDepot(data) {

        //reste tbody
        tbodyDepot.innerHTML = '';

        data.forEach(item => {

            //tr
            const tr = document.createElement("tr");

            //td codeDepot
            const tdCodeDepot = document.createElement("td");
            tdCodeDepot.textContent = item.codeDepot;

            //td montantDepot
            const tdMontantDepot = document.createElement("td");
            tdMontantDepot.textContent = item.montantDepot + " Ar";

            //td num_compte
            const tdNum_compte = document.createElement("td");
            tdNum_compte.textContent = item.num_compte;

            //td dateDepot
            let dateHeure = item.dateDepot.split(" ");
            let date = dateHeure[0];
            date = date.split("-");
            const tdDateDepot = document.createElement("td");
            tdDateDepot.textContent = `${date[2]}/${date[1]}/${date[0]} ${dateHeure[1]}`;

            //td actions
            const tdActions = document.createElement("td");
            tdActions.classList.add("text-center", "gap-2", "d-flex", "pt-4")
            //--btn-delete-depot
            const btnDeleteDepot = document.createElement("button");
            btnDeleteDepot.classList.add("btn", "btn-sm",
                "btn-light", "text-danger", "btn-delete-depot");
            const iBtnDeleteDepot = document.createElement("i");
            iBtnDeleteDepot.classList.add("fas", "fa-trash-alt");
            btnDeleteDepot.appendChild(iBtnDeleteDepot);
            //--btn-update-depot
            const btnUpdateDepot = document.createElement("button");
            btnUpdateDepot.classList.add("btn", "btn-sm",
                "btn-light", "text-primary", "btn-update-depot");
            const iBtnUpdateDepot = document.createElement("i");
            iBtnUpdateDepot.classList.add("fas", "fa-edit");
            btnUpdateDepot.appendChild(iBtnUpdateDepot);

            tdActions.appendChild(btnUpdateDepot);
            tdActions.appendChild(btnDeleteDepot);

            tr.appendChild(tdCodeDepot);
            tr.appendChild(tdMontantDepot);
            tr.appendChild(tdNum_compte);
            tr.appendChild(tdDateDepot);
            tr.appendChild(tdActions);
            tbodyDepot.appendChild(tr);
        });

        //-----------------------BTN UPDATE DEPOT CLICKED----------------
        const btnUpdateDepot = document.querySelectorAll(".btn-update-depot");
        btnUpdateDepot.forEach(btn => {
            btn.addEventListener("click", () => {
                //tr
                const tr = btn.closest("tr");

                //hidden-code-depot
                hiddenCodeDepot.value = btn.closest("tr").querySelector("td:first-child").textContent.trim();

                //h6 code depot value 
                h6CodeDepot.innerHTML = `Dépôt numéro : <b>${hiddenCodeDepot.value}</b>`;

                //montant depot update value
                inputMontantDepotUpdate.value =
                    parseFloat(tr.querySelector("td:nth-child(2)").textContent.trim().split(" ")[0]);

                //dateDepot update value
                const dateString = tr.querySelector("td:nth-child(4)").textContent.trim().split(" ")[0].split("/");
                const newDate = `${dateString[2]}-${dateString[1]}-${dateString[0]}`;
                inputDateDepotUpdate.value = newDate;

                //show modal-update-depot
                modalUpdateDepotB.show();
            });
        });

        //-----------------------BTN DELETE DEPOT CLICKED----------------
        const btnDeleteDepot = document.querySelectorAll(".btn-delete-depot");
        btnDeleteDepot.forEach(btn => {
            btn.addEventListener("click", () => {

                //JAX delete depot
                fetch("../../../public/index.php?route=depot/delete_depot/controller", {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ codeDepot: btn.closest("tr").querySelector("td:first-child").textContent.trim() })
                })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        else {
                            console.log("AJAx no response / delete depot");
                        }
                    })
                    .then(data => {
                        //response error
                        if (data.message === "error") {
                            //alert delete-depot-error
                            const alert = document.createElement('div');
                            alert.classList.add("alert", "alert-warning", "fade",
                                "show", "alert-dismissible", "my-2");
                            alert.setAttribute("role", "alert");
                            alert.innerHTML = `
                                <i class='fas fa-warning me-2'></i>
                                ${data.error_message}
                                <button type='button' class='btn btn-close'
                                    data-bs-dismiss='alert'></button>
                            `;
                            //insert alert
                            const tabelDepot = document.querySelector("#table-depot");
                            tabelDepot.parentNode.insertBefore(alert, tabelDepot);
                        }
                        //response success
                        else if (data.message === "success") {
                            //alert add-delete-depot-success
                            const alert = document.createElement('div');
                            alert.classList.add("alert", "alert-success", "fade",
                                "show", "alert-dismissible", "my-2");
                            alert.setAttribute("role", "alert");
                            alert.innerHTML = `
                                <i class='fas fa-check-circle me-2'></i>
                                Dépôt numéro <b>${btn.closest("tr").querySelector("td:first-child").textContent.trim()}</b>
                                <button type='button' class='btn btn-close'
                                    data-bs-dismiss='alert'></button>
                            `;
                            //insert alert
                            const tabelDepot = document.querySelector("#table-depot");
                            tabelDepot.parentNode.insertBefore(alert, tabelDepot);

                            //refresh listDepotAll
                            if (inputCodeDepotSearch.value.trim() === "" &&
                                searchNumCompteDepot.value.toString() === "" &&
                                dateDu.value.toString() === "" &&
                                dateAu.value.toString() === ""
                            ) {
                                listDepotAll();
                            }
                            //refresh search depot
                            else {
                                searchDepot();
                            }
                        }
                    })
                    .catch(error => {
                        console.error("Erreur ajax depot / delete depot : " + error);
                    });
            });
        });
    }

    //----------------------BTN SAVE DEPOSITE----------------------
    const btnSaveDepot = document.getElementById('btn-save-depot')
    //------------------------input-montant-depot
    const inputMontantDepot = document.getElementById('input-montant-depot');
    inputMontantDepot.addEventListener('change', () => {
        if (inputMontantDepot.value < 50) {
            inputMontantDepot.value = 50;
        }
    });
    const pMontantInvalid = document.getElementById('p-montant-invalid');
    btnSaveDepot.addEventListener("click", () => {

        //amount < 50
        if (inputMontantDepot.value < 50) {
            pMontantInvalid.classList.replace("d-none", "d-block");
        }
        //amount > 50
        else {
            pMontantInvalid.classList.replace("d-block", "d-none");

            //AJAX add depot
            fetch("../../../public/index.php?route=depot/add_depot/controller", {
                method: 'POST',
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    num_compte: hiddenNumCompte.value.trim(),
                    montantDepot: inputMontantDepot.value
                })
            })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    else {
                        console.log("AJAX no response / add depot ");
                    }
                })
                .then(data => {
                    //response montant invalid
                    if (data.message === "montant invalid") {
                        //alert montant-invalid
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning", "alert-dismissible");
                        alert.setAttribute("role", "alert");
                        alert.innerHTML = `
                                <i class='fas fa-info-circle me-2'></i>
                                Le montant minimum est : <b>50 Ar</b> .
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                            `;
                        const div = modalDeposite.querySelector(".modal-body #div-first");
                        div.insertBefore(alert, div.querySelector("#label"));
                    }
                    //response success
                    else if (data.message === "success") {
                        //hide modal
                        modalDepositeB.hide();

                        //alert add-depot-success
                        const alert = document.createElement('div');
                        alert.classList.add("alert", "alert-success", "fade",
                            "show", "alert-dismissible", "my-2");
                        alert.setAttribute("role", "alert");
                        alert.innerHTML = `
                                <i class='fas fa-check-circle me-2'></i>
                                Dépôt de <b>${inputMontantDepot.value} Ar</b>
                                effectué pour le compte <b>${hiddenNumCompte.value}</b>
                                <button type='button' class='btn btn-close'
                                    data-bs-dismiss='alert'></button>
                            `;
                        //insert alert
                        const tabelDepot = document.querySelector("#table-depot");
                        tabelDepot.parentNode.insertBefore(alert, tabelDepot);

                        //--refresh listClientAll
                        if (inputSearchClientDepot.value.trim() === "") {
                            listClientAll();
                        }
                        //--refresh searchClient
                        else {
                            searchClient();
                        }
                        //--refresh listDepotAll
                        listDepotAll();
                    }
                    //response error
                    else if (data.message === "error") {
                        //alert error-add-depot
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning", "alert-dismissible");
                        alert.setAttribute("role", "alert");
                        alert.innerHTML = `
                                <i class='fas fa-info-circle me-2'></i>
                                ${data.error_message}
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                            `;
                        const div = modalDeposite.querySelector(".modal-body #div-first");
                        div.insertBefore(alert, div.querySelector("#label"));
                    }
                })
                .catch(error => {
                    console.error("Erreur ajax / add depot : " + error);
                });
        }
    });

    //-----------------------BTN SAVE UPDATE DEPOT-----------------
    const btnSaveUpdateDepot = document.getElementById('btn-save-update-depot');
    //------------------------input-montant-depot-update
    inputMontantDepotUpdate.addEventListener('change', () => {
        if (inputMontantDepotUpdate.value < 50) {
            inputMontantDepotUpdate.value = 50;
        }
    });
    const pMontantInvalidUpdate = document.getElementById('p-montant-invalid-update');
    //------------------------p date depot update empty
    const pDateDepotUpdateEmpty = document.getElementById('p-date-depot-update-empty');

    btnSaveUpdateDepot.addEventListener('click', () => {
        //montant depot update invalid
        if (inputMontantDepotUpdate.value < 50) {
            pMontantInvalidUpdate.classList.replace("d-none", "d-block");
        }
        //montant depot update valid
        else {
            pMontantInvalidUpdate.classList.replace("d-block", "d-none");

            //date depot update empty
            if (inputDateDepotUpdate.value === "") {
                pDateDepotUpdateEmpty.classList.replace("d-none", "d-block");
            }
            //date depot update !empty*
            else {
                pDateDepotUpdateEmpty.classList.replace("d-block", "d-none");

                //AJAX update depot
                fetch("../../../public/index.php?route=depot/update_depot/controller",
                    {
                        method: "PUT",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            codeDepot: hiddenCodeDepot.value,
                            montantDepot: inputMontantDepotUpdate.value,
                            dateDepot: inputDateDepotUpdate.value,
                        })
                    }
                )
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        else {
                            console.log("AJAX no response depot / update depot");
                        }
                    })
                    .then(data => {
                        //response input invalid
                        if (data.message === "input invalid") {
                            //alert input invalid
                            const alert = document.createElement("div");
                            alert.classList.add("alert", "alert-warning",
                                "alert-dismissible", "my-2");
                            alert.setAttribute("role", "alert");
                            alert.innerHTML = `
                                    <i class='fas fa-info-circle me-2'></i>
                                    ${data.message_value}
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                `;

                            //insert alert
                            const divFirst = document.getElementById('div-first-update-depot');
                            divFirst.parentNode.insertBefore(alert, divFirst);
                        }
                        //response error
                        else if (data.message === "error") {
                            //alert error
                            const alert = document.createElement("div");
                            alert.classList.add("alert", "alert-warning",
                                "alert-dismissible", "my-2");
                            alert.setAttribute("role", "alert");
                            alert.innerHTML = `
                                     <i class='fas fa-warning me-2'></i>
                                     ${data.error_message}
                                     <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                 `;

                            //insert alert
                            const divFirst = document.getElementById('div-first-update-depot');
                            divFirst.parentNode.insertBefore(alert, divFirst);
                        }
                        //respons success
                        else if (data.message === "success") {
                            //hide modal
                            modalUpdateDepotB.hide();

                            //alert success
                            const alert = document.createElement("div");
                            alert.classList.add("alert", "alert-success",
                                "alert-dismissible", "my-2");
                            alert.setAttribute("role", "alert");
                            alert.innerHTML = `
                                     <i class='fas fa-check-circle me-2'></i>
                                     Dépôt numéro <b>${hiddenCodeDepot.value}</b> modifié avec succcès .
                                     <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                 `;

                            //insert alert
                            const tabelDepot = document.querySelector("#table-depot");
                            tabelDepot.parentNode.insertBefore(alert, tabelDepot);

                            //refresh listDepotAll
                            if (inputCodeDepotSearch.value.trim() === "" &&
                                searchNumCompteDepot.value.toString() === "" &&
                                dateDu.value.toString() === "" &&
                                dateAu.value.toString() === ""
                            ) {
                                listDepotAll();
                            }
                            //refresh search depot
                            else {
                                searchDepot();
                            }
                        }
                    })
                    .catch(error => {
                        console.error("Erreur ajax depot / update depot : " + error);
                    });
            }
        }
    });

    //------------------------SEARCH-------------------------------
    //input-search-client
    const inputSearchClientDepot = document.getElementById("input-search-client-depot");

    //inputSearch client
    inputSearchClientDepot.addEventListener("keyup", () => {

        //list-client-all
        if (inputSearchClientDepot.value.trim() === "") {
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
            body: JSON.stringify({ search: inputSearchClientDepot.value.trim() })
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
                tbodyClientDepot.innerHTML = "";

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
                        tbodyClientDepot.appendChild(tr);
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
                        tbodyClientDepot.appendChild(tr);
                    }
                }
            })
            .catch(error => {
                console.error("Erreur ajax depot search client : " + error);
            });
    }

    //--function search depot
    function searchDepot() {

        //AJAX search depot
        fetch("../../../public/index.php?route=depot/search_depot/controller", {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                codeDepot: inputCodeDepotSearch.value.trim(),
                num_compte: searchNumCompteDepot.value.trim(),
                dateDu: dateDu.value.trim(),
                dateAu: dateAu.value.trim()
            })
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX no response / search depot");
                }
            })
            .then(data => {
                //reset tbodyDepot
                tbodyDepot.innerHTML = "";

                //response not found
                if (data.message === "not found") {
                    //tr
                    const tr = document.createElement("tr");

                    //td
                    const td = document.createElement("td");
                    td.colSpan = "5";

                    //alert depot not found
                    const alertDNF = document.querySelector(".alert-depot-not-found");
                    if (!alertDNF) {
                        4
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning", "alert-depot-not-found");
                        alert.innerHTML = `
                            <i class='fas fa-info-circle me-2'></i>
                            Aucun résultat trouvé .
                        `;

                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyDepot.appendChild(tr);
                    }
                }
                //response found
                else if (data.message === "found") {
                    createTbodyDepot(data.list);
                }
                //response error
                else if (data.message === "error") {
                    //tr
                    const tr = document.createElement("tr");

                    //td
                    const td = document.createElement("td");
                    td.colSpan = "5";

                    //alert depot not found
                    const alertDNF = document.querySelector(".alert-depot-not-found");
                    if (!alertDNF) {
                        4
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-warning", "alert-depot-not-found");
                        alert.innerHTML = `
                            <i class='fas fa-warning me-2'></i>
                            ${data.error_message}.
                        `;

                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyDepot.appendChild(tr);
                    }
                }
            })
            .catch(error => {
                console.error("Erreur ajax search depot : " + error);
            });
    }

    //----btn-search-depot
    const btnSearchDepot = document.getElementById("btn-search");
    btnSearchDepot.addEventListener("click", () => {
        searchDepot();
    });
    //----search code depot keyup
    inputCodeDepotSearch.addEventListener("keyup", () => {
        searchDepot();
    });
    //----search num_compte keyup
    searchNumCompteDepot.addEventListener("keyup", () => {
        searchDepot();
    });
    //----dateDu change
    dateDu.addEventListener("change", () => {
        searchDepot();
    });
    //dateAu change
    dateAu.addEventListener("change", () => {
        searchDepot();
    });

});