<section class="crud-layout">
	<div class="crud-card">
		<h2>Pizzák kezelése</h2>
		<p class="crud-sub">A pizza tábla rekordjainak létrehozása, szerkesztése és törlése.</p>

		<?php if ($crud_hiba !== '') { ?>
			<div class="crud-alert crud-alert-error"><?= htmlspecialchars($crud_hiba, ENT_QUOTES, 'UTF-8') ?></div>
		<?php } ?>

		<?php if ($crud_siker !== '') { ?>
			<div class="crud-alert crud-alert-success"><?= htmlspecialchars($crud_siker, ENT_QUOTES, 'UTF-8') ?></div>
		<?php } ?>

		<form class="crud-form" method="post" action="crud">
			<input type="hidden" name="muvelet" value="mentes">
			<input type="hidden" name="szerkesztes_mod" value="<?= $crud_szerkesztes_mod ? '1' : '0' ?>">
			<input type="hidden" name="eredeti_pk" value="<?= isset($crud_szerkesztett[$crud_pk]) ? htmlspecialchars((string)$crud_szerkesztett[$crud_pk], ENT_QUOTES, 'UTF-8') : '' ?>">

			<?php foreach ($crud_oszlopok as $mezoNev => $meta) { ?>
				<?php if (!$meta['editable']) { continue; } ?>
				<label for="crud-<?= htmlspecialchars($mezoNev, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(crudLabel($mezoNev), ENT_QUOTES, 'UTF-8') ?></label>

				<?php if ($mezoNev === 'vegetarianus') { ?>
					<?php $aktualisErtek = isset($crud_szerkesztett[$mezoNev]) ? (string)$crud_szerkesztett[$mezoNev] : ''; ?>
					<div id="crud-<?= htmlspecialchars($mezoNev, ENT_QUOTES, 'UTF-8') ?>" class="crud-radio-group" role="radiogroup" aria-label="Vegetáriánus">
						<label class="crud-radio-option">
							<input type="radio" name="<?= htmlspecialchars($mezoNev, ENT_QUOTES, 'UTF-8') ?>" value="1" <?= $aktualisErtek === '1' ? 'checked' : '' ?> required>
							Igen
						</label>
						<label class="crud-radio-option">
							<input type="radio" name="<?= htmlspecialchars($mezoNev, ENT_QUOTES, 'UTF-8') ?>" value="0" <?= $aktualisErtek === '0' || $aktualisErtek === '' ? 'checked' : '' ?>>
							Nem
						</label>
					</div>
				<?php } elseif ($meta['input'] === 'textarea') { ?>
					<textarea id="crud-<?= htmlspecialchars($mezoNev, ENT_QUOTES, 'UTF-8') ?>" name="<?= htmlspecialchars($mezoNev, ENT_QUOTES, 'UTF-8') ?>" rows="3"><?= isset($crud_szerkesztett[$mezoNev]) ? htmlspecialchars((string)$crud_szerkesztett[$mezoNev], ENT_QUOTES, 'UTF-8') : '' ?></textarea>
				<?php } else { ?>
					<input
						id="crud-<?= htmlspecialchars($mezoNev, ENT_QUOTES, 'UTF-8') ?>"
						type="<?= $meta['input'] ?>"
						name="<?= htmlspecialchars($mezoNev, ENT_QUOTES, 'UTF-8') ?>"
						value="<?= isset($crud_szerkesztett[$mezoNev]) ? htmlspecialchars((string)$crud_szerkesztett[$mezoNev], ENT_QUOTES, 'UTF-8') : '' ?>"
						<?= $meta['null'] ? '' : 'required' ?>
					>
				<?php } ?>
			<?php } ?>

			<div class="crud-actions">
				<button type="submit">Mentés</button>
			</div>
		</form>
	</div>

	<div class="crud-card">
		<h3>Pizzák listája</h3>

		<div class="crud-table-wrap">
			<table class="crud-table">
				<thead>
					<tr>
						<?php foreach ($crud_oszlopok as $mezoNev => $meta) { ?>
							<th><?= htmlspecialchars(crudLabel($mezoNev), ENT_QUOTES, 'UTF-8') ?></th>
						<?php } ?>
						<th>Műveletek</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($crud_sorok)) { ?>
						<tr>
							<td colspan="<?= count($crud_oszlopok) + 1 ?>">Nincs még rekord a pizza táblában.</td>
						</tr>
					<?php } else { ?>
						<?php foreach ($crud_sorok as $sor) { ?>
							<tr>
								<?php foreach ($crud_oszlopok as $mezoNev => $meta) { ?>
									<td>
										<?php if ($mezoNev === 'vegetarianus') { ?>
											<?= ((string)$sor[$mezoNev] === '1') ? 'Igen' : 'Nem' ?>
										<?php } else { ?>
											<?= htmlspecialchars((string)$sor[$mezoNev], ENT_QUOTES, 'UTF-8') ?>
										<?php } ?>
									</td>
								<?php } ?>
								<td>
									<div class="crud-inline">
										<form method="post" action="crud">
											<input type="hidden" name="muvelet" value="szerkesztes">
											<input type="hidden" name="<?= htmlspecialchars($crud_pk, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string)$sor[$crud_pk], ENT_QUOTES, 'UTF-8') ?>">
											<button type="submit" class="btn-edit">Szerkesztés</button>
										</form>

										<form method="post" action="crud" onsubmit="return confirm('Biztosan törölni szeretné ezt a pizza rekordot?');">
											<input type="hidden" name="muvelet" value="torles">
											<input type="hidden" name="<?= htmlspecialchars($crud_pk, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string)$sor[$crud_pk], ENT_QUOTES, 'UTF-8') ?>">
											<button type="submit" class="btn-delete">Törlés</button>
										</form>
									</div>
								</td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</section>