<?php
    require_once('database.php');

    $isDeleteRequest = $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'delete';

    if($isDeleteRequest) {
        $id = $_POST['id'] ?? null;

        $sql = 'DELETE FROM players WHERE id = :id';
        $stmt = $pdo->prepare($sql);

        $param = ['id' => $id];
        $stmt->execute($param);

        header('Location: main.php');
        exit;
    }
    else {
        header('Location: main.php');
        exit;
    }
?>