<?php
    include(__DIR__ . '/config.inc.php');

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

    $feltoltesMeta = betoltFeltoltesMeta($METAFAJL);

    $uzenet = '';
    if (isset($_POST['torol']) && isset($_POST['fajl'])) {
        if (!isset($_SESSION['login'])) {
            $uzenet = 'Törléshez be kell jelentkezni.';
        } else {
            $torlendo = basename($_POST['fajl']);
            $vege = strtolower(substr($torlendo, strlen($torlendo) - 4));
            $mapparoot = realpath($MAPPA);
            $fajlroot = realpath($MAPPA . $torlendo);

            if ($mapparoot !== false && $fajlroot !== false && strpos($fajlroot, $mapparoot) === 0 && is_file($fajlroot) && in_array($vege, $TIPUSOK)) {
                if (unlink($fajlroot)) {
                    if (isset($feltoltesMeta[$torlendo])) {
                        unset($feltoltesMeta[$torlendo]);
                        mentFeltoltesMeta($METAFAJL, $feltoltesMeta);
                    }
                    $uzenet = 'A kép törölve: ' . $torlendo;
                } else {
                    $uzenet = 'A kép törlése sikertelen: ' . $torlendo;
                }
            } else {
                $uzenet = 'Érvénytelen fájlnév vagy a fájl nem létezik.';
            }
        }
    }

    $kepek = array();
    $olvaso = opendir($MAPPA);
    while (($fajl = readdir($olvaso)) !== false) {
        if (is_file($MAPPA . $fajl)) {
            $vege = strtolower(substr($fajl, strlen($fajl) - 4));
            if (in_array($vege, $TIPUSOK)) {
                $kepek[$fajl] = filemtime($MAPPA . $fajl);
            }
        }
    }
    closedir($olvaso);
?>

<section class="gallery-page">
    <div class="gallery-header">
        <h1>Képgaléria</h1>
        <p>A feltöltött képek időrendben jelennek meg, a legfrissebbek elöl.</p>
        <?php if (isset($_SESSION['login'])) { ?>
            <a class="gallery-upload-link" href="feltolt">Új kép feltöltése</a>
        <?php } ?>
    </div>

    <?php if ($uzenet !== '') { ?>
        <p class="gallery-message"><?php echo htmlspecialchars($uzenet, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php } ?>

    <div class="gallery-grid">
        <?php
        arsort($kepek);
        foreach ($kepek as $fajl => $datum) {
            $feltolto = 'Ismeretlen';
            if (isset($feltoltesMeta[$fajl]['feltolto']) && $feltoltesMeta[$fajl]['feltolto'] !== '') {
                $feltolto = $feltoltesMeta[$fajl]['feltolto'];
            }
        ?>
            <article class="gallery-card">
                <a class="gallery-image-link js-lightbox-trigger" href="<?php echo $MAPPA . $fajl; ?>" data-src="<?php echo $MAPPA . $fajl; ?>" data-alt="<?php echo htmlspecialchars($fajl, ENT_QUOTES, 'UTF-8'); ?>">
                    <img src="<?php echo $MAPPA . $fajl; ?>" alt="<?php echo htmlspecialchars($fajl, ENT_QUOTES, 'UTF-8'); ?>">
                </a>
                <div class="gallery-meta">
                    <p><strong>Név:</strong> <?php echo htmlspecialchars($fajl, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Dátum:</strong> <?php echo date($DATUMFORMA, $datum); ?></p>
                    <p><strong>Feltöltötte:</strong> <?php echo htmlspecialchars($feltolto, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <?php if (isset($_SESSION['login'])) { ?>
                    <form class="torles-form" action="kepek" method="post" onsubmit="return confirm('Biztosan törölni szeretné ezt a képet?');">
                        <input type="hidden" name="fajl" value="<?php echo htmlspecialchars($fajl, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="submit" name="torol" value="Kép törlése">
                    </form>
                <?php } ?>
            </article>
        <?php } ?>
    </div>

    <div id="lightbox" class="lightbox" aria-hidden="true">
        <div class="lightbox-topbar">
            <button id="lightbox-close" type="button" class="lightbox-back" aria-label="Vissza a galériához">← Vissza</button>
        </div>
        <div class="lightbox-content">
            <img id="lightbox-image" src="" alt="">
        </div>
    </div>
</section>

<script>
(function () {
    var triggers = document.querySelectorAll('.js-lightbox-trigger');
    var lightbox = document.getElementById('lightbox');
    var lightboxImage = document.getElementById('lightbox-image');
    var closeButton = document.getElementById('lightbox-close');

    function closeLightbox() {
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        lightboxImage.src = '';
        lightboxImage.alt = '';
    }

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            lightboxImage.src = trigger.getAttribute('data-src');
            lightboxImage.alt = trigger.getAttribute('data-alt') || '';
            lightbox.classList.add('open');
            lightbox.setAttribute('aria-hidden', 'false');
        });
    });

    closeButton.addEventListener('click', closeLightbox);

    lightbox.addEventListener('click', function (event) {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && lightbox.classList.contains('open')) {
            closeLightbox();
        }
    });
})();
</script>
