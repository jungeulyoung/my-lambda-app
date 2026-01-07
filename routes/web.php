<?php

use Illuminate\Support\Facades\Route;
use Laravel\Octane\Facades\Octane;
use App\Models\Board;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/async-test', function () { 
    // [비동기 테스트]
    // 원래대로라면 1초 + 1초 = 2초가 걸려야 함
    // Octane의 concurrently를 쓰면 '동시에' 실행되어 약 1초 만에 끝남

    $start = microtime(true);

    [$result1, $result2] = Octane::concurrently([
        fn() => sleep(1), // 1초 대기 (가정)
        fn() => sleep(1), // 1초 대기 (가정)
    ]);

    $time = microtime(true) - $start;

    return "걸린 시간: " . round($time, 2) . "초 (비동기 처리 성공!)";
});

Route::get('/boards-simple', function () {
    $startTime = microtime(true);
    
    // ID 역순(최신순)으로 100개만 가져옴

    $boards = Board::orderBy('id', 'desc')
                   ->take(100)
                   ->get();

    $endTime = microtime(true);

    $executionTime = $endTime - $startTime;

    return response()->json([
         'execution_time' => $executionTime,
        'boards' => $boards,
       
    ]);
});

Route::get('/get-data', function() {
    $start = microtime(true);
    
    

});