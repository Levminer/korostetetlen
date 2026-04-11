<h2>Beérkezett üzenetek</h2>

<?php if (!empty($uzenetek_hiba)) { ?>
    <p class="uzenetek-hiba"><?= htmlspecialchars($uzenetek_hiba, ENT_QUOTES, 'UTF-8') ?></p>
<?php } else if (empty($uzenetek)) { ?>
    <p>Még nem érkezett üzenet.</p>
<?php } else { ?>
    <div class="table-wrap">
        <table class="uzenetek-table">
            <thead>
                <tr>
                    <th>Küldés ideje</th>
                    <th>Küldő</th>
                    <th>E-mail</th>
                    <th>Tárgy</th>
                    <th>Üzenet</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($uzenetek as $uzenet) { ?>
                    <tr>
                        <td><?= htmlspecialchars($uzenet['kuldes_ideje'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $uzenet['bejelentkezett'] ? htmlspecialchars($uzenet['kuldo_nev'], ENT_QUOTES, 'UTF-8') : 'Vendég' ?></td>
                        <td><?= htmlspecialchars($uzenet['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($uzenet['targy'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= nl2br(htmlspecialchars($uzenet['uzenet'], ENT_QUOTES, 'UTF-8')) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>