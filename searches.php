<?php
    require_once('database.php');

    $search = htmlspecialchars($_GET['search']);

    if(!$search) {
        header('Location: main.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM players WHERE name LIKE :search');

    $param = ['search'=>"%$search%"];
    $stmt->execute($param);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VZ Basketball</title>
    <!-- Bootstrap -->
    <?php require_once('bootstrap.html') ?>
</head>
<body style="background-color: #552583;">
    <!-- Navbar -->
    <?php include_once('navbar.php'); ?>

    <main>
        <div class="container my-5">
            <h1 class="text-center text-light">Search Results for "<?= $search ?>"</h1>
        </div>

        <div class="container mb-5" id="players">
            <div class="row g-3">
                <?php if (!empty($players)): ?>
                    <?php foreach($players as $player): ?>
                        <div class="col-md-4 col-sm-6 d-flex align-items-stretch">
                            <div class="card bg-light rounded-5 shadow-lg d-flex align-items-stretch w-100">
                                <img src="<?= $player['img_src'] ? 'uploads/' . $player['img_src'] : 'unknown.jpg'?>" class="card-img-top" alt="player-img">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-center"><?= $player['name']?></h5>
                                    <p class="card-text text-center"><?= $player['team']?></p>
                                    <a href="player-info.php?id=<?= $player['id'] ?>" class="btn btn-warning rounded-pill mt-auto">Check Info</a>    
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <h2 class="text-center text-light">No players found</h2>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>