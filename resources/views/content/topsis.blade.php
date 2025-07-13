@extends('layouts.app')

@section('content')
 {{-- ✅ Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- ✅ Optional: Bootstrap Icons (jika kamu butuh icon) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    thead {
        background-color: #032b54 !important;
        color: white !important;
    }

     .bg-primary {
        background-color: #032b54 !important;
    }

    table th,
    table td {
        vertical-align: center !important;
    }

    .table-responsive {
        margin-bottom: 2rem;
    }

    h4 {
        font-weight: bold;
        margin-top: 2rem;
        color: #007bff;
    }

    ol.fw-bold li {
        margin-bottom: 5px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0 text-center">Hasil Perhitungan Metode TOPSIS</h3>
                </div>
                <div class="card-body p-4">

                    {{-- 1. Matriks Keputusan --}}
                    <h4>1. Matriks Keputusan</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="bg-primary text-white text-center">No</th>
                                    <th class="bg-primary text-white text-center">Alternatif</th>
                                    <th class="bg-primary text-white text-center">Likes</th>
                                    <th class="bg-primary text-white text-center">Comments</th>
                                    <th class="bg-primary text-white text-center">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($decisionMatrix as $title => $row)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $title }}</td>
                                    <td class="text-center">{{ $row['likes'] }}</td>
                                    <td class="text-center">{{ $row['comments'] }}</td>
                                    <td class="text-center">{{ $row['views'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 2. Nilai Pembagi --}}
                    <h4>2. Nilai Pembagi (Akar Kuadrat Jumlah Kuadrat)</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white text-center text-center">Kriteria</th>
                                    <th class="bg-primary text-white text-center text-center">Likes</th>
                                    <th class="bg-primary text-white text-center text-center">Comments</th>
                                    <th class="bg-primary text-white text-center text-center">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Nilai Pembagi</td>
                                    <td class="text-center">{{ number_format($divisors['likes'], 4) }}</td>
                                    <td class="text-center">{{ number_format($divisors['comments'], 4) }}</td>
                                    <td class="text-center">{{ number_format($divisors['views'], 4) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. Matriks Ternormalisasi --}}
                    <h4>3. Matriks Ternormalisasi</h4>
                    <div class="table-responsive">
                        <table id="normalizedTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white text-center">No</th>
                                    <th class="bg-primary text-white text-center">Alternatif</th>
                                    <th class="bg-primary text-white text-center">Likes</th>
                                    <th class="bg-primary text-white text-center">Comments</th>
                                    <th class="bg-primary text-white text-center">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($normalizedMatrix as $alternative => $values)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $alternative }}</td>
                                    @foreach($values as $value)
                                    <td class="text-center">{{ number_format($value, 4) }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 4. Matriks Terbobot --}}
                    <h4>4. Matriks Ternormalisasi Terbobot</h4>
                    <div class="table-responsive">
                        <table id="weightedTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white text-center">No</th>
                                    <th class="bg-primary text-white text-center">Alternatif</th>
                                    <th class="bg-primary text-white text-center">Likes (V1)</th>
                                    <th class="bg-primary text-white text-center">Comments (V2)</th>
                                    <th class="bg-primary text-white text-center">Views (V3)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weightedMatrix as $i => $row)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $i }}</td>
                                    @foreach($row as $value)
                                    <td class="text-center">{{ number_format($value, 4) }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 5. Solusi Ideal --}}
                    <h4>5. Solusi Ideal Positif (A⁺) dan Negatif (A⁻)</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white text-center">No</th>
                                    <th class="bg-primary text-white text-center">Solusi</th>
                                    <th class="bg-primary text-white text-center">Likes</th>
                                    <th class="bg-primary text-white text-center">Comments</th>
                                    <th class="bg-primary text-white text-center">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Solusi Ideal Positif (A⁺)</td>
                                    @foreach($idealPositive as $value)
                                    <td class="text-center">{{ number_format($value, 4) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Solusi Ideal Negatif (A⁻)</td>
                                    @foreach($idealNegative as $value)
                                    <td class="text-center">{{ number_format($value, 4) }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- 6. Jarak Solusi Ideal --}}
                    <h4>6. Jarak terhadap Solusi Ideal Positif dan Negatif</h4>
                    <div class="table-responsive">
                        <table id="measuresTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white text-center">No</th>
                                    <th class="bg-primary text-white text-center">Alternatif</th>
                                    <th class="bg-primary text-white text-center">Jarak ke A⁺ (D⁺)</th>
                                    <th class="bg-primary text-white text-center">Jarak ke A⁻ (D⁻)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alternatives as $index => $alternative)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $alternative }}</td>
                                    <td class="text-center">{{ number_format($distancePositive[$alternative], 4) }}</td>
                                    <td class="text-center">{{ number_format($distanceNegative[$alternative], 4) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 7. Nilai Preferensi (Ci) --}}
                    <h4>7. Nilai Preferensi (Ci)</h4>
                    <div class="table-responsive">
                        <table id="closenessTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white text-center">No</th>
                                    <th class="bg-primary text-white text-center">Alternatif</th>
                                    <th class="bg-primary text-white text-center">Nilai Ci</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $index => $result)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $result['alternative'] }}</td>
                                    <td class="text-center">{{ number_format($result['score'], 4) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 8. Ranking Akhir --}}
                    <h4>8. Ranking Hasil Akhir</h4>
                    <ol class="fw-bold">
                        @foreach($results as $result)
                        <li>{{ $result['alternative'] }} (Ci = {{ number_format($result['score'], 4) }})</li>
                        @endforeach
                    </ol>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#normalizedTable, #weightedTable, #measuresTable, #closenessTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});
</script>
@endpush
