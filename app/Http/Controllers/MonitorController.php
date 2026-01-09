<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonitorController extends Controller
{
    // 대시보드 화면 렌더링
    public function index()
    {
        return view('dashboard');
    }

    // 실시간 서버 상태 반환 (JSON)
    public function stats()
    {
        // 1. 메모리 사용량 계산 (Linux 'free' 명령어 파싱)
        // 실제 운영 환경에서는 /proc/meminfo를 읽거나 전용 패키지를 쓰는 것이 좋습니다.
        $free = shell_exec('free -m');
        $free = (string)trim($free);
        $freeArr = explode("\n", $free);
        $mem = explode(" ", $freeArr[1]);
        $mem = array_values(array_filter($mem)); // 빈 배열 제거 및 인덱스 재정렬
        
        // $mem[1]: 전체, $mem[2]: 사용중
        $totalMem = $mem[1];
        $usedMem = $mem[2];
        $memoryPercentage = round(($usedMem / $totalMem) * 100, 2);

        // 2. CPU Load (1분 평균 부하)
        // 윈도우에서는 작동하지 않을 수 있습니다 (리눅스 전용)
        $cpuLoad = sys_getloadavg(); 
        $cpu = $cpuLoad[0]; 

        // 3. 디스크 사용량 (현재 파티션)
        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsed = $diskTotal - $diskFree;
        $diskPercentage = round(($diskUsed / $diskTotal) * 100, 2);

        return response()->json([
            'cpu' => $cpu,
            'memory' => $memoryPercentage,
            'disk' => $diskPercentage,
            'timestamp' => now()->format('H:i:s'), // 현재 시간
        ]);
    }
}
