<section class="upload-page">
    <div class="upload-header">
        <h1>Képfeltöltés</h1>
        <p>Töltsön fel egy képet, majd ismételje meg a műveletet a következő fájlhoz.</p>
    </div>

    <?php if (!empty($uzenet)) { ?>
        <ul class="upload-messages">
            <?php foreach ($uzenet as $u) { ?>
                <li><?= htmlspecialchars($u, ENT_QUOTES, 'UTF-8') ?></li>
            <?php } ?>
        </ul>
    <?php } ?>

    <form class="upload-card" action="feltolt" method="post" enctype="multipart/form-data">
        <label for="kep">Kép kiválasztása</label>
        <input id="kep" type="file" name="kep" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>

        <div class="upload-actions">
            <input type="submit" name="kuld" value="Feltöltés">
            <a href="kepek">Vissza a galériához</a>
        </div>
    </form>
</section>