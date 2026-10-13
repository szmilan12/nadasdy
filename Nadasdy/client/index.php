<?php
require_once '../server/db/conn.php';

$submitted = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teljes_nev = trim($_POST['teljes_nev'] ?? '');
    $elozo_iskola = trim($_POST['elozo_iskola'] ?? '');
    $majors = $_POST['majors'] ?? [];
    $koli = isset($_POST['koli']) ? intval($_POST['koli']) : 0;
    $vegzos = isset($_POST['vegzos']) ? intval($_POST['vegzos']) : 0;

    if (!empty($teljes_nev) && !empty($elozo_iskola) && !empty($majors)) {
        try {
            $db->beginTransaction();

            // 1. Beszúrás a regisztraciok táblába
            $stmt = $db->prepare("INSERT INTO regisztraciok (teljes_nev, elozo_iskola, koli, vegzos) VALUES (?, ?, ?, ?)");
            $stmt->execute([$teljes_nev, $elozo_iskola, $koli, $vegzos]);
            
            $regisztracio_id = $db->lastInsertId();

            // 2. Beszúrás a regisztraciok_szakma táblába
            $stmtSzakma = $db->prepare("INSERT INTO regisztraciok_szakma (regisztracio_id, szakma_neve) VALUES (?, ?)");
            foreach ($majors as $szakma) {
                $stmtSzakma->execute([$regisztracio_id, $szakma]);
            }

            $db->commit();
            $submitted = true;
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Hiba történt a mentés során: ' . $e->getMessage();
        }
    } else {
        $error = 'Kérlek, tölts ki minden mezőt és válassz ki legalább egy szakmát!';
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nyílt Nap Regisztráció | NTSZKI Csepreg</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="src/css/style.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between">

    <div class="bg-image-blur"></div>
    <div class="bg-overlay"></div>

    <header class="w-full bg-slate-900/85 backdrop-blur-md border-b border-slate-800 py-4 px-6 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3.5">
                <div class="bg-blue-600 text-white p-2.5 rounded-xl font-bold shadow-lg shadow-blue-600/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-extrabold text-white tracking-wide">NTSZKI Csepreg</h1>
                    <p class="text-xs text-blue-400 font-medium">Vas Vármegyei SZC Nádasdy Tamás Technikum és Kollégium</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="admin/index.php" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-lg border border-slate-700 transition">Admin belépés</a>
                <span class="hidden sm:inline-block text-xs bg-blue-500/10 text-blue-300 px-3 py-1.5 rounded-lg border border-blue-500/20 font-medium">OM: 203062</span>
            </div>
        </div>
    </header>

    <main class="max-w-2xl w-full mx-auto px-4 sm:px-6 py-10 my-auto">
        <div class="glass-card border border-slate-700/60 rounded-3xl shadow-2xl overflow-hidden p-6 sm:p-10">
            
            <div class="text-center mb-8">
                <span class="inline-block bg-blue-500/10 text-blue-400 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full border border-blue-500/20 mb-3">
                    ✨ Nyílt Napi Jelentkezés
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">Válaszd a jövőt a Nádasdyban!</h2>
                <p class="text-slate-400 text-sm sm:text-base mt-2">Töltsd ki az alábbi űrlapot, és látogass el hozzánk a nyílt napokon.</p>
            </div>

            <?php if ($submitted): ?>
                <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 p-8 rounded-2xl text-center space-y-4">
                    <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-2xl text-white">Sikeres regisztráció!</h3>
                        <p class="text-sm text-emerald-400/90 mt-1">Köszönjük jelentkezésedet. Hamarosan találkozunk Csepregen!</p>
                    </div>
                    <div class="pt-2">
                        <a href="index.php" class="inline-block bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-6 py-3.5 rounded-xl transition shadow-lg shadow-emerald-600/20 text-base">
                            Új jelentkezés rögzítése
                        </a>
                    </div>
                </div>
            <?php else: ?>

                <?php if ($error): ?>
                    <div class="bg-rose-500/10 border border-rose-500/30 text-rose-300 p-4 rounded-xl text-sm mb-6">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form action="index.php" method="POST" class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Teljes neved <span class="text-blue-400">*</span></label>
                        <input type="text" name="teljes_nev" required placeholder="pl. Kovács Anna" 
                            class="w-full bg-slate-900/60 border border-slate-700/80 rounded-2xl px-4 py-4 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition text-base">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Jelenlegi iskolád <span class="text-blue-400">*</span></label>
                        <input type="text" name="elozo_iskola" required placeholder="pl. Csepregi Általános Iskola és Alapfokú Művészeti Iskola" 
                            class="w-full bg-slate-900/60 border border-slate-700/80 rounded-2xl px-4 py-4 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition text-base">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Melyik szakma/képzés érdekel? <span class="text-xs text-blue-400 font-normal">(Többet is kijelölhetsz)</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <?php 
                            $majors = [
                                'CNC-programozó (Gépészet)',
                                'Cukrász (Turizmus-vendéglátás)',
                                'Mechatronikai technikus (Specializált gép- és járműgyártás)',
                                'Mechatronikus karbantartó (Specializált gép- és járműgyártás)',
                                'Pénzügyi-számviteli ügyintéző (Gazdálkodás és menedzsment)',
                                'Pincér - vendégtéri szakember (Turizmus-vendéglátás)',
                                'Szakács (Turizmus-vendéglátás)',
                                'Szerszám- és készülékgyártó (Gépészet)',
                                'Szoftverfejlesztő és -tesztelő (Informatika és távközlés)',
                                'Turisztikai technikus (Turizmus-vendéglátás)'
                            ];
                            foreach($majors as $major): 
                            ?>
                            <!-- Javítva: items-start és mt-0.5 a checkboxon, hogy igazodjon a szöveg első sorához -->
                            <label class="flex items-start space-x-3.5 p-4 rounded-2xl border border-slate-700/80 bg-slate-900/40 hover:bg-slate-700/40 cursor-pointer transition select-none group">
                                <input type="checkbox" name="majors[]" value="<?= $major ?>" class="w-5 h-5 mt-0.5 rounded-lg border-slate-700 text-blue-600 focus:ring-blue-500 bg-slate-900 group-hover:border-blue-400 transition flex-shrink-0">
                                <span class="text-sm text-slate-200 font-medium leading-relaxed"><?= $major ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Lakhatási igény <span class="text-blue-400">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center p-4 rounded-2xl border border-slate-700/80 bg-slate-900/40 hover:bg-slate-700/40 cursor-pointer transition text-center select-none">
                                    <input type="radio" name="koli" value="1" required class="text-blue-600 focus:ring-blue-500 mr-2">
                                    <span class="text-sm text-slate-200 font-medium">Koleszos</span>
                                </label>
                                <label class="flex items-center justify-center p-4 rounded-2xl border border-slate-700/80 bg-slate-900/40 hover:bg-slate-700/40 cursor-pointer transition text-center select-none">
                                    <input type="radio" name="koli" value="0" required class="text-blue-600 focus:ring-blue-500 mr-2">
                                    <span class="text-sm text-slate-200 font-medium">Bejárós</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Végzős vagy idén? <span class="text-blue-400">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center p-4 rounded-2xl border border-slate-700/80 bg-slate-900/40 hover:bg-slate-700/40 cursor-pointer transition text-center select-none">
                                    <input type="radio" name="vegzos" value="1" required class="text-blue-600 focus:ring-blue-500 mr-2">
                                    <span class="text-sm text-slate-200 font-medium">Igen</span>
                                </label>
                                <label class="flex items-center justify-center p-4 rounded-2xl border border-slate-700/80 bg-slate-900/40 hover:bg-slate-700/40 cursor-pointer transition text-center select-none">
                                    <input type="radio" name="vegzos" value="0" required class="text-blue-600 focus:ring-blue-500 mr-2">
                                    <span class="text-sm text-slate-200 font-medium">Nem</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4.5 px-8 rounded-2xl shadow-xl shadow-blue-600/30 transition duration-200 text-base flex items-center justify-center space-x-3">
                            <span>Jelentkezés elküldése</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <footer class="text-center py-6 text-slate-500 text-xs border-t border-slate-800/80 mt-10 space-y-1 z-10">
        <p>&copy; 2026 Vas Vármegyei SZC Nádasdy Tamás Technikum és Kollégium</p>
        <p>9735 Csepreg, Rákóczi u. 13-15. &bull; Tel: +36 94 565 040</p>
    </footer>

    <script src="src/js/script.js"></script>
</body>
</html>