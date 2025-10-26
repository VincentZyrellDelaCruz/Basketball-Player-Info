<?php
    require_once('database.php');

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if(!$id) {
        header('Location: main.php');
        exit;
    }

    $sql = 'SELECT * FROM players WHERE id = :id';
    $stmt = $pdo->prepare($sql);

    $param = ['id' => $id];
    $stmt->execute($param);

    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$player) {
        header('Location: main.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Info</title>
    <!-- Bootstrap -->
    <?php require_once('bootstrap.html') ?>
</head>
<body style="background-color: #552583;">
    <!-- Navbar -->
    <?php include_once('navbar.php'); ?>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" tabindex="-1" id="deleteModal" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Are you sure?</h5>
                </div>
                <div class="modal-body">
                    <p class="fs-5">Do you really want to delete this player? This process cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
            
                    <form action="delete-player.php" method="POST">
                        <input type="hidden" name="_method" value="delete">
                        <input type="hidden" name="id" value="<?= $player['id'] ?>">
                        <input type="submit" value="Delete" class="btn btn-danger rounded-pill">
                    </form>

                </div>
            </div>
        </div>
    </div>

    <main>
        <div class="container my-5">
            <div class="row g-3">
                <div class="col-md-4 col-sm-12 d-flex align-items-stretch">
                    <div class="card bg-light rounded-5 shadow-lg d-flex align-items-stretch w-100">
                        <img src="<?= $player['img_src'] ? 'uploads/' . $player['img_src'] : 'unknown.jpg'?>" class="card-img-top img-thumbnail w-75 mx-auto d-block" alt="player-img">
                        <div class="card-body d-flex flex-column">
                            <a href="edit-player.php?id=<?= $player['id'] ?>" class="btn btn-secondary rounded-pill mb-3">Edit</a>
                            <button type="button" class="btn btn-danger rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-sm-12 d-flex align-items-stretch">
                    <div class="container bg-light px-5 py-3 border rounded-5 shadow-lg w-100">
                        <div class="d-flex flex-column text-sm-center text-md-start">
                            <h1><?= $player['name'] ?? 'Unknown' ?></h1>
                            <h2 class="mb-3 text-sm-center text-md-start">#<?= $player['player_no'] ?? '0' ?></h2>
                            <h4>Age</h4>
                            <p class="fs-5 mb-3"><?= $player['age'] ?? 'N/A' ?></p>
                            <h4>Height</h4>
                            <p class="fs-5 mb-3"><?= ($player['height'] ?? 'N/A') . ' cm' ?></p>
                            <h4>Team</h4>
                            <p class="fs-5 mb-3"><?= $player['team'] ?? 'None' ?></p>
                            <h4>Position</h4>
                            <p class="fs-5 mb-3"><?= $player['position'] ?? 'N/A' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
