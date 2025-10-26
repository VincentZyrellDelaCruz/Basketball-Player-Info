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

    $errors = [];

    function file_upload() {
        $file = $_FILES['player_img'];

        if($file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';

            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $filename = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9\.\-_]/", '', $file['name']);
            $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png'];

            if(in_array($fileExtension, $allowedExtensions)) {
                $destination = $upload_dir . $filename;

                if(move_uploaded_file($file['tmp_name'], $destination)) {
                    return $filename;
                } 
                else {
                    global $errors;
                    array_push($errors, 'Failed to move uploaded file.');
                    return null;
                }

            } 
            else {
                global $errors;
                array_push($errors, 'Invalid file type. Only JPG, JPEG, and PNG are allowed.');
                return null;
            }
        }
        else if($file['error'] === UPLOAD_ERR_NO_FILE) {
            global $player;
            return $player['img_src'];
        }
        else {
            global $errors;
            array_push($errors, 'File upload error code: ' . $file['error']);
            return null;
        }
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
        $height = filter_input(INPUT_POST, 'height', FILTER_VALIDATE_INT);
        $player_no = filter_input(INPUT_POST, 'player_no', FILTER_VALIDATE_INT);

        if(($age >= 15 && $age <= 99) && ($height >= 100 && $height <= 250) && ($player_no >= 0 && $player_no <= 99)) {
            
            $name = htmlspecialchars(ucwords($_POST['name']));
            $team = htmlspecialchars(ucwords($_POST['team']));
            $position = htmlspecialchars($_POST['position']);

            $player_img = file_upload();

            if($player_img) {
                $sql = 'UPDATE players SET name = :name, age = :age, position = :position, team = :team, height = :height, player_no = :player_no, img_src = :img_src WHERE id = :id';
                $stmt = $pdo->prepare($sql);

                $params = [
                    'name' => $name,
                    'age' => $age,
                    'position' => $position,
                    'team' => $team,
                    'height' => $height,
                    'player_no' => $player_no,
                    'img_src' => $player_img,
                    'id' => $id
                ];
                $stmt->execute($params);

                header('Location: player-info.php?id=' . $id);
                exit;
            }
        }
        else {
            if(($age < 15 || $age > 99 )) {
                array_push($errors, 'Age must be between 15 and 99 years old');
            }
            if(($height < 100 || $height > 250)) {
                array_push($errors, 'Height must be between 100 and 250 cm');
            }
            if(($player_no < 0 || $player_no > 99)) {
                array_push($errors, 'Jersey No. must be between 0 and 99');
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Player</title>
    <?php require_once('bootstrap.html') ?>
</head>
<body style="background-color: #552583;">
    <!-- Navbar -->
    <?php include_once('navbar.php'); ?>

    <main>
        <div class="container my-5">
            <h1 class="text-center text-light">Edit Player</h1>
        </div>
        
        <div class="container bg-light px-5 py-3 border rounded-5 shadow-lg mb-5">

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible d-flex align-items-center fade show" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <div>
                        <?= implode(', ', $errors) ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row gy-3 my-3">
                    <div class="col-md-6 col-sm-12">
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <div class="input-group rounded-5">
                                    <label class="input-group-text bg-warning" for="name">Player Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= $player['name'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="input-group rounded-5">
                                    <label class="input-group-text bg-warning" for="age">Age</label>
                                    <input type="number" name="age" id="age" class="form-control" value="<?= $player['age'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="input-group rounded-5">
                                    <label class="input-group-text bg-warning" for="height">Height (cm)</label>
                                    <input type="number" name="height" id="height" class="form-control" value="<?= $player['height'] ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <div class="input-group rounded-5">
                                    <label class="input-group-text bg-warning" for="team">Team</label>
                                    <input type="text" name="team" id="team" class="form-control" value="<?= $player['team'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="input-group rounded-5">
                                    <label class="input-group-text bg-warning" for="name">Position</label>
                                    <select name="position" id="position" class="form-select">
                                        <option value="Point Guard" <?= ($player['team'] === 'Point Guard') ? 'selected' : '' ?>>Point Guard</option>
                                        <option value="Shooting Guard" <?= ($player['team'] === 'Shooting Guard') ? 'selected' : '' ?>>Shooting Guard</option>
                                        <option value="Small Forward" <?= ($player['team'] === 'Small Forward') ? 'selected' : '' ?>>Small Forward</option>
                                        <option value="Power Forward" <?= ($player['team'] === 'Power Forward') ? 'selected' : '' ?>>Power Forward</option>
                                        <option value="Center" <?= ($player['team'] === 'Center') ? 'selected' : '' ?>>Center</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="input-group rounded-5">
                                    <label class="input-group-text bg-warning" for="player_no">Jersey No.</label>
                                    <input type="number" name="player_no" id="player_no" class="form-control" value="<?= $player['player_no'] ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 offset-md-6 col-sm-12">
                        <?php if(!empty($player['img_src'])): ?>
                            <img src="uploads/<?= $player['img_src'] ?>" alt="player-img" class="img-thumbnail mb-3 w-25 d-block mx-auto">
                        <?php endif; ?>
                        <div class="input-group rounded-5">
                            <label class="input-group-text bg-warning" for="player_img">Player Image</label>
                            <input type="file" name="player_img" id="player_img" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <a href="player-info.php?id=<?= $player['id'] ?>" class="btn btn-secondary rounded-pill px-5 w-100">Back</a>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <input type="submit" name="submit" value="Update" class="btn btn-success rounded-pill px-5 w-100">
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>
</html>