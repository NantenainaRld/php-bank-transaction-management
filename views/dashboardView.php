<?php require_once __DIR__ . "/header.php"; ?>

<!-- modal add client -->
<div class="modal fade" id="modal-add-client" tabindex="-1" aria-labelledby="modal-title-add-client" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-dark text-light" id="modal-title-add-client">
                <i class="fas fa-user-plus me-2"></i>
                <h5 class=" modal-title">Ajout d'un client</h5>
            </div>

            <!-- body-->
            <div class="modal-body">

                <!-- div Nom-->
                <div class="mb-4">
                    <label for="add-nom" class="form-label">
                        <i class="fas fa-address-card me-2"></i>
                        Nom</label>
                    <input type="text" placeholder="Nom du client" id="add-nom" required class="form-control">
                    <p class="form-text text-danger d-none" id="add-nom-error">Le <b>Nom</b> est obligatoire .</p>
                </div>

                <!-- div Prenoms-->
                <div class="mb-4">
                    <label for="add-prenoms" class="form-label">
                        <i class="fas fa-address-card me-2"></i>
                        Prénoms</label>
                    <input type="text" placeholder="Prénoms du client" id="add-prenoms" class="form-control">
                </div>

                <!-- div Tel-->
                <div class="mb-4">
                    <label for="add-tel" class="form-label">
                        <i class="fas fa-phone me-2"></i>
                        Téléphone</label>
                    <input type="tel" pattern="^\+?[0-9\s\-\(\)]{7,20}$" placeholder="Téléphone du client" id="add-tel"
                        required class="form-control">
                    <p class="form-text text-danger d-none" id="add-tel-error">Numéro du <b>téléphone</b>
                        invalide</p>
                </div>

                <!-- div emailClient -->
                <div class="mb-4">
                    <label for="add-email" class="form-label">
                        <i class="fas fa-envelope me-2"></i>
                        Email</label>
                    <input type="email" placeholder="Email du client" pattern=".+@.+\..+" title="" id="add-email"
                        required class="form-control">
                    <p class="form-text text-danger d-none" id="add-email-error">Veuiller entrer une adresse
                        <b>email</b>
                        valide (ex:
                        nom@exemple.com)
                    </p>
                </div>

                <!-- footer  -->
                <div class="modal-footer d-flex">
                    <button type="button" id="btn-save-add-client" class="btn btn-primary btn-sm"><i
                            class="fas fa-check me-2"></i>
                        Ajouter
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- table client -->
<div class="table-container border p-4 mt-2 rounded" style="overflow: auto;height : 70vh;">
    <h5 class="text-secondary">
        <i class="fas fa-list-ol me-2"></i>Liste des clients
    </h5>
    <p class="my-2 text-secondary" id="total-client">Total client : </p>

    <table class="styled-table table-striped" style="overflow: auto;">
        <thead class="bg-dark text-light" style="position: sticky;top:0; z-index:1;">
            <tr>
                <th><i class="fas fa-hashtag me-2"></i>Numéro du compte</th>
                <th><i class="fas fa-address-card me-2"></i>
                    Nom et prénoms
                </th>
                <th><i class="fas fa-phone me-2"></i>Téléphone</th>
                <th><i class="fas fa-envelope me-1"></i>Email</th>
                <th><i class="fas fa-coins me-1"></i>Solde</th>
                <th><i class="fas fa-ellipsis-h me-2"></i>Actions</th>
            </tr>
        </thead>
        <tbody id="tbody-client">
        </tbody>
    </table>
</div>

<!-- modal update client -->
<div class="modal fade" id="modal-update-client" tabindex="-1" aria-labelledby="modal-title-update-client"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-dark text-light" id="modal-title-update-client">
                <i class="fas fa-user-edit me-2"></i>
                <h5 class=" modal-title">Modification des informations du client</h5>
            </div>

            <!-- body-->
            <div class="modal-body">
                <h6 class="text-secondary text-center mb-2" id="h6-update-client-numCompte">Numéro : <b>1234</b></h6>
                <div class="mb-4" id="div-first-child">
                    <label for="update-nom" class="form-label">
                        <i class="fas fa-address-card me-2"></i>
                        Nom</label>
                    <input type="text" placeholder="Nom du client" id="update-nom" required class="form-control">
                    <p class="form-text text-danger d-none" id="update-nom-error">Le <b>Nom</b> est obligatoire .</p>
                </div>

                <!-- div Prenoms-->
                <div class="mb-4">
                    <label for="update-prenoms" class="form-label">
                        <i class="fas fa-address-card me-2"></i>
                        Prénoms</label>
                    <input type="text" placeholder="Prénoms du client" id="update-prenoms" class="form-control">
                </div>

                <!-- div Tel-->
                <div class="mb-4">
                    <label for="update-tel" class="form-label">
                        <i class="fas fa-phone me-2"></i>
                        Téléphone</label>
                    <input type="tel" pattern="^\+?[0-9\s\-\(\)]{7,20}$" placeholder="Téléphone du client"
                        id="update-tel" required class="form-control">
                    <p class="form-text text-danger d-none" id="update-tel-error">Numéro du <b>téléphone</b>
                        invalide</p>
                </div>

                <!-- div emailClient -->
                <div class="mb-4">
                    <label for="update-email" class="form-label">
                        <i class="fas fa-envelope me-2"></i>
                        Email</label>
                    <input type="email" placeholder="Email du client" pattern=".+@.+\..+"
                        title="Veuiller entrer une adresse email valide (ex: nom@exemple.com)" id="update-email"
                        required class="form-control">
                    <p class="form-text text-danger d-none" id="update-email-error">Veuiller entrer une adresse
                        <b>email</b>
                        valide (ex:
                        nom@exemple.com)
                    </p>
                </div>

                <!-- div solde -->
                <div class="mb-4">
                    <label for="update-solde" class="form-label">
                        <i class="fas fa-coins me-2"></i>
                        Solde</label>
                    <input type="number" placeholder="Solde du client" id="update-solde" required class="form-control"
                        min="0">
                    <p class="form-text text-secondary ms-2">Minimum : 0 Ar</p>
                    <p class="form-text text-danger ms-2 d-none" id="update-solde-error"><b>Solde</b> invalide
                    </p>
                </div>

                <!-- footer  -->
                <div class="modal-footer d-flex">
                    <button type="button" id="btn-save-update-client" class="btn btn-primary btn-sm"><i
                            class="fas fa-save me-2"></i>
                        Enregistrer
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal delete client -->
<div class="modal fade" id="modal-delete-client" tabindex="-1" aria-labelledby="modal-title-delete-client"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- header-->
            <div class="modal-header bg-danger text-light" id="modal-title-delete-client">
                <i class="fas fa-question-circle me-2"></i>
                <h5 class="modal-title">Suppréssion du client</h5>
            </div>

            <!-- body-->
            <div class="modal-body text-start">
                <p class="text-secondary">Toute les transactions de ce compte seront supprimées aussi.</p>
                Voulez-vous vraiment supprimer ce compte?
            </div>

            <!-- footer  -->
            <div class="modal-footer d-flex">
                <button type="button" id="btn-save-delete-client" class="btn btn-danger btn-sm"><i
                        class="fas fa-check me-2"></i>
                    Oui, supprimer
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler</button>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>