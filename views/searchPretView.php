<div class="d-flex gap-2">

    <!-- div codePret -->
    <div class="d-flex bg-light p-2 rounded">
        <label for="input-code-pret" class="text-secondary me-2 mt-1" style="text-wrap: nowrap;">
            <i class="fas fa-hashtag me-2"></i>Code du prêt</label>
        <input type="search" class="form-control" id="input-code-pret">
    </div>
    <!-- div num_compte-->
    <div class="d-flex bg-light p-2 rounded">
        <label for="input-num-compte" class="text-secondary me-2 mt-1" style="text-wrap: nowrap;">
            <i class="fas fa-user me-2"></i>Numéro du compte</label>
        <input type="text" id="input-num-compte" class="form-control form-control-sm">
    </div>
    <!-- div situation-->
    <div class="d-flex bg-light p-2 rounded">
        <label for="situation" class="text-secondary me-2 mt-1" style="text-wrap: nowrap;">
            <i class="fas fa-money-bill me-1"></i>Situation</label>
        <select id="situation" class="form-select">
            <option value="tout">Tout</option>
            <option value="non remboursé">Non remboursé</option>
            <option value="tout payé">Tout payé</option>
            <option value="payé une part">Payé une part </option>
        </select>
    </div>
    <!-- div date -->
    <div class="d-flex bg-light p-2 rounded">
        <i class="fas fa-calendar me-2 mt-2 text-secondary"></i>
        <label for="date-du" class="text-secondary me-2 mt-1">Du</label>
        <input type="date" class="form-control form-control-sm me-2" id="date-du">
        <label for="date-au" class="text-secondary me-2 mt-1">Au</label>
        <input type="date" class="form-control form-control-sm" id="date-au">
    </div>
</div>