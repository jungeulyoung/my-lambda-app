<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Octane Monitor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-900 text-white font-sans">

    <nav class="bg-gray-800 p-4 border-b border-gray-700">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold text-teal-400">⚡ Octane Server Monitor</h1>

            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-400">Live Connection</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded transition">Logout</button>
                </form>  
            </div>

            <span class="text-sm text-gray-400">Live Connection</span>

        </div>
    </nav>

    <div class="container mx-auto mt-10 p-4">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg border-l-4 border-blue-500">
                <h2 class="text-gray-400 text-sm font-bold uppercase">CPU Load (1min)</h2>
                <p class="text-3xl font-bold mt-2" id="txt-cpu">0.00</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg border-l-4 border-purple-500">
                <h2 class="text-gray-400 text-sm font-bold uppercase">Memory Usage</h2>
                <p class="text-3xl font-bold mt-2"><span id="txt-memory">0</span>%</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg border-l-4 border-green-500">
                <h2 class="text-gray-400 text-sm font-bold uppercase">Disk Usage</h2>
                <p class="text-3xl font-bold mt-2"><span id="txt-disk">0</span>%</p>
            </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
            <h3 class="text-lg font-bold mb-4">Real-time Memory Traffic</h3>
            <div class="relative h-80 w-full">
                <canvas id="monitorChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('monitorChart').getContext('2d');
        
        // 차트 초기 설정
        const monitorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [], // 시간축
                datasets: [{
                    label: 'Memory Usage (%)',
                    data: [],
                    borderColor: '#a855f7', // 보라색
                    backgroundColor: 'rgba(168, 85, 247, 0.2)',
                    borderWidth: 2,
                    tension: 0.4, // 곡선 부드럽게
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 100, grid: { color: '#374151' } },
                    x: { grid: { color: '#374151' } }
                },
                animation: { duration: 0 } // 실시간 느낌을 위해 애니메이션 제거
            }
        });

        // 데이터 가져오기 함수 (1초마다 실행)
        async function fetchStats() {
            try {
                const response = await fetch('/api/server-stats');
                const data = await response.json();

                // 1. 텍스트 업데이트
                document.getElementById('txt-cpu').innerText = data.cpu;
                document.getElementById('txt-memory').innerText = data.memory;
                document.getElementById('txt-disk').innerText = data.disk;

                // 2. 차트 데이터 업데이트
                const time = data.timestamp;
                
                // 데이터 추가
                monitorChart.data.labels.push(time);
                monitorChart.data.datasets[0].data.push(data.memory);

                // 데이터가 20개 넘으면 앞부분 삭제 (차트가 계속 밀리면서 보이도록)
                if (monitorChart.data.labels.length > 20) {
                    monitorChart.data.labels.shift();
                    monitorChart.data.datasets[0].data.shift();
                }

                monitorChart.update();

            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        }

        // 1초(1000ms)마다 fetchStats 실행
        setInterval(fetchStats, 1000);
        
        // 페이지 로드 시 즉시 1회 실행
        fetchStats();
    </script>
</body>
</html>