<?php require_once __DIR__ . "/header.php"; ?>

<!-- row for two tables -->
<div class="row">
    <!-- table depot -->
    <div class="table-container col-7 border rounded p-4" style="overflow: auto;height : 75vh;">
        <h5 class="text-secondary">
            <i class="fas fa-history me-2"></i>Historique des dépôts
        </h5>
        <table class="styled-table table-striped" style="overflow: auto;" id="table-depot">
            <thead class="bg-dark text-light" style="position: sticky;top:0; z-index:1;">
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>Code du dépôt</th>
                    <th><i class="fas fa-coins me-2"></i>
                        Montant
                    </th>
                    <th><i class="fas fa-user me-2"></i>Numéro du compte</th>
                    <th><i class="fas fa-calendar me-1"></i>Date du dépôt</th>
                    <th><i class="fas fa-ellipsis-h me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody id="tbody-depot">
            </tbody>
        </table>
    </div>
    <!-- table client -->
    <div class="table-container col-5 border p-4" style="overflow: auto;height : 75vh;">
        <h5 class="text-secondary">
            <i class="fas fa-list-ol me-2"></i>Liste des clients
        </h5>
        <div class="p-1 text-secondary rounded" style="background-color:  #e9ecef;">
            <label for="input-search-client-depot">Rechercher</label>
            <input type="text" id="input-search-client-depot" placeholder="Nom ou prénoms ou numéro du compte"
                class="form-control form-control-sm">
        </div>
        <table class="styled-table table-striped" style="overflow: auto;">
            <thead class="bg-dark text-light" style="position: sticky;top:0; z-index:1;">
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>Numéro du compte</th>
                    <th><i class="fas fa-address-card me-2"></i>
                        Nom et prénoms
                    </th>
                    <th><i class="fas fa-coins me-2"></i>Solde</th>
                    <th><i class="fas fa-ellipsis-h me-2"></i>Action</th>
                </tr>
            </thead>
            <tbody id="tbody-client-depot">
            </tbody>
        </table>
    </div>
</div>

<!-- modal deposite -->
<div class="modal fade" id="modal-deposite" tabindex="-1" aria-labelledby="modal-title-deposite" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-dark text-light" id="modal-title-deposite">
                <i class="fas fa-arrow-down me-2"></i>
                <h5 class=" modal-title">Déposer de l'argent</h5>
            </div>

            <!-- body-->
            <div class="modal-body">

                <!-- div montantDepot-->
                <div class="mb-4" id="div-first">
                    <h6 class="text-secondary text-center mb-3" id="h6-numCompte-deposite">Numéro : <b>1234</b>
                    </h6>
                    <label for="input-montant-depot" class="form-label" id="label">
                        <i class=" fas fa-coins me-2"></i>
                        Montant à déposer</label>
                    <input type="number" id="input-montant-depot" min="50" value="50" required class="form-control">
                    <p class="form-text text-secondary ms-2" id="form-text-minimum"> Minimum : 50 Ar</p>
                    <p class="form-text text-danger ms-2 d-none" id="p-montant-invalid"> Montant incorrecte, minimum :
                        50 Ar .
                    </p>
                </div>

                <!-- footer  -->
                <div class="modal-footer d-flex">
                    <button type="button" id="btn-save-depot" class="btn btn-primary btn-sm"><i
                            class="fas fa-check me-2"></i>
                        Déposer
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal update depot -->
<div class="modal fade" id="modal-update-depot" tabindex="-1" aria-labelledby="modal-title-update-depot"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-dark text-light" id="modal-title-update-depot">
                <i class="fas fa-edit me-2"></i>
                <h5 class="modal-title">Modification d'un dépôt</h5>
            </div>

            <!-- body-->
            <div class="modal-body">
                <!-- h6 code depot -->
                <h6 class="text-secondary text-center mb-4" id="h6-code-depot">Dépôt numéro : <b>1234</b>
                </h6>
                <!-- div montant-update-depot-->
                <div class="mb-4" id="div-first-update-depot">
                    <label for="input-montant-depot-update" class="form-label">
                        <i class=" fas fa-coins me-2"></i>
                        Montant du dépôt</label>
                    <input type="number" id="input-montant-depot-update" placeholder="montant dépôsé" min="50"
                        value="50" required class="form-control">
                    <p class="form-text text-secondary ms-2"> Minimum : 50 Ar</p>
                    <p class="form-text text-danger ms-2 d-none" id="p-montant-invalid-update"> Montant incorrecte,
                        minimum :
                        50 Ar .
                    </p>
                </div>
                <!-- div date depot-->
                <div class="mb-4">
                    <label for="input-date-depot-update" class="form-label">
                        <i class=" fas fa-calendar me-2"></i>
                        Date du dépôt</label>
                    <input type="date" id="input-date-depot-update" placeholder="date dépôsé" required
                        class="form-control">
                    <p class="form-text text-danger ms-2 d-none" id="p-date-depot-update-empty">
                        Cette date est obligatoire
                    </p>
                </div>

                <!-- footer  -->
                <div class="modal-footer d-flex">
                    <button type="button" id="btn-save-update-depot" class="btn btn-primary btn-sm"><i
                            class="fas fa-check me-2"></i>
                        Enregistrer
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>