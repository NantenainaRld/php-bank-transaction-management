<?php require_once __DIR__ . "/header.php"; ?>

<!-- row for two tables -->
<div class="row">
    <!-- table depot -->
    <div class="table-container col-7 border rounded p-4" style="overflow: auto;height : 75vh;">
        <h5 class="text-secondary">
            <i class="fas fa-history me-2"></i>Historique des prêts
        </h5>
        <p class="my-2 text-secondary" id="total-benefice">Total Bénéfice : </p>
        <table class="styled-table table-striped" style="overflow: auto;" id="table-pret">
            <thead class="bg-dark text-light" style="position: sticky;top:0; z-index:1;">
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>Code du prêt</th>
                    <th><i class="fas fa-coins me-2"></i>
                        Montant
                    </th>
                    <th><i class="fas fa-user me-2"></i>Numéro du compte</th>
                    <th><i class="fas fa-calendar me-1"></i>Date</th>
                    <th><i class="fas fa-money-bill me-1"></i>Situation</th>
                    <th><i class="fas fa-piggy-bank me-1"></i>Bénéfice de la banque</th>
                    <th><i class="fas fa-clock me-1"></i>Durée</th>
                    <th><i class="fas fa-ellipsis-h me-1"></i>Actions</th>
                </tr>
            </thead>
            <tbody id="tbody-pret">
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
            <input type="text" id="input-search-client-pret" placeholder="Nom ou prénoms ou numéro du compte"
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
            <tbody id="tbody-client-pret">
            </tbody>
        </table>
    </div>
</div>

<!-- modal pret -->
<div class="modal fade" id="modal-pret" tabindex="-1" aria-labelledby="modal-title-deposite" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-dark text-light" id="modal-title-pret">
                <i class="fas fa-arrow-down me-2"></i>
                <h5 class=" modal-title">Prêter de l'argent</h5>
            </div>

            <!-- body-->
            <div class="modal-body">

                <!-- div montantPret-->
                <div class="mb-4">
                    <p class="text-secondary mb-4" id="p-info-pret"></p>
                    <label for="input-montant-pret" class="form-label">
                        <i class="fas fa-coins me-2"></i>
                        Montant à prêter</label>
                    <input type="number" id="input-montant-pret" min="5000" required class="form-control">
                    <p class="form-text text-secondary" id="form-text-minimum"> Minimum 5000 Ar</p>
                </div>
                <!-- div duree-->
                <div class="mb-4">
                    <label for="input-duree" class="form-label">
                        <i class="fas fa-clock me-2"></i>
                        Durée</label>
                    <input type="number" id="input-duree" min="7" required class="form-control">
                    <p class="form-text text-secondary" id="form-text-minimum"> Minimum 7 jours</p>
                </div>

                <!-- footer  -->
                <div class="modal-footer d-flex">
                    <button type="button" id="btn-save-pret" class="btn btn-primary btn-sm"><i
                            class="fas fa-check me-2"></i>
                        Prêter
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal delete pret -->
<div class="modal fade" id="modal-delete-pret" tabindex="-1" aria-labelledby="modal-title-delete-pret"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-danger text-light" id="modal-title-delete-pret">
                <i class="fas fa-question-circle me-2"></i>
                <h5 class="modal-title">Suppréssion du pret</h5>
            </div>

            <!-- body-->
            <div class="modal-body text-start">
                <p class="text-secondary">Tout les historiques de rendu pour ce prêt seront supprimées aussi.</p>
                Voulez-vous vraiment supprimer ce prêt?
            </div>

            <!-- footer  -->
            <div class="modal-footer d-flex">
                <button type="button" id="btn-save-delete-pret" class="btn btn-danger btn-sm"><i
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