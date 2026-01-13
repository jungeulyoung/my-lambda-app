<?php

use Illuminate\Support\Facades\Route;
use Laravel\Octane\Facades\Octane;
use App\Models\Board;
use App\Http\Controllers\MonitorController;

use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('login');

Route::post('/login', [App\Http\Controllers\LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');


Route::get('/register', [App\Http\Controllers\RegisterController::class, 'create'])->name('register');
Route::post('/register', [App\Http\Controllers\RegisterController::class, 'store'])->name('register.post');



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

// 로그인한 사용자만 접근하도록 middleware 설정 (auth)
Route::middleware(['auth'])->group(function () {
    // 1. 대시보드 화면
    Route::get('/dashboard', [MonitorController::class, 'index'])->name('dashboard');
    
    // 2. 실시간 데이터 반환 API (Octane의 속도를 보여줄 핵심 API)
    Route::get('/api/server-stats', [MonitorController::class, 'stats'])->name('api.stats');
});