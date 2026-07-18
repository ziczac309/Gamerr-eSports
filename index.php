<?php
// index.php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch upcoming tournaments
$tournamentsStmt = $pdo->query("SELECT * FROM tournaments WHERE status = 'Upcoming' ORDER BY match_time ASC");
$tournaments = $tournamentsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>GAMER ESPORTS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            user-select: none; 
            -webkit-user-select: none; 
            -webkit-touch-callout: none;
        }
        .banner-slide { display: none; }
        .banner-slide.active { display: block; }
    </style>
</head>
<body class="bg-gray-900 text-white pb-20" oncontextmenu="return false;">

    <div class="flex justify-between items-center p-5">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center">
                <i class="fa-solid fa-user text-xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm">Hi,</p>
                <h2 class="font-bold text-lg"><?= htmlspecialchars($user['username']) ?></h2>
            </div>
        </div>
        <a href="profile.php" class="text-gray-400 text-2xl"><i class="fa-solid fa-gear"></i></a>
    </div>

    <div class="px-5 mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-purple-900 p-6 rounded-[25px] shadow-lg shadow-purple-900/50">
            <p class="text-purple-200 text-sm mb-1">Total Wallet Balance</p>
            <h1 class="text-4xl font-bold">₹ <?= number_format($user['wallet_balance'], 2) ?></h1>
        </div>
    </div>

    <div class="flex justify-between px-6 mb-8 text-center text-sm text-gray-300">
        <a href="wallet.php" class="flex flex-col items-center gap-2">
            <div class="w-14 h-14 bg-gray-800 rounded-full flex items-center justify-center text-purple-400 text-xl shadow-inner"><i class="fa-solid fa-plus"></i></div>
            <span>Add Money</span>
        </a>
        <a href="#tournaments" class="flex flex-col items-center gap-2">
            <div class="w-14 h-14 bg-gray-800 rounded-full flex items-center justify-center text-purple-400 text-xl shadow-inner"><i class="fa-solid fa-gamepad"></i></div>
            <span>Join</span>
        </a>
        <a href="wallet.php" class="flex flex-col items-center gap-2">
            <div class="w-14 h-14 bg-gray-800 rounded-full flex items-center justify-center text-purple-400 text-xl shadow-inner"><i class="fa-solid fa-money-bill-transfer"></i></div>
            <span>Withdraw</span>
        </a>
        <a href="private_match.php" class="flex flex-col items-center gap-2">
            <div class="w-14 h-14 bg-gray-800 rounded-full flex items-center justify-center text-purple-400 text-xl shadow-inner"><i class="fa-solid fa-lock"></i></div>
            <span>Private</span>
        </a>
    </div>

    <div class="px-5 mb-8">
        <div class="relative w-full h-40 bg-gray-800 rounded-[25px] overflow-hidden">
            <div class="banner-slide active w-full h-full bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-center flex-col">
                <h2 class="text-2xl font-bold">BGMI Night Tournament</h2>
                <p>₹10 Entry → ₹100 Prize Pool</p>
            </div>
            <div class="banner-slide w-full h-full bg-gradient-to-r from-red-600 to-orange-600 flex items-center justify-center flex-col">
                <h2 class="text-2xl font-bold">Free Fire Custom Room</h2>
                <p>Join Now and Win Big!</p>
            </div>
        </div>
    </div>

    <div class="px-5" id="tournaments">
        <h3 class="text-xl font-bold mb-4 border-l-4 border-purple-500 pl-3">Upcoming Matches</h3>
        
        <?php foreach($tournaments as $t): ?>
        <div class="bg-gray-800 rounded-[25px] p-5 mb-4 shadow-md">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h4 class="font-bold text-lg"><?= htmlspecialchars($t['title']) ?></h4>
                    <p class="text-gray-400 text-sm"><?= htmlspecialchars($t['game_name']) ?></p>
                </div>
                <div class="bg-purple-900/50 text-purple-300 px-3 py-1 rounded-full text-xs font-bold">
                    <?= date('d M, h:i A', strtotime($t['match_time'])) ?>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                <div>
                    <p class="text-gray-400">Entry Fee</p>
                    <p class="font-bold text-green-400">₹ <?= $t['entry_fee'] ?></p>
                </div>
                <div>
                    <p class="text-gray-400">Prize Pool</p>
                    <p class="font-bold text-yellow-400">₹ <?= $t['prize_pool'] ?></p>
                </div>
            </div>

            <form method="POST" action="join_tournament.php">
                <input type="hidden" name="tournament_id" value="<?= $t['id'] ?>">
                <button type="submit" class="w-full bg-purple-600 py-3 rounded-xl font-bold hover:bg-purple-500 transition">Join Tournament</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        let slides = document.querySelectorAll('.banner-slide');
        let currentSlide = 0;
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 3000);
    </script>
</body>
</html>
