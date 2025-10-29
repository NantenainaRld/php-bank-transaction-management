document.addEventListener("DOMContentLoaded", () => {
    //tbody pret
    const tbodyPret = document.getElementById("tbody-pret");
    //code_pret hidden
    const hiddenCP = document.createElement("input");
    hiddenCP.type = "hidden";
    //modal rendu
    const modal = document.getElementById("modal-rendu");
    const modalB = new bootstrap.Modal(modal);
    //tbody rendu
    const tbodyRendu = document.getElementById("tbody-rendu");
    //modal delete rendu
    const modalDR = document.getElementById("modal-delete-rendu");
    const modalDRB = new bootstrap.Modal(modalDR);
    //codeRendu hidden
    const hiddenCR = document.createElement("input");
    hiddenCR.type = "hidden";


    //-------------------LIST--------------------
    //function list pret all
    function listPretAll() {

        //AJAX list pret all
        fetch("../../../public/index.php?route=rendu/list_pret_all/controller")
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX rendu / list pret all didn't responsd");
                }
            })
            .then(data => {
                //reste tbody pret
                tbodyPret.innerHTML = "";

                //list empty
                if (data === "list pret empty") {
                    //tr
                    const tr = document.createElement("tr");
                    //td
                    const td = document.createElement("td");
                    td.colSpan = "5";

                    //alert LPE
                    const alertLPE = document.querySelector(".alert-list-pret-empty");
                    if (!alertLPE) {

                        //alert
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-info", "alert-list-pret-empty");

                        //i
                        const iAlert = document.createElement("i");
                        iAlert.classList.add("fas", "fa-info-circle", "me-2");

                        //text node
                        const txtNode = document.createTextNode("Aucun prêt pour le moment");

                        //add
                        alert.appendChild(iAlert);
                        alert.appendChild(txtNode);
                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyPret.appendChild(tr);
                    }
                }

                //list !empty
                else {

                    //**create tbody pret elemnt

                    createPretElement(data);
                }
            })
            .catch(error => {
                console.log("Erreur rendu / list pret all : " + error);
            });
    }
    listPretAll();

    //function list rendu all
    function listRenduAll() {

        //AJAX list rendu all
        fetch("../../../public/index.php?route=rendu/list_rendu_all/controller")
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX rendu / list rendu all didn't responsd");
                }
            })
            .then(data => {
                //reste tbody rendu
                tbodyRendu.innerHTML = "";

                //list empty
                if (data === "list rendu empty") {
                    //tr
                    const tr = document.createElement("tr");
                    //td
                    const td = document.createElement("td");
                    td.colSpan = "6";

                    //alert LRE
                    const alertLRE = document.querySelector(".alert-list-rendu-empty");
                    if (!alertLRE) {

                        //alert
                        const alert = document.createElement("div");
                        alert.classList.add("alert", "alert-info", "alert-list-rendu-empty");

                        //i
                        const iAlert = document.createElement("i");
                        iAlert.classList.add("fas", "fa-info-circle", "me-2");

                        //text node
                        const txtNode = document.createTextNode("Aucun rendu pour le moment");

                        //add
                        alert.appendChild(iAlert);
                        alert.appendChild(txtNode);
                        td.appendChild(alert);
                        tr.appendChild(td);
                        tbodyRendu.appendChild(tr);
                    }
                }

                //list !empty
                else {

                    //**create tbody rendu elemnt
                    createRenduElement(data);
                }
            })
            .catch(error => {
                console.log("Erreur rendu / list rendu all : " + error);
            });
    }
    listRenduAll();


    //------------------CREATE ELEMENT----------------

    //function create pret element
    function createPretElement(data) {
        //reset tbody pret
        tbodyPret.innerHTML = "";


        data.forEach(item => {

            //tr
            const tr = document.createElement("tr");

            //td code pret
            const tdCP = document.createElement("td");
            tdCP.textContent = item.codePret;

            //td num_compte
            const tdNC = document.createElement("td");
            tdNC.textContent = item.num_compte;

            //td montantPret
            const tdMP = document.createElement("td");
            tdMP.textContent = item.montantPret + " Ar";

            //td restePaye
            const tdRP = document.createElement("td");
            tdRP.textContent = item.restePaye + " Ar";

            //td action
            const tdAction = document.createElement("td");
            const btnRendu = document.createElement("button");
            btnRendu.type = "button";
            btnRendu.classList.add("btn", "btn-sm", "d-flex",
                "btn-light", "text-primary", "btn-rendu");
            const iAction = document.createElement("i");
            iAction.classList.add("fas", "fa-undo", "me-2", "mt-1");
            const txtNode = document.createTextNode("Rembourser");

            btnRendu.appendChild(iAction);
            btnRendu.appendChild(txtNode);
            tdAction.appendChild(btnRendu);



            //add
            tr.appendChild(tdCP);
            tr.appendChild(tdNC);
            tr.appendChild(tdMP);
            tr.appendChild(tdRP);
            tr.appendChild(tdAction);
            tbodyPret.appendChild(tr);
        });

        //btn rendu clicked
        const btnRendu = document.querySelectorAll(".btn-rendu");
        btnRendu.forEach(btn => {
            btn.addEventListener("click", () => {

                //code pret
                hiddenCP.value = btn.closest("tr").querySelector("td:first-child").textContent.trim();

                //show modal rendu
                //p code pret
                const pCP = document.getElementById("p-code-pret");
                pCP.textContent = hiddenCP.value;
                //max montantRendu
                const montantRendu = document.getElementById("input-montant-rendu");
                montantRendu.max = btn.closest("tr").querySelector("td:nth-child(4)").textContent.split(" ")[0];
                //p min max
                const pMinMax = document.getElementById("p-montant-min-max");
                pMinMax.textContent = "Minimum : 50 Ar  -  Max : " + btn.closest("tr").querySelector("td:nth-child(4)").textContent;
                modalB.show();
            });
        });
    }

    //function create rendu element
    function createRenduElement(data) {
        //reset tbody rendu
        tbodyRendu.innerHTML = "";


        data.forEach(item => {

            //tr
            const tr = document.createElement("tr");

            //td codeRendu
            const tdCR = document.createElement("td");
            tdCR.textContent = item.codeRendu;
            //rendu !last
            if (item.lastRendu != 'not max') {
                tdCR.classList.add("text-primary");
            }

            //td code_pret
            const tdCP = document.createElement("td");
            tdCP.textContent = item.code_pret;
            tdCP.classList.add("text-secondary");

            //td montantRendu
            const tdMR = document.createElement("td");
            tdMR.textContent = item.montantRendu + " Ar";

            //td situation 
            const tdS = document.createElement("td");
            tdS.textContent = item.situation;
            if (item.situation === "tout payé") {
                tdS.classList.add("text-success");
            }
            else {
                tdS.classList.add("text-primary");
            }

            //td restePaye
            const tdRP = document.createElement("td");
            tdRP.textContent = item.restePaye + " Ar";

            //td dateRendu
            const tdDR = document.createElement("td");
            let dateHeure = item.dateRendu.split(" ");
            let date = dateHeure[0].split("-");
            tdDR.textContent = `${date[2]}/${date[1]}/${date[0]} ${dateHeure[1]}`;

            //td actions
            const tdActions = document.createElement("td");
            tdActions.classList.add("d-flex", "gap-2", "mt-2");
            //#btn notify rendu
            const btnNotifyRendu = document.createElement("button");
            btnNotifyRendu.type = "button";
            btnNotifyRendu.classList.add("btn", "btn-sm", "btn-light", "btn-notify");
            const ibtnNotifyRendu = document.createElement("i");
            ibtnNotifyRendu.classList.add("fas", "fa-bell", "text-primary");
            btnNotifyRendu.appendChild(ibtnNotifyRendu);
            //#btn delete rendu
            const btnDeleteRendu = document.createElement("button");
            btnDeleteRendu.type = "button";
            btnDeleteRendu.classList.add("btn", "btn-sm", "btn-light", "btn-delete");
            const ibtnDeleteRendu = document.createElement("i");
            ibtnDeleteRendu.classList.add("fas", "fa-trash-alt", "text-danger");
            btnDeleteRendu.appendChild(ibtnDeleteRendu);

            tdActions.appendChild(btnNotifyRendu);
            tdActions.appendChild(btnDeleteRendu);


            //add
            tr.appendChild(tdCR);
            tr.appendChild(tdCP);
            tr.appendChild(tdMR);
            tr.appendChild(tdS);
            tr.appendChild(tdRP);
            tr.appendChild(tdDR);
            tr.appendChild(tdActions);
            tbodyRendu.appendChild(tr);
        });

        //-------------BTN DELETE RENDU-----------
        const btnDelete = document.querySelectorAll(".btn-delete");
        btnDelete.forEach(btn => {
            btn.addEventListener("click", () => {
                modalDR.querySelector(".modal-body").innerHTML = `<p>Le <b>montant</b> reste à payer d'un <b>prêt</b> dépend de la <b>dernière
                        montant (reste à payer) </b> de son <b>rendu</b>.</p>
                    Voulez-vous vraiment supprimer ce rendu (<b> 
                ${btn.closest('tr').querySelector('td:first-child').textContent}?`;
                hiddenCR.value = btn.closest('tr').querySelector('td:first-child').textContent;
                modalDRB.show();
            });
        });

        //-----------BTN NOTIFY-------------
        const btnNotify = document.querySelectorAll('.btn-notify');

        //btn notify pret
        const btnNotifyRendu = document.querySelectorAll(".btn-notify");
        btnNotifyRendu.forEach(btn => {
            btn.addEventListener("click", () => {

                //AJAX sendmail pret
                fetch("../../../public/index.php?route=rendu/notify_rendu/controller", {
                    method: 'POST',
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        codeRendu: btn.closest("tr").querySelector("td:first-child").textContent.trim(),
                        montantRendu: btn.closest("tr").querySelector("td:nth-child(3)").textContent.split(" ")[0],
                        dateRendu: btn.closest("tr").querySelector("td:nth-child(6)").textContent,
                        restePaye: btn.closest("tr").querySelector("td:nth-child(5)").textContent,
                        situation: btn.closest("tr").querySelector("td:nth-child(4)").textContent
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

    //-----------BTN TOUT PAYE---------------
    const btnToutPaye = document.getElementById("btn-tout-paye");
    btnToutPaye.addEventListener("click", () => {
        if (btnToutPaye.classList.contains("unclicked")) {
            btnToutPaye.classList.replace("btn-light", "btn-success");
            btnToutPaye.classList.replace("text-success", "text-light");
            btnToutPaye.classList.replace("unclicked", "clicked");

            //montantRendu disabled
            const montantRendu = document.getElementById("input-montant-rendu");
            montantRendu.value = montantRendu.max;
            montantRendu.disabled = true;
        }
        else {
            btnToutPaye.classList.replace("btn-success", "btn-light");
            btnToutPaye.classList.replace("text-light", "text-success");
            btnToutPaye.classList.replace("clicked", "unclicked");

            //montantRendu disabled
            const montantRendu = document.getElementById("input-montant-rendu");
            montantRendu.value = montantRendu.max;
            montantRendu.disabled = false;
        }
    });
    //-----------BTN SAVE RENDU--------------
    const btnSaveRendu = document.getElementById("btn-save-rendu");
    btnSaveRendu.addEventListener("click", () => {

        //is montant correct
        const montantRendu = document.getElementById("input-montant-rendu");

        if (!montantRendu.checkValidity()) {

            //alert input invalid
            const alertMI = document.querySelector(".alert-montant-invalid");

            if (!alertMI) {
                //alert
                const alert = document.createElement("div");
                alert.classList.add("alert", "alert-warning"
                    , "alert-dismissible", "alert-montant-invalid");
                const iAlert = document.createElement("i");
                iAlert.classList.add("fas", "fa-info-circle", "me-2");
                const txtNode = document.createTextNode("Veuiller entrer le montant valide !");
                const btnClose = document.createElement("button");
                btnClose.classList.add("btn", "btn-close");
                btnClose.type = "button";
                btnClose.setAttribute("data-bs-dismiss", "alert");

                alert.appendChild(iAlert);
                alert.appendChild(txtNode);
                alert.appendChild(btnClose);

                const modalBody = modal.querySelector(".modal-body");
                modalBody.insertBefore(alert, modalBody.querySelector(".div-montant"));
            }
        }
        //montant ok
        else {

            //AJAX add rendu
            fetch("../../../public/index.php?route=rendu/add_rendu/controller",
                {
                    method: 'POST',
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        code_pret: hiddenCP.value,
                        montantRendu: montantRendu.value,
                    })
                }
            )
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    else {
                        console.log("Ajax rendu / add rendu didn't respond");
                    }
                })
                .then(data => {

                    //response solde not sufficient
                    if (data === "solde !sufficient") {

                        //alert SNS
                        const alertSNS = document.querySelector(".alert-solde-not-sufficient");

                        if (!alertSNS) {
                            //alert
                            const alert = document.createElement("div");
                            alert.classList.add("alert", "alert-warning"
                                , "alert-dismissible", "alert-solde-not-sufficient");
                            const iAlert = document.createElement("i");
                            iAlert.classList.add("fas", "fa-info-circle", "me-2");
                            const txtNode = document.createTextNode("Solde insuffisant !");
                            const btnClose = document.createElement("button");
                            btnClose.classList.add("btn", "btn-close");
                            btnClose.type = "button";
                            btnClose.setAttribute("data-bs-dismiss", "alert");

                            alert.appendChild(iAlert);
                            alert.appendChild(txtNode);
                            alert.appendChild(btnClose);

                            const modalBody = modal.querySelector(".modal-body");
                            modalBody.insertBefore(alert, modalBody.querySelector(".div-montant"));
                        }
                    }
                    //response success
                    else if (data === "success") {
                        modalB.hide();

                        //**refresh list pret*/
                        listPretAll();
                        //**refresh list rendu*/
                        listRenduAll();
                    }
                    else {
                        console.log(data);
                    }
                })
                .catch(error => {
                    console.error("Erreur ajax / add rendu : " + error);
                });
        }
    });

    //-----------BTN SAVE DELETE RENDU--------
    const btnSaveDelete = document.getElementById("btn-save-delete-rendu");
    btnSaveDelete.addEventListener("click", () => {
        fetch("../../../public/index.php?route=rendu/delete_rendu/controller", {
            method: 'DELETE',
            headers: { "Content": "application/json" },
            body: JSON.stringify({ codeRendu: hiddenCR.value })
        })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("Erreur ajax delete rendu didn't respond");
                }
            })
            .then(data => {

                //respons success
                if (data === "success") {
                    modalDRB.hide();
                    alert("Rendu supprimé avec succès !");

                    //**refresh list rendu */
                    listRenduAll();
                    //**refresh list pret */
                    listPretAll();
                }
                //response !success
                else {
                    alert("Erreur de supprésion du rendu : " + data);
                }
            })
            .catch(error => {
                console.error("Erreur delete rendu : " + error);
            });
    });

    //-----------SEARCH PRET-----------------
    const inputSearchpret = document.getElementById('input-search-pret');
    inputSearchpret.addEventListener('keyup', () => {
    });

    //function search pret
    // function searchPret() {
    //     fetch("../../../public/index.php?route=rendu/search_pret/controller", {
    //         method: "POST",
    //         headers: { "Content-Type": "application/json" },
    //         body: JSON.stringify({ search: inputSearchpret.value.trim() })
    //     })
    //         .then(response => {
    //             if (response.ok) { 
    //                     return response.ok;
    //             }
    //             else{
    //             console.log();
    //             }
    //         })
    //         .
    // }
});