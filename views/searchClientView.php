<div class="d-flex gap-3">

    <!-- div search nom or numCompte -->
    <label for="num-compte-search" class="text-secondary mt-1" style="text-wrap: nowrap;">
        <i class="fas fa-hashtag me-2"></i>Recherecher un client</label>
    <input type="text" name="input-search-client" id="input-search-client" class="form-control"
        placeholder="Numéro du compte ou nom ou prénoms">
    <button class="btn btn-sm btn-light text-danger" type="button"><i class="fas fa-undo"
            onclick="window.location.reload()"></i></button>
    <button class=" btn btn-sm btn-light text-success px-3" type="button" id="btn-search"><i
            class="fas fa-search"></i></button>
</div>