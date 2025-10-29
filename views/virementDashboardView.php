<?php require_once __DIR__ . "/header.php"; ?>

<!-- row for two tables -->
<div class="row">
    <!-- table virement -->
    <div class="table-container col-7 border rounded p-4" style="overflow: auto;height : 75vh;">
        <h5 class="text-secondary">
            <i class="fas fa-history me-2"></i>Historique des virements
        </h5>
        <table class="styled-table table-striped" style="overflow: auto;" id="table-virement">
            <thead class=" bg-dark text-light" style="position: sticky;top:0; z-index:1;">
                <tr>
                    <th><i class="fas fa-hashtag me-2"></i>Code du virement</th>
                    <th><i class="fas fa-user me-2"></i>Envoyeur</th>
                    <th><i class="fas fa-user me-2"></i>Bénéficiare</th>
                    <th><i class="fas fa-coins me-2"></i>
                        Montant
                    </th>
                    <th><i class="fas fa-calendar me-1"></i>Date du virement</th>
                    <th><i class="fas fa-ellipsis-h me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody id="tbody-virement">
            </tbody>
        </table>
    </div>
    <!-- table client -->
    <div class="table-container col-5 border p-4" style="overflow: auto;height : 75vh;">
        <h5 class="text-secondary">
            <i class="fas fa-list-ol me-2"></i>Liste des clients
        </h5>
        <div class="p-1 text-secondary rounded" style="background-color:  #e9ecef;">
            <label for="input-search-client-virement">Rechercher</label>
            <input type="text" id="input-search-client-virement" placeholder="Nom ou prénoms ou numéro du compte"
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
            <tbody id="tbody-client">
            </tbody>
        </table>
    </div>
</div>

<!-- modal virement -->
<div class="modal fade" id="modal-virement" tabindex="-1" aria-labelledby="modal-title-virement" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-dark text-light" id="modal-title-virement">
                <i class="fas fa-exchange-alt me-2"></i>
                <h5 class=" modal-title">Transfert d'argent</h5>
            </div>

            <!-- body-->
            <div class="modal-body">
                <!-- h6 num_compteE -->
                <h6 class="text-secondary text-center mb-4" id="h6-num-compte-e">Envoyeur : <b>1234</b>
                </h6>

                <!-- div num_compte -->
                <div class="mb-4" id="div-first">
                    <label for="input-recipient" class="form-label">
                        <i class="fas fa-user me-2"></i>
                        Destinataire</label>
                    <input type="number" id="input-recipient" placeholder="numéro du compte de destinataire" required
                        class="form-control">
                    <p class="form-text text-secondary ms-2" id="p-recipient-info">à</p>
                </div>
                <!-- div montantVirement -->
                <div class="mb-4">
                    <p class="text-secondary mb-4" id="p-info-depot"></p>
                    <label for="input-montant-virement" class="form-label">
                        <i class="fas fa-coins me-2"></i>
                        Montant à transférer</label>
                    <input type="number" id="input-montant-virement" min="50" value="50" required class="form-control">
                    <p class="form-text text-secondary" id="form-text-minimum"> Minimum 50 Ar</p>
                    <p class="form-text text-danger d-none" id="p-montant-virement-invalid">Montant invalide, minimum :
                        50 Ar
                    </p>
                </div>

                <!-- footer  -->
                <div class="modal-footer d-flex">
                    <button type="button" id="btn-save-virement" class="btn btn-primary btn-sm"><i
                            class="fas fa-check me-2"></i>
                        Transférer
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>