<h3>{{ $kpi->name }} Results</h3>
<table>
    <thead>
        <tr>
            <th>Model ID</th>
            <th>Value</th>
            <th>Meets Target</th>
            <th>Measured At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kpi->results as $result)
        <tr>
            <td>{{ $result->model_id }}</td>
            <td>{{ $result->result_value }}</td>
            <td>{{ $result->meets_target ? 'Yes' : 'No' }}</td>
            <td>{{ $result->measured_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>