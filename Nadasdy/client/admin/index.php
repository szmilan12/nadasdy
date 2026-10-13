<?php
session_start();
require_once '../../server/db/conn.php';

$adminPassword = 'nadasdy2026';
$loginError = '';

if (isset($_POST['login'])) {
    if (($_POST['password'] ?? '') === $adminPassword) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        $loginError = 'Hibás jelszó!';
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: index.php");
    exit;
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    
    // Törlés kezelése
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        
        $stmtDelSzakma = $db->prepare("DELETE FROM regisztraciok_szakma WHERE regisztracio_id = ?");
        $stmtDelSzakma->execute([$id]);

        $stmtDelReg = $db->prepare("DELETE FROM regisztraciok WHERE id = ?");
        $stmtDelReg->execute([$id]);

        header("Location: index.php");
        exit;
    }

    $stmt = $db->query("SELECT * FROM regisztraciok ORDER BY ekkor_regisztralt DESC");
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $szakmakStmt = $db->query("SELECT * FROM regisztraciok_szakma");
    $allSzakmak = $szakmakStmt->fetchAll(PDO::FETCH_ASSOC);

    $regSzakmak = [];
    foreach ($allSzakmak as $sz) {
        $regSzakmak[$sz['regisztracio_id']][] = $sz['szakma_neve'];
    }

    $totalRegs = count($registrations);
    $koliCount = 0;
    $koliVegzosCount = 0;
    $bejarosCount = 0;
    $bejarosVegzosCount = 0;
    
    $majorCounts = [];
    $majorVegzosCounts = [];

    foreach ($registrations as $reg) {
        $isKoli = ($reg['koli'] == 1);
        $isVegzos = ($reg['vegzos'] == 1);
        $regId = $reg['id'];

        if ($isKoli) {
            $koliCount++;
            if ($isVegzos) $koliVegzosCount++;
        } else {
            $bejarosCount++;
            if ($isVegzos) $bejarosVegzosCount++;
        }

        $currentSzakmak = $regSzakmak[$regId] ?? [];
        foreach ($currentSzakmak as $szakma) {
            $majorCounts[$szakma] = ($majorCounts[$szakma] ?? 0) + 1;
            if ($isVegzos) {
                $majorVegzosCounts[$szakma] = ($majorVegzosCounts[$szakma] ?? 0) + 1;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | NTSZKI Nyílt Nap</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../src/css/style.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

    <?php if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true): ?>
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="bg-slate-900 border border-slate-800 p-8 rounded-3xl shadow-2xl max-w-md w-full">
                <div class="text-center mb-6">
                    <span class="bg-blue-500/10 text-blue-400 text-xs font-bold px-3 py-1 rounded-full border border-blue-500/20">Biztonságos terület</span>
                    <h2 class="text-2xl font-bold text-white mt-3">Admin Bejelentkezés</h2>
                    <p class="text-sm text-slate-400 mt-1">NTSZKI Nyílt Nap Vezérlőpult</p>
                </div>

                <?php if ($loginError): ?>
                    <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 p-3 rounded-xl text-sm mb-4">
                        <?= $loginError ?>
                    </div>
                <?php endif; ?>

                <form action="index.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Admin Jelszó</label>
                        <input type="password" name="password" required placeholder="Add meg a jelszót (pl. nadasdy2026)" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-blue-500">
                    </div>
                    <button type="submit" name="login" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-blue-600/30">
                        Bejelentkezés
                    </button>
                </form>

                <div class="text-center mt-6">
                    <a href="../index.php" class="text-xs text-slate-400 hover:text-slate-200">&larr; Vissza a regisztrációs oldalra</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <header class="bg-slate-900 border-b border-slate-800 py-4 px-6 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 text-white p-2 rounded-lg font-bold">NTSZKI</div>
                    <h1 class="text-lg font-bold text-white">Adminisztrációs Vezérlőpult</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="../index.php" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-lg border border-slate-700 transition">Regisztrációs Form</a>
                    <a href="index.php?logout=true" class="text-xs bg-rose-600 hover:bg-rose-500 text-white px-3 py-2 rounded-lg font-medium transition shadow-md">Kilépés</a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-lg">
                    <p class="text-slate-400 text-sm font-medium">Összes jelentkezés</p>
                    <h3 class="text-4xl font-extrabold text-white mt-2"><?= $totalRegs ?></h3>
                </div>
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-lg">
                    <p class="text-slate-400 text-sm font-medium">Koleszos jelentkezők</p>
                    <h3 class="text-4xl font-extrabold text-blue-400 mt-2">
                        <?= $koliCount ?> 
                        <span class="text-xs text-slate-400 font-normal block mt-1">(ebből végzős: <?= $koliVegzosCount ?> fő)</span>
                    </h3>
                </div>
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-lg">
                    <p class="text-slate-400 text-sm font-medium">Bejárós jelentkezők</p>
                    <h3 class="text-4xl font-extrabold text-emerald-400 mt-2">
                        <?= $bejarosCount ?> 
                        <span class="text-xs text-slate-400 font-normal block mt-1">(ebből végzős: <?= $bejarosVegzosCount ?> fő)</span>
                    </h3>
                </div>
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-lg">
                    <p class="text-slate-400 text-sm font-medium">Végzősök aránya</p>
                    <h3 class="text-4xl font-extrabold text-amber-400 mt-2">
                        <?php 
                        $vegzosTotal = 0;
                        foreach($registrations as $r) { if($r['vegzos'] == 1) $vegzosTotal++; }
                        echo $totalRegs > 0 ? round(($vegzosTotal / $totalRegs) * 100) . '%' : '0%';
                        ?>
                    </h3>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-lg">
                <h3 class="text-lg font-bold text-white mb-4">Jelentkezések szakmák szerint (Összesen és Végzős bontásban)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach($majorCounts as $szakma => $count): 
                        $vegzosCount = $majorVegzosCounts[$szakma] ?? 0;
                    ?>
                        <div class="bg-slate-800/60 border border-slate-700/60 p-4 rounded-xl flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-200"><?= htmlspecialchars($szakma) ?></p>
                                <p class="text-xs text-slate-400 mt-1">Végzős: <span class="text-amber-400 font-semibold"><?= $vegzosCount ?> fő</span></p>
                            </div>
                            <div class="bg-blue-600/20 text-blue-400 font-bold px-3 py-1.5 rounded-lg border border-blue-500/30 text-sm">
                                <?= $count ?> fő
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($majorCounts)): ?>
                        <p class="text-sm text-slate-500 col-span-full">Még nincsenek szakmák rögzítve.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white">Összes regisztráció listája</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-800/50 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-800">
                                <th class="p-4">Dátum</th>
                                <th class="p-4">Név</th>
                                <th class="p-4">Iskola</th>
                                <th class="p-4">Szakma(k)</th>
                                <th class="p-4">Lakhatás</th>
                                <th class="p-4">Végzős?</th>
                                <th class="p-4 text-right">Művelet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 text-sm">
                            <?php foreach($registrations as $reg): ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="p-4 text-slate-400 whitespace-nowrap"><?= $reg['ekkor_regisztralt'] ?></td>
                                <td class="p-4 font-semibold text-white"><?= htmlspecialchars($reg['teljes_nev']) ?></td>
                                <td class="p-4 text-slate-300"><?= htmlspecialchars($reg['elozo_iskola']) ?></td>
                                <td class="p-4 text-slate-300">
                                    <?php 
                                    $szakmakList = $regSzakmak[$reg['id']] ?? [];
                                    echo implode(', ', array_map('htmlspecialchars', $szakmakList));
                                    ?>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $reg['koli'] == 1 ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-slate-800 text-slate-300 border border-slate-700' ?>">
                                        <?= $reg['koli'] == 1 ? 'Koleszos' : 'Bejárós' ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $reg['vegzos'] == 1 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-slate-800 text-slate-300 border border-slate-700' ?>">
                                        <?= $reg['vegzos'] == 1 ? 'Igen' : 'Nem' ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right whitespace-nowrap">
                                    <a href="index.php?delete=<?= $reg['id'] ?>" onclick="return confirm('Biztosan törölni szeretnéd ezt a jelentkezést?');" 
                                       class="text-rose-400 hover:text-rose-300 bg-rose-500/10 hover:bg-rose-500/20 p-2 rounded-lg transition border border-rose-500/20 inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($registrations)): ?>
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-500">Még nincsenek regisztrációk.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    <?php endif; ?>
</body>
</html>