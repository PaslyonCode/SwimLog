<?php
session_start();

// --------------------- Multilanguage ----------------------
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'ru';
$_SESSION['lang'] = $lang;
$i18n = require __DIR__ . "/lang/$lang.php";
function t($key) { global $i18n; return $i18n[$key] ?? $key; }

// --------------------- DB connect----------------------
$pdo = new PDO('mysql:host=localhost;dbname=your_db_name;charset=utf8mb4', 'your_user', 'your_pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// --------------------- Add record -------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_run'])) {
    $date = $_POST['date'];
    $distance = (int)$_POST['distance'];
    $hours = (int)$_POST['hours'];
    $minutes = (int)$_POST['minutes'];
    $seconds = (int)$_POST['seconds'];
    $description = $_POST['description'] ?: null;
    $gear = $_POST['gear'] ?? 'без лопаток';
    $location = $_POST['location'] ?? 'бассейн';

    $duration = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

    $stmt = $pdo->prepare("INSERT INTO runs (date, distance, duration, description, gear, location) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$date, $distance, $duration, $description, $gear, $location]);
    header("Location: index.php");
    exit;
}

// --------------------- Read data -----------------------
$runs = $pdo->query("SELECT * FROM runs ORDER BY date DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
$distances = array_unique(array_column($runs, 'distance'));
sort($distances);

// --------------------- Assets -----------------------------
function formatTime($seconds) {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    return sprintf("%02d:%02d:%02d", $h, $m, $s);
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= t('title') ?></title>
    <style>
        .container {
        max-width: 1100px; /* 🔹 Ограничение ширины */
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        border-radius: 6px;
    }
        body { font-family: sans-serif; margin: 20px; }
        form { margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: center; }
        th { background: #f4f4f4; }
        td[contenteditable="true"] { background: #fdfdfd; }
        .delete-btn { cursor: pointer; color: red; font-weight: bold; }
        #filters { max-width: 950px; margin: 20px auto; }
        .filter-group { margin-bottom: 12px; }
        .filter-title { font-weight: bold; margin-right: 8px; }
        .filter-buttons { display: inline-flex; flex-wrap: wrap; gap: 6px; }
        .filter-btn { padding: 6px 12px; border: 1px solid #888; border-radius: 4px; background: #f0f0f0; cursor: pointer; transition: all 0.2s; }
        .filter-btn.active { background: #3a7bd5; color: #fff; border-color: #2a5ca0; }
        .filter-btn:hover { background: #ddd; }
        footer {
        margin-top: 30px;
        padding-top: 10px;
        border-top: 1px solid #ddd;
        text-align: center;
        font-size: 14px;
        color: #555;
    }
    footer a {
        color: #3a7bd5;
        text-decoration: none;
        font-weight: bold;
    }
    footer a:hover {
        text-decoration: underline;
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const translations = <?= json_encode($i18n, JSON_UNESCAPED_UNICODE) ?>;
    </script>
</head>
<body>
<div class="container">
<div style="text-align:right; margin-bottom:10px;">
    <a href="?lang=ru">🇷🇺 Русский</a> | 
    <a href="?lang=en">🇬🇧 English</a>
</div>

<h2><?= t('training_form') ?></h2>
<form method="post">
    <label><?= t('date') ?>: <input type="date" name="date" required></label>
    <label><?= t('distance') ?>: <input type="number" name="distance" required></label>
    <label><?= t('time') ?>:
        <input type="number" name="hours" min="0" max="23" value="0"> :
        <input type="number" name="minutes" min="0" max="59" value="0"> :
        <input type="number" name="seconds" min="0" max="59" value="0">
    </label>
    <label><?= t('description') ?>: <input type="text" name="description"></label>
    <label><?= t('gear') ?>:
        <select name="gear">
            <option value="без лопаток"><?= t('without_paddles') ?></option>
            <option value="в лопатках"><?= t('with_paddles') ?></option>
        </select>
    </label>
    <label><?= t('location') ?>:
        <select name="location">
            <option value="бассейн"><?= t('pool') ?></option>
            <option value="море"><?= t('sea') ?></option>
        </select>
    </label>
    <button type="submit" name="add_run"><?= t('add') ?></button>
</form>

<div id="filters">
    <div class="filter-group">
        <span class="filter-title"><?= t('distance') ?>:</span>
        <div class="filter-buttons" id="filter-distance">
            <?php foreach ($distances as $d): ?>
                <div class="filter-btn" data-value="<?= $d ?>"><?= $d ?>м</div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="filter-group">
        <span class="filter-title"><?= t('gear') ?>:</span>
        <div class="filter-buttons" id="filter-gear">
            <div class="filter-btn" data-value="без лопаток"><?= t('without_paddles') ?></div>
            <div class="filter-btn" data-value="в лопатках"><?= t('with_paddles') ?></div>
        </div>
    </div>
    <div class="filter-group">
        <span class="filter-title"><?= t('location') ?>:</span>
        <div class="filter-buttons" id="filter-location">
            <div class="filter-btn" data-value="бассейн"><?= t('pool') ?></div>
            <div class="filter-btn" data-value="море"><?= t('sea') ?></div>
        </div>
    </div>
</div>

<canvas id="paceChart" height="120"></canvas>

<table>
    <thead>
    <tr>
        <th><?= t('date') ?></th>
        <th><?= t('distance') ?></th>
        <th><?= t('time') ?></th>
        <th><?= t('description') ?></th>
        <th><?= t('gear') ?></th>
        <th><?= t('location') ?></th>
        <th><?= t('pace_per_100') ?></th>
        <th><?= t('projected_2500') ?></th>
        <th><?= t('projected_3000') ?></th>
        <th><?= t('projected_5000') ?></th>
        <th><?= t('projected_10000') ?></th>
        <th><?= t('delete') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($runs as $run): 
        [$h,$m,$s] = array_map('intval', explode(':', $run['duration']));
        $totalSec = $h*3600+$m*60+$s;
        $pace = $totalSec/($run['distance']/100);
    ?>
    <tr data-id="<?= $run['id'] ?>">
        <td contenteditable="true" data-field="date"><?= htmlspecialchars($run['date']) ?></td>
        <td contenteditable="true" data-field="distance"><?= $run['distance'] ?></td>
        <td contenteditable="true" data-field="duration"><?= $run['duration'] ?></td>
        <td contenteditable="true" data-field="description"><?= htmlspecialchars($run['description'] ?? '') ?></td>
        <td>
            <select data-field="gear">
                <option value="без лопаток" <?= $run['gear']==="без лопаток"?"selected":"" ?>><?= t('without_paddles') ?></option>
                <option value="в лопатках" <?= $run['gear']==="в лопатках"?"selected":"" ?>><?= t('with_paddles') ?></option>
            </select>
        </td>
        <td>
            <select data-field="location">
                <option value="бассейн" <?= $run['location']==="бассейн"?"selected":"" ?>><?= t('pool') ?></option>
                <option value="море" <?= $run['location']==="море"?"selected":"" ?>><?= t('sea') ?></option>
            </select>
        </td>
        <td><?= formatTime(round($pace)) ?></td>
        <td><?= formatTime(round($pace*25)) ?></td>
        <td><?= formatTime(round($pace*30)) ?></td>
        <td><?= formatTime(round($pace*50)) ?></td>
        <td><?= formatTime(round($pace*100)) ?></td>
        <td class="delete-btn">✖</td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
const runs = <?= json_encode($runs, JSON_UNESCAPED_UNICODE) ?>;

function randomColor(){
    return `hsl(${Math.floor(Math.random()*360)},70%,50%)`;
}

const ctx = document.getElementById('paceChart').getContext('2d');
const chart = new Chart(ctx,{type:'line',data:{labels:[],datasets:[]},options:{responsive:true,plugins:{legend:{position:'bottom'}}}});

function getActiveValues(containerId) {
    return Array.from(document.querySelectorAll(`#${containerId} .filter-btn.active`))
                .map(btn => btn.dataset.value);
}
document.querySelectorAll('.filter-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{btn.classList.toggle('active');updateChart();});
});

function updateChart() {
    const dists = getActiveValues('filter-distance');
    const gears = getActiveValues('filter-gear');
    const locs  = getActiveValues('filter-location');

    const allDists = dists.length ? dists : [...new Set(runs.map(r => r.distance))];
    const allGears = gears.length ? gears : [...new Set(runs.map(r => r.gear))];
    const allLocs  = locs.length  ? locs  : [...new Set(runs.map(r => r.location))];

    const datasets = [];
    let labels = [];

    allDists.forEach(dist => {
        allGears.forEach(gear => {
            allLocs.forEach(loc => {
                const filtered = runs.filter(r => r.distance == dist && r.gear === gear && r.location === loc).reverse();
                if (!filtered.length) return;

                const localLabels = filtered.map(r => r.date);
                const data = filtered.map(r => {
                    const [h,m,s] = r.duration.split(':').map(Number);
                    const totalSec = h*3600+m*60+s;
                    return totalSec / (r.distance/100);
                });

                if (localLabels.length > labels.length) labels = localLabels;

                datasets.push({
                    label: `${dist}м / ${translations[gear==='без лопаток'?'without_paddles':'with_paddles']} / ${translations[loc==='бассейн'?'pool':'sea']}`,
                    data,
                    borderColor: randomColor(),
                    tension: 0.2,
                    fill: false
                });
            });
        });
    });

    chart.data.labels = labels;
    chart.data.datasets = datasets;
    chart.update();
}
updateChart();

// --- Inline edit ---
document.querySelectorAll('td[contenteditable=true]').forEach(td=>{
    td.addEventListener('blur',()=>{
        fetch('update.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                id:td.closest('tr').dataset.id,
                field:td.dataset.field,
                value:td.innerText.trim()
            })
        }).then(r=>r.json()).then(resp=>{
            if(!resp.success) alert(resp.error);
        });
    });
});

// --- Selects ---
document.querySelectorAll('select[data-field]').forEach(sel=>{
    sel.addEventListener('change',()=>{
        fetch('update.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                id:sel.closest('tr').dataset.id,
                field:sel.dataset.field,
                value:sel.value
            })
        }).then(r=>r.json()).then(resp=>{
            if(!resp.success) alert(resp.error);
        });
    });
});

// --- Delete rows ---
document.querySelectorAll('.delete-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
        if(!confirm("<?= t('confirm_delete') ?>")) return;
        const id=btn.closest('tr').dataset.id;
        fetch('delete.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({id})
        }).then(r=>r.json()).then(resp=>{
            if(resp.success) btn.closest('tr').remove();
            else alert(resp.error);
        });
    });
});
</script>

    <footer>
        <p>🚀 <a href="https://github.com/PaslyonCode/SwimLog" target="_blank">GitHub: PaslyonCode/SwimLog</a></p>
    </footer>
</div>
</div>
</body>
</html>

