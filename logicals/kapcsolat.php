<?php
$kapcsolat_hibak = array();
$kapcsolat_siker = false;
$bekuldott_uzenet = null;

$kapcsolat_adatok = array(
    'nev' => 'Vendég',
    'email' => '',
    'targy' => '',
    'uzenet' => ''
);

if (isset($_SESSION['login']) && isset($_SESSION['csn']) && isset($_SESSION['un'])) {
    $kapcsolat_adatok['nev'] = trim($_SESSION['csn'] . ' ' . $_SESSION['un']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['login']) && isset($_SESSION['csn']) && isset($_SESSION['un'])) {
        $kapcsolat_adatok['nev'] = trim($_SESSION['csn'] . ' ' . $_SESSION['un']);
    } else {
        $kapcsolat_adatok['nev'] = 'Vendég';
    }
    $kapcsolat_adatok['email'] = isset($_POST['email']) ? trim($_POST['email']) : '';
    $kapcsolat_adatok['targy'] = isset($_POST['targy']) ? trim($_POST['targy']) : '';
    $kapcsolat_adatok['uzenet'] = isset($_POST['uzenet']) ? trim($_POST['uzenet']) : '';

    if (strlen($kapcsolat_adatok['nev']) < 3 || strlen($kapcsolat_adatok['nev']) > 100) {
        $kapcsolat_hibak[] = 'A név 3 és 100 karakter között legyen.';
    }

    if (!filter_var($kapcsolat_adatok['email'], FILTER_VALIDATE_EMAIL)) {
        $kapcsolat_hibak[] = 'Adjon meg érvényes e-mail címet.';
    }

    if (strlen($kapcsolat_adatok['targy']) < 3 || strlen($kapcsolat_adatok['targy']) > 150) {
        $kapcsolat_hibak[] = 'A tárgy 3 és 150 karakter között legyen.';
    }

    if (strlen($kapcsolat_adatok['uzenet']) < 10 || strlen($kapcsolat_adatok['uzenet']) > 2000) {
        $kapcsolat_hibak[] = 'Az üzenet 10 és 2000 karakter között legyen.';
    }

    if (empty($kapcsolat_hibak)) {
        try {
            require_once __DIR__ . '/../includes/db.inc.php';
            $dbh = getDbConnection();

            $bejelentkezett = isset($_SESSION['login']) ? 1 : 0;

            $stmt = $dbh->prepare(
                'INSERT INTO kapcsolat_uzenetek (kuldo_nev, email, targy, uzenet, bejelentkezett) VALUES (:nev, :email, :targy, :uzenet, :bejelentkezett)'
            );
            $stmt->execute(array(
                ':nev' => $kapcsolat_adatok['nev'],
                ':email' => $kapcsolat_adatok['email'],
                ':targy' => $kapcsolat_adatok['targy'],
                ':uzenet' => $kapcsolat_adatok['uzenet'],
                ':bejelentkezett' => $bejelentkezett
            ));

            $kapcsolat_siker = true;
            $bekuldott_uzenet = $kapcsolat_adatok;

            if (!isset($_SESSION['login'])) {
                $kapcsolat_adatok['nev'] = 'Vendég';
            }
            $kapcsolat_adatok['email'] = '';
            $kapcsolat_adatok['targy'] = '';
            $kapcsolat_adatok['uzenet'] = '';
        } catch (PDOException $e) {
            $kapcsolat_hibak[] = 'Adatbázis hiba történt: ' . $e->getMessage();
        }
    }
}
?>