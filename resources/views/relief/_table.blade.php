<table class="table table-striped table-bordered" id=reliefsTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Relief Name</th>
            <th>Calculation Basis</th>
            <th>Rate Percentage</th>
            <th>Rate Amount</th>
            <th>Is Manadatory</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reliefs as $key => $relief)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $relief->name }}</td>
                <td>{{ formatStatus($relief->tax_application) }}</td>
                <td>{{ $relief->rate_percentage }} <i class="fa-solid fa-percent"></i> </td>
                <td>{{ $relief->fixed_amount }} </td>
                <td>{{ $relief->is_mandatory ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="" class="btn btn-primary btn-sm"> <i class="bi bi-pencil-square"></i> Edit</a>
                    <button type="button"  class="btn btn-danger btn-sm" data-relief="{{ $relief->slug }}" onclick="deleteRelief(this)""> <i class="bi bi-trash"></i> Delete</button>
                    <button type="button" class="btn btn-success btn-sm" data-relief="{{ $relief->slug }}" onclick="showRelief(this)" class="view-btn"> <i class="bi bi-view-list"></i> View</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
