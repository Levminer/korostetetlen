<?php
$crud_hiba = '';
$crud_siker = '';
$crud_oszlopok = array();
$crud_sorok = array();
$crud_szerkesztett = array();
$crud_pk = '';
$crud_szerkesztes_mod = false;

if (!function_exists('crudTipus')) {
    function crudTipus($type)
    {
        $type = strtolower($type);
        if (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false || strpos($type, 'float') !== false || strpos($type, 'double') !== false) {
            return 'number';
        }
        if (strpos($type, 'text') !== false) {
            return 'textarea';
        }
        return 'text';
    }
}

if (!function_exists('crudSzovegesTipus')) {
    function crudSzovegesTipus($type)
    {
        $type = strtolower($type);
        return strpos($type, 'char') !== false || strpos($type, 'text') !== false;
    }
}

if (!function_exists('crudMagyarCollation')) {
    function crudMagyarCollation($meta)
    {
        $aktualis = isset($meta['collation']) ? strtolower((string)$meta['collation']) : '';

        if (strpos($aktualis, 'utf8mb4_') === 0) {
            return 'utf8mb4_hungarian_ci';
        }

        if (strpos($aktualis, 'utf8_') === 0) {
            return 'utf8_hungarian_ci';
        }

        return '';
    }
}

if (!function_exists('crudLabel')) {
    function crudLabel($nev)
    {
        $cimke = str_replace('_', ' ', $nev);
        $cimke = ucfirst($cimke);
        $cserek = array(
            'Nev' => 'Név',
            'Leiras' => 'Leírás',
            'Meret' => 'Méret',
            'Kategorianev' => 'Kategória',
            'Ar' => 'Ár',
            'Kep' => 'Kép',
            'Keszlet' => 'Készlet',
            'Vegetarianus' => 'Vegetáriánus'
        );

        return isset($cserek[$cimke]) ? $cserek[$cimke] : $cimke;
    }
}

if (!function_exists('crudAlapMezok')) {
    function crudAlapMezok($oszlopok)
    {
        $alap = array();
        foreach ($oszlopok as $mezoNev => $meta) {
            $alap[$mezoNev] = $meta['default'] !== null ? $meta['default'] : '';
        }
        return $alap;
    }
}

if (!function_exists('crudUresMezok')) {
    function crudUresMezok($oszlopok)
    {
        $ures = array();
        foreach ($oszlopok as $mezoNev => $meta) {
            $ures[$mezoNev] = '';
        }
        return $ures;
    }
}

try {
    if (!isset($_SESSION['login'])) {
        header('Location: belepes');
        exit;
    }

    require_once __DIR__ . '/../includes/db.inc.php';
    $dbh = getDbConnection();

    $tabla = '';
    $tablaJeloltek = array('pizza', 'pizzak');
    $tablaSt = $dbh->query('SHOW TABLES');
    $elerhetoTablak = $tablaSt->fetchAll(PDO::FETCH_COLUMN, 0);

    foreach ($tablaJeloltek as $jelolt) {
        foreach ($elerhetoTablak as $levoTabla) {
            if (strtolower($levoTabla) === strtolower($jelolt)) {
                $tabla = $levoTabla;
                break 2;
            }
        }
    }

    if ($tabla === '') {
        throw new RuntimeException('Nem található pizza vagy pizzak nevű tábla az adatbázisban.');
    }

    $descSt = $dbh->query("SHOW FULL COLUMNS FROM `{$tabla}`");
    $desc = $descSt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($desc)) {
        throw new RuntimeException('A pizza tábla nem található vagy üres a séma.');
    }

    foreach ($desc as $mezo) {
        if ($mezo['Key'] === 'PRI' && $crud_pk === '') {
            $crud_pk = $mezo['Field'];
        }
    }

    if ($crud_pk === '') {
        $crud_pk = $desc[0]['Field'];
    }

    foreach ($desc as $mezo) {
        $field = $mezo['Field'];
        $extra = strtolower((string)$mezo['Extra']);
        $autoIncrement = strpos($extra, 'auto_increment') !== false;

        $szerkesztheto = true;
        if ($autoIncrement) {
            $szerkesztheto = false;
        }
        if ($field === 'letrehozva' || $field === 'modositva') {
            $szerkesztheto = false;
        }

        $crud_oszlopok[$field] = array(
            'type' => $mezo['Type'],
            'null' => $mezo['Null'] === 'YES',
            'key' => $mezo['Key'],
            'default' => $mezo['Default'],
            'extra' => $mezo['Extra'],
            'collation' => isset($mezo['Collation']) ? $mezo['Collation'] : null,
            'auto_increment' => $autoIncrement,
            'input' => crudTipus($mezo['Type']),
            'editable' => $szerkesztheto
        );

        if (!isset($crud_szerkesztett[$field])) {
            $crud_szerkesztett[$field] = $mezo['Default'] !== null ? $mezo['Default'] : '';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $muvelet = isset($_POST['muvelet']) ? $_POST['muvelet'] : '';

        if ($muvelet === 'szerkesztes') {
            $id = isset($_POST[$crud_pk]) ? $_POST[$crud_pk] : '';
            $st = $dbh->prepare("SELECT * FROM `{$tabla}` WHERE `{$crud_pk}` = :id LIMIT 1");
            $st->execute(array(':id' => $id));
            $talalat = $st->fetch(PDO::FETCH_ASSOC);
            if ($talalat) {
                $crud_szerkesztett = $talalat;
                $crud_szerkesztes_mod = true;
            }
        }

        if ($muvelet === 'torles') {
            $id = isset($_POST[$crud_pk]) ? $_POST[$crud_pk] : '';
            $st = $dbh->prepare("DELETE FROM `{$tabla}` WHERE `{$crud_pk}` = :id");
            $st->execute(array(':id' => $id));
            $crud_siker = 'A pizza rekord törölve.';
        }

        if ($muvelet === 'mentes') {
            $id = isset($_POST['eredeti_pk']) ? trim((string)$_POST['eredeti_pk']) : '';
            $bejovoSzerkesztes = isset($_POST['szerkesztes_mod']) && $_POST['szerkesztes_mod'] === '1';
            $szerkesztesLetezoAzonositoval = $bejovoSzerkesztes && $id !== '' && $id !== '0';
            $adatok = array();

            foreach ($crud_oszlopok as $mezoNev => $meta) {
                if (!$meta['editable']) {
                    continue;
                }

                if ($mezoNev === 'vegetarianus') {
                    $bejovo = isset($_POST[$mezoNev]) ? trim((string)$_POST[$mezoNev]) : '';
                    if ($bejovo === '1') {
                        $ertek = '1';
                    } elseif ($bejovo === '0') {
                        $ertek = '0';
                    } else {
                        $ertek = $meta['null'] ? null : '0';
                    }
                } else {
                    $ertek = isset($_POST[$mezoNev]) ? trim((string)$_POST[$mezoNev]) : '';
                }

                if ($ertek === '' && $meta['null']) {
                    $adatok[$mezoNev] = null;
                } else {
                    $adatok[$mezoNev] = $ertek;
                }
                $crud_szerkesztett[$mezoNev] = $adatok[$mezoNev];
            }

            if ($szerkesztesLetezoAzonositoval) {
                $setek = array();
                $params = array(':pk' => $id);
                foreach ($adatok as $mezoNev => $ertek) {
                    $setek[] = "`{$mezoNev}` = :{$mezoNev}";
                    $params[":{$mezoNev}"] = $ertek;
                }
                if (!empty($setek)) {
                    $sql = "UPDATE `{$tabla}` SET " . implode(', ', $setek) . " WHERE `{$crud_pk}` = :pk";
                    $st = $dbh->prepare($sql);
                    $st->execute($params);
                    $crud_siker = 'A pizza rekord sikeresen mentve.';
                    $crud_szerkesztett = crudUresMezok($crud_oszlopok);
                    $crud_szerkesztes_mod = false;
                }
            } else {
                $mezoLista = array();
                $ertekLista = array();
                $params = array();
                foreach ($adatok as $mezoNev => $ertek) {
                    $mezoLista[] = "`{$mezoNev}`";
                    $ertekLista[] = ":{$mezoNev}";
                    $params[":{$mezoNev}"] = $ertek;
                }
                if (!empty($mezoLista)) {
                    $sql = "INSERT INTO `{$tabla}` (" . implode(', ', $mezoLista) . ") VALUES (" . implode(', ', $ertekLista) . ")";
                    $st = $dbh->prepare($sql);
                    $st->execute($params);
                    $crud_siker = 'Az új pizza rekord sikeresen létrehozva.';
                    $crud_szerkesztett = crudUresMezok($crud_oszlopok);
                    $crud_szerkesztes_mod = false;
                }
            }
        }
    }

    $rendezesMezo = '';

    if (isset($crud_oszlopok['nev']) && crudSzovegesTipus($crud_oszlopok['nev']['type'])) {
        $rendezesMezo = 'nev';
    } else {
        foreach ($crud_oszlopok as $mezoNev => $meta) {
            if (crudSzovegesTipus($meta['type'])) {
                $rendezesMezo = $mezoNev;
                break;
            }
        }
    }

    if ($rendezesMezo !== '') {
        $alapSql = "SELECT * FROM `{$tabla}` ORDER BY `{$rendezesMezo}` ASC, `{$crud_pk}` ASC";
        $kollacio = crudMagyarCollation($crud_oszlopok[$rendezesMezo]);

        if ($kollacio !== '') {
            $listSql = "SELECT * FROM `{$tabla}` ORDER BY `{$rendezesMezo}` COLLATE {$kollacio} ASC, `{$crud_pk}` ASC";
            try {
                $listSt = $dbh->query($listSql);
            } catch (Throwable $e) {
                $listSt = $dbh->query($alapSql);
            }
        } else {
            $listSt = $dbh->query($alapSql);
        }
    } else {
        $listSt = $dbh->query("SELECT * FROM `{$tabla}` ORDER BY `{$crud_pk}` ASC");
    }

    $crud_sorok = $listSt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $crud_hiba = 'CRUD hiba: ' . $e->getMessage();
}
?>