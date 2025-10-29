<div class="d-flex gap-2">

    <!-- div codeVirement -->
    <div class="d-flex bg-light p-2 rounded">
        <label for="input-code-virement" class="text-secondary me-2 mt-1" style="text-wrap: nowrap;">
            <i class="fas fa-hashtag me-2"></i>Code du virement</label>
        <input type="s" class="form-control" id="input-code-virement">
    </div>
    <!-- div num_compteE-->
    <div class="d-flex bg-light p-2 rounded">
        <label for="input-num-compteE" class="text-secondary me-2 mt-1" style="text-wrap: nowrap;">
            <i class="fas fa-user me-2"></i>Envoyeur</label>
        <input type="text"
            id="input-num-compteE" class="form-control form-control-sm">
    </div>
    <!-- div num_compteB-->
    <div class="d-flex bg-light p-2 rounded">
        <label for="input-num-compteB" class="text-secondary me-2 mt-1" style="text-wrap: nowrap;">
            <i class="fas fa-user me-2"></i>Bénéficiaire</label>
        <input type="text"
            id="input-num-compteB" class="form-control form-control-sm">
    </div>
    <!-- div date -->
    <div class="d-flex bg-light p-2 rounded">
        <i class="fas fa-calendar me-2 mt-2 text-secondary"></i>
        <label for="date-du" class="text-secondary me-2 mt-1">Du</label>
        <input type="date" class="form-control form-control-sm me-2" id="date-du">
        <label for="date-au" class="text-secondary me-2 mt-1">Au</label>
        <input type="date" class="form-control form-control-sm" id="date-au">
    </div>
    <!-- btn reset -->
    <button class="btn btn-sm btn-light 
    text-danger rounded ms-2 my-2" id="btn-reset">
        <i class="fas fa-undo"></i>
    </button>
</div>