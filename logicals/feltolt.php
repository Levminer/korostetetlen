<?php
include(__DIR__ . '/../templates/pages/config.inc.php');

if (!isset($_SESSION['login'])) {
    header('Location: belepes');
    exit;
}

function betoltFeltoltesMeta($metaFajl) {
    if (!is_file($metaFajl)) {
        return array();
    }

    $tartalom = file_get_contents($metaFajl);
    if ($tartalom === false || trim($tartalom) === '') {
        return array();
    }

    $adat = json_decode($tartalom, true);
    return is_array($adat) ? $adat : array();
}

function mentFeltoltesMeta($metaFajl, $adat) {
    $json = json_encode($adat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json !== false) {
        file_put_contents($metaFajl, $json, LOCK_EX);
    }
}

$uzenet = array();
$feltoltesMeta = betoltFeltoltesMeta($METAFAJL);

if (isset($_POST['kuld'])) {
    if (!isset($_FILES['kep'])) {
        $uzenet[] = 'Nem érkezett fájl a feltöltéshez.';
    } else {
        $fajl = $_FILES['kep'];

        if ($fajl['error'] == 4) {
            $uzenet[] = 'Válasszon ki egy képet a feltöltéshez.';
        } elseif (!in_array($fajl['type'], $MEDIATIPUSOK)) {
            $uzenet[] = 'Nem megfelelő típus: ' . $fajl['name'];
        } elseif ($fajl['error'] == 1 || $fajl['error'] == 2) {
            $uzenet[] = 'Túl nagy állomány: ' . $fajl['name'];
        } else {
            $taroltNev = strtolower(basename($fajl['name']));
            $vegsohely = $MAPPA . $taroltNev;
            if (file_exists($vegsohely)) {
                $uzenet[] = 'Már létezik: ' . $taroltNev;
            } elseif (move_uploaded_file($fajl['tmp_name'], $vegsohely)) {
                $feltoltoNev = trim($_SESSION['csn'] . ' ' . $_SESSION['un']);
                if ($feltoltoNev === '') {
                    $feltoltoNev = $_SESSION['login'];
                }

                $feltoltesMeta[$taroltNev] = array(
                    'feltolto' => $feltoltoNev,
                    'login' => $_SESSION['login'],
                    'idopont' => date('c')
                );
                mentFeltoltesMeta($METAFAJL, $feltoltesMeta);

                $uzenet[] = 'Sikeres feltöltés: ' . $taroltNev;
            } else {
                $uzenet[] = 'Sikertelen feltöltés: ' . $taroltNev;
            }
        }
    }
}
?>