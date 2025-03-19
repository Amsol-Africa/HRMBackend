<h3>Calculation Results</h3>
<ul>
    @foreach ($results as $result)
    <li>{{ $result['kpi_name'] }}: {{ $result['result_value'] }}
        ({{ $result['meets_target'] ? 'Meets Target' : 'Does Not Meet Target' }})</li>
    @endforeach
</ul>