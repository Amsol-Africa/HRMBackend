<form action="" id="allowancesForm">

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="name">Allowance Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Allowance Name">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" value="1" id="is_taxable" name="is_taxable">
                <label class="form-check-label" for="is_taxable">
                    Is Taxable
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <button type="button" class="btn btn-primary w-100" onclick="saveAllowance(this)">
            <i class="bi bi-check-circle me-1"></i> Save Allowance
        </button>
    </div>

</form>