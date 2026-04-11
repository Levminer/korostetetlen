<?php
$uzenetek_hiba = null;
$uzenetek = array();

if (!isset($_SESSION['login'])) {
    $uzenetek_hiba = 'Az üzenetek megtekintéséhez be kell jelentkeznie.';
} else {
    try {
        require_once __DIR__ . '/../includes/db.inc.php';
        $dbh = getDbConnection();

        $sth = $dbh->prepare('SELECT kuldo_nev, email, targy, uzenet, bejelentkezett, kuldes_ideje FROM kapcsolat_uzenetek ORDER BY kuldes_ideje DESC, id DESC');
        $sth->execute();
        $uzenetek = $sth->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $uzenetek_hiba = 'Adatbázis hiba történt: ' . $e->getMessage();
    }
}
?>