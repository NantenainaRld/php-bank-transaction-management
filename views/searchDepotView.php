<div class="d-flex gap-2">

    <!-- div code depot -->
    <div class="d-flex bg-light p-2 rounded">
        <label for="input-code-depot-search" class="text-secondary me-2 mt-1" style="text-wrap: nowrap;">
            <i class="fas fa-hashtag me-2"></i>Dépôt</label>
        <input type="text" id="input-code-depot-search" placeholder="code du dépôt"
            class="form-control form-control-sm">
    </div>
    <!-- div num_compte -->
    <div class="d-flex bg-light p-2 rounded">
        <label for="input-depot-num-compte-search" class="text-secondary me-2 mt-1" style="text-wrap: nowrap;">
            <i class="fas fa-user me-2"></i>Client</label>
        <input type="number" class="form-control form-control-sm" placeholder="numéro du compte"
            id="input-depot-num-compte-search">
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
    text-danger rounded" onclick="window.location.reload()">
        <i class="fas fa-undo"></i>
    </button>
    <!-- btn search -->
    <button class="btn btn-sm btn-light 
    text-success rounded px-3" id="btn-search">
        <i class="fas fa-search"></i>
    </button>
</div>