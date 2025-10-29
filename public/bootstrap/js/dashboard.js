document.addEventListener("DOMContentLoaded", () => {
    //nomBanque
    const nomBanque = document.getElementById("bank-name-aside");
    //emailBanque
    const emailBanque = document.getElementById("email-bank-aside");
    //btn update bank
    const btnUpdateBank = document.getElementById("btn-update-bank");
    //btn save update bank
    const btnSaveUpdateBank = document.getElementById("btn-save-update-bank");

    //function bankInfo
    function infoBank() {
        //AJAX info bank
        fetch("../../../public/index.php?route=bank_info/controller")
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                else {
                    console.log("AJAX didn't respond / bank info");
                }
            })
            .then(data => {
                nomBanque.textContent = data.nomBanque;
                emailBanque.innerHTML = "<i class='fas fa-envelope me-2'></i>"
                    + data.emailBanque;
            })
            .catch(error => {
                console.error("Erreur ajax / bank info : " + error);
            });
    }
    infoBank();

    //btn update bank clicked
    btnUpdateBank.addEventListener("click", () => {
        const modal = document.getElementById("modal-update-bank");
        const nomBanqueUpdate = document.getElementById("nom-bank-update");
        nomBanqueUpdate.value = nomBanque.textContent.trim();
        const emailBanqueUpdate = document.getElementById("email-bank-update");
        emailBanqueUpdate.value = emailBanque.textContent.trim();
        const passwordUpdate = document.querySelector("input[type='password']");
        const modalB = new bootstrap.Modal(modal);
        modalB.show();


        //btn save update bank clicked
        btnSaveUpdateBank.addEventListener("click", () => {

            //email !valid 
            if (!emailBanqueUpdate.checkValidity()) {
                const txtEmailIncorrect = document.getElementById("text-email-incorrect");
                txtEmailIncorrect.style.display = "block";
            }
            //email valid
            else {
                const txtEmailIncorrect = document.getElementById("text-email-incorrect");
                txtEmailIncorrect.style.display = "none";


                //AJAX update bank
                fetch("../../../public/index.php?route=update_bank/controller", {
                    method: 'PUT',
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        nomBanque: nomBanqueUpdate.value.trim(),
                        emailBanque: emailBanqueUpdate.value.trim(),
                        password: passwordUpdate.value
                    })
                })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        else {
                            console.log("AJAX didn't respond / update bank");
                        }
                    })
                    .then(data => {

                        //response input empty
                        if (data === "input empty") {
                            //alert input empty
                            const alertIE = document.querySelector(".alert-update-bank-input-empty");
                            if (!alertIE) {
                                const alert = document.createElement("div");
                                alert.classList.add("alert", "alert-warning",
                                    "fade", "show", "alert-dismissible", "alert-update-bank-input-empty");
                                alert.role = "alert";
                                //icon alert
                                const iAlert = document.createElement("i");
                                //text node
                                const txtNode = document.createTextNode("Les deux premiers champs\
                                sont obligatoires !");
                                //btn close
                                iAlert.classList.add("fas", "fa-info-circle", "me-2");
                                const btnClose = document.createElement("button");
                                btnClose.type = "button";
                                btnClose.classList.add("btn", "btn-close");
                                btnClose.setAttribute("data-bs-dismiss", "alert");
                                alert.appendChild(iAlert);
                                alert.appendChild(txtNode);
                                alert.appendChild(btnClose);
                                const modalBody = modal.querySelector(".modal-body");
                                modalBody.insertBefore(alert, modalBody.querySelector("div:first-child"));
                            }
                        }
                        //response email already xist
                        else if (data === "email exist") {
                            //alert email already exist
                            const alertEAE = document.querySelector(".alert-update-bank-email-already-exist");
                            if (!alertEAE) {
                                const alert = document.createElement("div");
                                alert.classList.add("alert", "alert-warning",
                                    "fade", "show", "alert-dismissible"
                                    , "alert-update-bank-email-already-exist");
                                alert.role = "alert";
                                //icon alert
                                const iAlert = document.createElement("i");
                                //text node
                                const txtNode = document.createTextNode("Cette adresse email existe déjà !");
                                //btn close
                                iAlert.classList.add("fas", "fa-info-circle", "me-2");
                                const btnClose = document.createElement("button");
                                btnClose.type = "button";
                                btnClose.classList.add("btn", "btn-close");
                                btnClose.setAttribute("data-bs-dismiss", "alert");
                                alert.appendChild(iAlert);
                                alert.appendChild(txtNode);
                                alert.appendChild(btnClose);
                                const modalBody = modal.querySelector(".modal-body");
                                modalBody.insertBefore(alert, modalBody.querySelector("div:first-child"));
                            }
                        }
                        //response update success
                        else if (data === 'success') {
                            modalB.hide();
                            infoBank();
                        }
                        else {
                            console.log(data);
                        }
                    })
                    .catch(error => {
                        console.error("Erreur ajax / update bank : " + error);
                    });
            }

        });
    });

});