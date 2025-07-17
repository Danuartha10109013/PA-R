<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use App\Algorithms\Topsis;

class ContentController extends Controller
{
    // public function index()
    // {
    //     $popularContent = Content::getPopularContent(5);
    //     $allContent = Content::orderBy('created_at', 'desc')->paginate(15);

    //     return view('content.index', compact('popularContent', 'allContent'));
    // }

//     public function index()
// {
//     //ini yang buat spk disini semua , ga pake lagi yang di folder Algorithms
//     // 1. Ambil 3 konten terakhir sesuai file
//     $contents = \App\Models\Content::orderBy('created_at', 'desc')->get();

//     // 2. Bangun matriks keputusan
//     $data = [];
//     foreach ($contents as $content) {
//         $data[] = [
//             'id' => $content->id,
//             'title' => $content->title,
//             'likes' => $content->likes,
//             'comments' => $content->comments,
//             'views' => $content->views,
//         ];
//     }

//     // Ambil semua nilai dari masing-masing kriteria
//     $likes = array_column($data, 'likes');
//     $comments = array_column($data, 'comments');
//     $views = array_column($data, 'views');

//     // 3. Normalisasi vektor
//     $normLikes = sqrt(array_sum(array_map(fn($v) => $v ** 2, $likes)));
//     $normComments = sqrt(array_sum(array_map(fn($v) => $v ** 2, $comments)));
//     $normViews = sqrt(array_sum(array_map(fn($v) => $v ** 2, $views)));

//     // 4. Normalisasi dan pembobotan
//     $weights = [
//         'likes' => 3.5,
//         'comments' => 2.5,
//         'views' => 4.0,
//     ];

//     $topsis = [];
//     foreach ($data as $d) {
//         $norm = [
//             'likes' => ($d['likes'] / $normLikes) * $weights['likes'],
//             'comments' => ($d['comments'] / $normComments) * $weights['comments'],
//             'views' => ($d['views'] / $normViews) * $weights['views'],
//         ];
//         $topsis[] = array_merge($d, $norm);
//     }

//     // 5. Solusi ideal positif (maks untuk benefit, min untuk cost)
//     $vPos = [
//         'likes' => max(array_column($topsis, 'likes')),
//         'comments' => min(array_column($topsis, 'comments')), // cost
//         'views' => max(array_column($topsis, 'views')),
//     ];

//     $vNeg = [
//         'likes' => min(array_column($topsis, 'likes')),
//         'comments' => max(array_column($topsis, 'comments')), // cost
//         'views' => min(array_column($topsis, 'views')),
//     ];

//     // 6. Hitung jarak ke solusi ideal positif dan negatif
//     foreach ($topsis as &$alt) {
//         $dPlus = sqrt(
//             pow($alt['likes'] - $vPos['likes'], 2) +
//             pow($alt['comments'] - $vPos['comments'], 2) +
//             pow($alt['views'] - $vPos['views'], 2)
//         );
//         $dMinus = sqrt(
//             pow($alt['likes'] - $vNeg['likes'], 2) +
//             pow($alt['comments'] - $vNeg['comments'], 2) +
//             pow($alt['views'] - $vNeg['views'], 2)
//         );
//         $alt['d_plus'] = $dPlus;
//         $alt['d_minus'] = $dMinus;
//         $alt['score'] = $dMinus / ($dPlus + $dMinus); // Closeness Coefficient
//     }

//     // 7. Urutkan berdasarkan nilai preferensi tertinggi
//     usort($topsis, fn($a, $b) => $b['score'] <=> $a['score']);

//     // Pagination all content (optional)
//     $allContent = \App\Models\Content::orderBy('created_at', 'desc')->paginate(15);
//     // dd($topsis);
//     foreach ($topsis as &$item) {
//         $model = Content::find($item['id']);
//         $model->score = $item['score'];
//         $model->d_plus = $item['d_plus'];
//         $model->d_minus = $item['d_minus'];
//         $item = $model;
//     }
//     return view('content.index', [
//         'popularContent' => $topsis,
//         'allContent' => $allContent,
//     ]);
// }

public function index()
{
    // Ambil semua konten, bisa difilter jadi 3 terakhir jika perlu
    $contents = \App\Models\Content::orderBy('created_at', 'desc')->get();

    // Jika data kosong, langsung kembalikan view dengan data kosong
    if ($contents->isEmpty()) {
        return view('content.index', [
            'popularContent' => [],
            'allContent' => [],
        ]);
    }

    // 2. Bangun matriks keputusan
    $data = [];
    foreach ($contents as $content) {
        $data[] = [
            'id' => $content->id,
            'title' => $content->title,
            'likes' => $content->likes,
            'comments' => $content->comments,
            'views' => $content->views,
        ];
    }

    // Ambil semua nilai dari masing-masing kriteria
    $likes = array_column($data, 'likes');
    $comments = array_column($data, 'comments');
    $views = array_column($data, 'views');

    // 3. Normalisasi vektor
    $normLikes = sqrt(array_sum(array_map(fn($v) => $v ** 2, $likes))) ?: 1;
    $normComments = sqrt(array_sum(array_map(fn($v) => $v ** 2, $comments))) ?: 1;
    $normViews = sqrt(array_sum(array_map(fn($v) => $v ** 2, $views))) ?: 1;

    // 4. Normalisasi dan pembobotan
    $weights = [
        'likes' => 3.5,
        'comments' => 2.5,
        'views' => 4.0,
    ];

    $topsis = [];
    foreach ($data as $d) {
        $norm = [
            'likes' => ($d['likes'] / $normLikes) * $weights['likes'],
            'comments' => ($d['comments'] / $normComments) * $weights['comments'],
            'views' => ($d['views'] / $normViews) * $weights['views'],
        ];
        $topsis[] = array_merge($d, $norm);
    }

    // 5. Solusi ideal positif dan negatif
    $vPos = [
        'likes' => max(array_column($topsis, 'likes')),
        'comments' => min(array_column($topsis, 'comments')), // cost
        'views' => max(array_column($topsis, 'views')),
    ];

    $vNeg = [
        'likes' => min(array_column($topsis, 'likes')),
        'comments' => max(array_column($topsis, 'comments')), // cost
        'views' => min(array_column($topsis, 'views')),
    ];

    // 6. Hitung jarak ke solusi ideal positif dan negatif
    foreach ($topsis as &$alt) {
        $dPlus = sqrt(
            pow($alt['likes'] - $vPos['likes'], 2) +
            pow($alt['comments'] - $vPos['comments'], 2) +
            pow($alt['views'] - $vPos['views'], 2)
        );
        $dMinus = sqrt(
            pow($alt['likes'] - $vNeg['likes'], 2) +
            pow($alt['comments'] - $vNeg['comments'], 2) +
            pow($alt['views'] - $vNeg['views'], 2)
        );
        $alt['d_plus'] = $dPlus;
        $alt['d_minus'] = $dMinus;
        $alt['score'] = ($dPlus + $dMinus) > 0 ? $dMinus / ($dPlus + $dMinus) : 0;
    }

    // 7. Urutkan berdasarkan nilai preferensi tertinggi
    usort($topsis, fn($a, $b) => $b['score'] <=> $a['score']);

    // Pagination untuk semua konten
    $allContent = \App\Models\Content::orderBy('created_at', 'desc')->paginate(15);

    // Ambil model ulang berdasarkan ID
    foreach ($topsis as &$item) {
        $model = Content::find($item['id']);
        $model->score = $item['score'];
        $model->d_plus = $item['d_plus'];
        $model->d_minus = $item['d_minus'];
        $item = $model;
    }

    return view('content.index', [
        'popularContent' => $topsis,
        'allContent' => $allContent,
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'views' => 'required|integer|min:0',
            'likes' => 'required|integer|min:0',
            'comments' => 'required|integer|min:0',
        ]);

        // Set default values for metrics if not provided
        $validated['views'] = $validated['views'] ?? 0;
        $validated['likes'] = $validated['likes'] ?? 0;
        $validated['comments'] = $validated['comments'] ?? 0;

        Content::create($validated);

        return redirect()->route('content.index')->with('success', 'Content created successfully');
    }

    public function like($id)
    {
        $content = Content::findOrFail($id);
        $content->increment('likes');
        return response()->json(['likes' => $content->likes]);
    }

    public function comment($id)
    {
        $content = Content::findOrFail($id);
        $content->increment('comments');
        return response()->json(['comments' => $content->comments]);
    }

    public function view($id)
    {
        $content = Content::findOrFail($id);
        $content->increment('views');
        return response()->json(['views' => $content->views]);
    }

    /**
     * Display the normalization divisors.
     *
     * @return \Illuminate\View\View
     */
    public function showNormalizationDivisors()
    {
        $topsis = $this->initializeTopsis();
        $topsis->calculate(); // This will trigger the calculation of divisors
        
        return view('content.normalization-divisors', [
            'divisors' => $topsis->getNormalizationDivisors()
        ]);
    }

    /**
     * Display the normalized decision matrix.
     *
     * @return \Illuminate\View\View
     */
    public function showNormalizedMatrix()
    {
        $topsis = $this->initializeTopsis();
        
        $normalizedMatrix = [];
        foreach ($topsis->getNormalizedMatrix() as $i => $row) {
            $normalizedMatrix[$topsis->getAlternatives()[$i]] = $row;
        }
        
        return view('content.normalized-matrix', [
            'normalizedMatrix' => $normalizedMatrix
        ]);
    }

    /**
     * Display the weighted normalized decision matrix.
     */
    public function weightedNormalizedMatrix()
    {
        $topsis = $this->initializeTopsis();
        $topsis->calculate(); // This will calculate everything including the weighted matrix
        
        return view('content.weighted-normalized-matrix', [
            'weightedMatrix' => $topsis->getWeightedNormalizedMatrix(),
            'alternatives' => $topsis->getAlternatives()
        ]);
    }

    /**
     * Initialize TOPSIS with content data
     * 
     * @return \App\Algorithms\Topsis
     */
    private function initializeTopsis()
    {
        $contents = Content::all();
        
        return new Topsis(
            $contents->pluck('title')->toArray(),
            ['likes', 'comments', 'views'],
            $contents->map(function ($content) {
                return [$content->likes, $content->comments, $content->views];
            })->toArray(),
            [0.4, 0.3, 0.3], // weights for likes, comments, views
            [true, true, true] // all are benefit criteria
        );
    }

    public function showIdealSolutions()
    {
        $topsis = $this->initializeTopsis();
        $weighted = $topsis->getWeightedNormalizedMatrix();
        [$idealPositive, $idealNegative] = $topsis->getIdealSolutions($weighted);
        
        return view('content.ideal-solutions', [
            'criteria' => $topsis->getCriteria(),
            'idealPositive' => $idealPositive,
            'idealNegative' => $idealNegative
        ]);
    }

    public function showSeparationMeasures()
    {
        $topsis = $this->initializeTopsis();
        [$distancePositive, $distanceNegative] = $topsis->getDistances();
        
        return view('content.separation-measures', [
            'alternatives' => array_map(function($alt) {
                return ['name' => $alt];
            }, $topsis->getAlternatives()),
            'distancePositive' => $distancePositive,
            'distanceNegative' => $distanceNegative
        ]);
    }

    /**
     * Display the relative closeness matrix and rankings.
     *
     * @return \Illuminate\View\View
     */
    public function showRelativeCloseness()
    {
        $topsis = $this->initializeTopsis();
        $results = $topsis->calculate();
        
        return view('content.relative-closeness', [
            'results' => $results
        ]);
    }

    // public function topsis()
    // {
    //     $topsis = $this->initializeTopsis();
    //     $topsis->calculate();
        
    //     // Get normalized matrix
    //     $normalizedMatrix = $topsis->getNormalizedMatrix();
        
    //     // Get weighted normalized matrix
    //     $weightedMatrix = $topsis->getWeightedNormalizedMatrix();
        
    //     // Get ideal solutions
    //     [$idealPositive, $idealNegative] = $topsis->getIdealSolutions($weightedMatrix);
        
    //     // Get separation measures
    //     [$distancePositive, $distanceNegative] = $topsis->getDistances();
        
    //     // Get relative closeness and results
    //     $results = $topsis->calculate();
        
    //     // Get alternatives (content titles)
    //     $alternatives = Content::all()->pluck('title')->toArray();
        
    //     return view('content.topsis', [
    //         'divisors' => $topsis->getNormalizationDivisors(),
    //         'normalizedMatrix' => array_combine($alternatives, $normalizedMatrix),
    //         'weightedMatrix' => $weightedMatrix,
    //         'alternatives' => $alternatives,
    //         'criteria' => ['Likes', 'Comments', 'Views'],
    //         'idealPositive' => $idealPositive,
    //         'idealNegative' => $idealNegative,
    //         'distancePositive' => $distancePositive,
    //         'distanceNegative' => $distanceNegative,
    //         'results' => array_map(function($alt, $score) {
    //             return ['alternative' => $alt, 'score' => $score];
    //         }, $alternatives, array_column($results, 'score'))
    //     ]);
    // }


public function topsis()
{
    //ini juga buat yang topsis, buat nampilin pehitungann nya, step stepnya sama kaya yang tadi
    $contents = \App\Models\Content::orderBy('created_at', 'desc')->get();

    if ($contents->isEmpty()) {
        return back()->with('error', 'Tidak ada data konten untuk dianalisis.');
    }

    $criteria = ['likes', 'comments', 'views'];
    $weights = [
        'likes' => 3.5,
        'comments' => 2.5, // cost
        'views' => 4.0,
    ];

    // 1. Ambil alternatif & matriks keputusan
    $alternatives = $contents->pluck('title')->toArray();
    $decisionMatrixRaw = [];
    foreach ($contents as $content) {
        $decisionMatrixRaw[] = [
            'likes' => $content->likes,
            'comments' => $content->comments,
            'views' => $content->views,
        ];
    }

    // Buat matriks keputusan terasosiasi dengan nama konten
    $decisionMatrix = collect($decisionMatrixRaw)
        ->mapWithKeys(fn($row, $i) => [$alternatives[$i] => $row])
        ->toArray();

    // 2. Pembagi normalisasi (divisors)
    $divisors = [];
    foreach ($criteria as $key) {
        $sumSquares = array_reduce($decisionMatrixRaw, fn($carry, $row) => $carry + pow($row[$key], 2), 0);
        $divisors[$key] = sqrt($sumSquares);
    }

    // 3. Matriks normalisasi
    $normalizedMatrixRaw = [];
    foreach ($decisionMatrixRaw as $row) {
        $normRow = [];
        foreach ($criteria as $key) {
            $normRow[$key] = $row[$key] / $divisors[$key];
        }
        $normalizedMatrixRaw[] = $normRow;
    }
    $normalizedMatrix = collect($normalizedMatrixRaw)
        ->mapWithKeys(fn($row, $i) => [$alternatives[$i] => $row])
        ->toArray();

    // 4. Matriks terbobot
    $weightedMatrixRaw = [];
    foreach ($normalizedMatrixRaw as $row) {
        $weightedRow = [];
        foreach ($criteria as $key) {
            $weightedRow[$key] = round($row[$key] * $weights[$key], 4);
        }
        $weightedMatrixRaw[] = $weightedRow;
    }
    $weightedMatrix = collect($weightedMatrixRaw)
        ->mapWithKeys(fn($row, $i) => [$alternatives[$i] => $row])
        ->toArray();

    // 5. Solusi Ideal Positif dan Negatif
    $idealPositive = [];
    $idealNegative = [];
    foreach ($criteria as $key) {
        $column = array_column($weightedMatrixRaw, $key);
        $idealPositive[$key] = $key === 'comments' ? min($column) : max($column);
        $idealNegative[$key] = $key === 'comments' ? max($column) : min($column);
    }

    // 6. Jarak ke Solusi Ideal
    $distancePositiveRaw = [];
    $distanceNegativeRaw = [];
    foreach ($weightedMatrixRaw as $row) {
        $dp = 0;
        $dn = 0;
        foreach ($criteria as $key) {
            $dp += pow($row[$key] - $idealPositive[$key], 2);
            $dn += pow($row[$key] - $idealNegative[$key], 2);
        }
        $distancePositiveRaw[] = round(sqrt($dp), 4);
        $distanceNegativeRaw[] = round(sqrt($dn), 4);
    }

    $distancePositive = collect($distancePositiveRaw)
        ->mapWithKeys(fn($val, $i) => [$alternatives[$i] => $val])
        ->toArray();
    $distanceNegative = collect($distanceNegativeRaw)
        ->mapWithKeys(fn($val, $i) => [$alternatives[$i] => $val])
        ->toArray();

    // 7. Nilai preferensi (Closeness Coefficient)
    $resultsRaw = [];
    foreach ($alternatives as $i => $alt) {
        $dp = $distancePositive[$alt];
        $dn = $distanceNegative[$alt];
        $score = $dn / ($dp + $dn);
        $resultsRaw[] = [
            'alternative' => $alt,
            'score' => round($score, 4),
        ];
    }

    // 8. Urutkan berdasarkan skor tertinggi
    $results = collect($resultsRaw)->sortByDesc('score')->values();

    return view('content.topsis', [
        'criteria' => ['Likes', 'Comments', 'Views'],
        'alternatives' => $alternatives,
        'decisionMatrix' => $decisionMatrix,
        'divisors' => $divisors,
        'normalizedMatrix' => $normalizedMatrix,
        'weightedMatrix' => $weightedMatrix,
        'idealPositive' => $idealPositive,
        'idealNegative' => $idealNegative,
        'distancePositive' => $distancePositive,
        'distanceNegative' => $distanceNegative,
        'results' => $results,
    ]);
}





    /**
     * Update the specified content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $content = Content::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'views' => 'nullable|integer|min:0',
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
        ]);

        $content->update($validated);

        return redirect()->route('content.index')
            ->with('success', 'Content updated successfully');
    }

    /**
     * Remove the specified content.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully'
        ]);
    }
}
