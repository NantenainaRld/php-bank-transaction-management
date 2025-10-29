<?php require_once __DIR__ . "/header.php"; ?>

<!-- row for two tables -->
<div class="row">
    <!-- table rendu -->
    <div class="table-container col-7 border rounded p-4" style="overflow: auto;height : 75vh;">
        <h5 class="text-secondary">
            <i class="fas fa-history me-2"></i>Historique des rendus
        </h5>
        <table class="styled-table table-striped" style="overflow: auto;">
            <thead class="bg-dark text-light" style="position: sticky;top:0; z-index:1;">
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>Code du rendu</th>
                    <th><i class="fas fa-hand-holding-dollar me-2"></i>
                        Code du prêt
                    </th>
                    <th><i class="fas fa-coins me-2"></i>Montant rendu</th>
                    <th><i class="fas fa-money-bill me-2"></i>Situation</th>
                    <th><i class="fas fa-coins me-2"></i>Reste</th>
                    <th><i class ss="fas fa-calendar me-2"></i>Date du rendu</th>
                    <th><i class="fas fa-ellipsis-h me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody id="tbody-rendu">
            </tbody>
        </table>
    </div>
    <!-- table pret -->
    <div class="table-container col-5 border p-4" style="overflow: auto;height : 75vh;">
        <h5 class="text-secondary">
            <i class="fas fa-list-ol me-2"></i>Liste des prêts
        </h5>
        <div class="p-1 text-secondary rounded" style="background-color:  #e9ecef;">
            <label for="input-search-pret">Rechercher</label>
            <input type="text" id="input-search-pret" placeholder="Code du prêt ou numéro du compte"
                class="form-control form-control-sm">
        </div>
        <table class="styled-table table-striped" style="overflow: auto;">
            <thead class="bg-dark text-light" style="position: sticky;top:0; z-index:1;">
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>Code du prê</th>
                    <th><i class="fas fa-user me-2"></i>Numéro du compte</th>
                    <th><i class="fas fa-coins me-2"></i>Montant</th>
                    <th><i class="fas fa-money-bill-alt me-2"></i>Reste à payer</th>
                    <th><i class="fas fa-ellipsis-h me-2"></i>Action</th>
                </tr>
            </thead>
            <tbody id="tbody-pret">
            </tbody>
        </table>
    </div>
</div>

<!-- modal rendu-->
<div class="modal fade" id="modal-rendu" tabindex="-1" aria-labelledby="modal-title-rendu" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-dark text-light" id="modal-title-rendu">
                <i class="fas fa-undo me-2"></i>
                <h5 class="modal-title">Remboursement du prêt</h5>
            </div>

            <!-- body-->
            <div class="modal-body">

                <p class="text-dark mb-4 fw-bold text-center" id="p-code-pret"></p>
                <!-- div montantPret-->
                <div class="mb-4 div-montant">
                    <label for=" input-montant-pret" class="form-label">
                        <i class="fas fa-coins me-2"></i>
                        Montant à rembourser</label>
                    <input type="number" id="input-montant-rendu" min="50" required class="form-control">
                    <p class="form-text text-secondary" id="p-montant-min-max"></p>
                </div>

                <!-- div tout payé-->
                <div class="mb-4 text-center text-secondary">
                    <p style="font-size: small;">ou</p>
                    <button type="button" id="btn-tout-paye"
                        class=" btn btn-sm btn-light text-success border unclicked">
                        <i class="fas fa-check-circle me-2"></i>Tout payé
                    </button>
                </div>

            </div>

            <!-- footer  -->
            <div class="modal-footer d-flex">
                <button type="button" id="btn-save-rendu" class="btn btn-primary btn-sm"><i
                        class="fas fa-check me-2"></i>
                    Rembourser
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler</button>
            </div>
        </div>
    </div>
</div>

<!-- modal delete rendu -->
<div class="modal fade" id="modal-delete-rendu" tabindex="-1" aria-labelledby="modal-title-delete-rendu"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-danger text-light" id="modal-title-delete-rendu">
                <i class="fas fa-question-circle me-2"></i>
                <h5 class="modal-title">Suppréssion du rendu</h5>
            </div>

            <!-- body-->
            <div class="modal-body text-start text-secondary">
            </div>

            <!-- footer  -->
            <div class="modal-footer d-flex">
                <button type="button" id="btn-save-delete-rendu" class="btn btn-danger btn-sm"><i
                        class="fas fa-check me-2"></i>
                    Oui, supprimer
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>