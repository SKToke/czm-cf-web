@php
    $filterRoute = $filterRoute ?? '#';
@endphp
<form id="filterForm" action="{{ $filterRoute }}" method="GET" data-target-container="{{ $dataTargetContainer ?? 'defaultContainerId' }}">
    <label for="month" class='d-inline-block'>Month</label>
    <select name="month" id="month" class='custom-select d-inline-block mb-10'>
        <option value="">Month</option>
        @for ($m=1; $m<=12; $m++)
            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
        @endfor
    </select>

    <label for="year" class='d-inline-block'>Year</label>
    <select name="year" id="year" class='custom-select d-inline-block mb-10'>
        <option value="">Year</option>
        @for ($y=date('Y'); $y>=2014; $y--)
            <option value="{{ $y }}">{{ $y }}</option>
        @endfor
    </select>

    <button type="button" id="resetButton" class='btn btn-sm reset-btn btn-outline-secondary'>Reset Filter</button>
</form>
