<section class="kapcsolat-layout">
	<div class="kapcsolat-info">
		<h2>Kapcsolat</h2>
		<p>Polgármester: <strong>Pásztor Roland</strong></p>

		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2716.0891800565455!2d20.019128325170623!3d47.09733115951192!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47416809d9516b45%3A0x400c4290c1e4cd0!2zS8WRcsO2c3RldMOpdGxlbiwgMjc0NQ!5e0!3m2!1shu!2shu!4v1774797301428!5m2!1shu!2shu" width="600" height="380" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
		<p><a target="_blank" href="https://www.google.com/maps/place/K%C5%91r%C3%B6stet%C3%A9tlen,+2745/@47.0973312,20.0191283,17z/data=!4m16!1m9!3m8!1s0x47416809d9516b45:0x400c4290c1e4cd0!2zS8WRcsO2c3RldMOpdGxlbiwgMjc0NQ!3b1!8m2!3d47.0976011!4d20.0227627!10e5!16s%2Fm%2F043r78c!3m5!1s0x47416809d9516b45:0x400c4290c1e4cd0!8m2!3d47.0976011!4d20.0227627!16s%2Fm%2F043r78c?authuser=0&entry=ttu&g_ep=EgoyMDI2MDMyNC4wIKXMDSoASAFQAw%3D%3D">Nagyobb térkép</a></p>
	</div>

	<div class="kapcsolat-form-wrap">
		<h3>Írjon üzenetet az oldal tulajdonosának</h3>

		<?php if (!empty($kapcsolat_hibak)) { ?>
			<div class="form-alert form-alert-error">
				<strong>Hibás kitöltés:</strong>
				<ul>
					<?php foreach ($kapcsolat_hibak as $hiba) { ?>
						<li><?= htmlspecialchars($hiba, ENT_QUOTES, 'UTF-8') ?></li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>

		<?php if (!empty($kapcsolat_siker)) { ?>
			<div class="form-alert form-alert-success">
				Az üzenet mentése sikeres volt.
			</div>
		<?php } ?>

		<form id="kapcsolat-form" method="post" action="kapcsolat" novalidate>
			<label for="kapcsolat-nev">Név</label>
			<input id="kapcsolat-nev" type="text" name="nev" value="<?= htmlspecialchars($kapcsolat_adatok['nev'], ENT_QUOTES, 'UTF-8') ?>" readonly>

			<label for="kapcsolat-email">E-mail</label>
			<input id="kapcsolat-email" type="text" name="email" value="<?= htmlspecialchars($kapcsolat_adatok['email'], ENT_QUOTES, 'UTF-8') ?>">

			<label for="kapcsolat-targy">Tárgy</label>
			<input id="kapcsolat-targy" type="text" name="targy" value="<?= htmlspecialchars($kapcsolat_adatok['targy'], ENT_QUOTES, 'UTF-8') ?>">

			<label for="kapcsolat-uzenet">Üzenet</label>
			<textarea id="kapcsolat-uzenet" name="uzenet" rows="6"><?= htmlspecialchars($kapcsolat_adatok['uzenet'], ENT_QUOTES, 'UTF-8') ?></textarea>

			<button type="submit">Üzenet küldése</button>
		</form>

		<div id="kapcsolat-client-errors" class="form-alert form-alert-error" style="display: none;"></div>

		<?php if (!empty($bekuldott_uzenet)) { ?>
			<div class="bekuldott-adatok">
				<h4>Elküldött adatok</h4>
				<p><strong>Név:</strong> <?= htmlspecialchars($bekuldott_uzenet['nev'], ENT_QUOTES, 'UTF-8') ?></p>
				<p><strong>E-mail:</strong> <?= htmlspecialchars($bekuldott_uzenet['email'], ENT_QUOTES, 'UTF-8') ?></p>
				<p><strong>Tárgy:</strong> <?= htmlspecialchars($bekuldott_uzenet['targy'], ENT_QUOTES, 'UTF-8') ?></p>
				<p><strong>Üzenet:</strong><br><?= nl2br(htmlspecialchars($bekuldott_uzenet['uzenet'], ENT_QUOTES, 'UTF-8')) ?></p>
			</div>
		<?php } ?>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var form = document.getElementById('kapcsolat-form');
	var errorBox = document.getElementById('kapcsolat-client-errors');

	if (!form || !errorBox) {
		return;
	}

	form.addEventListener('submit', function (event) {
		var errors = [];
		var name = (form.nev.value || '').trim();
		var email = (form.email.value || '').trim();
		var targy = (form.targy.value || '').trim();
		var uzenet = (form.uzenet.value || '').trim();

		if (name.length < 3 || name.length > 100) {
			errors.push('A név 3 és 100 karakter között legyen.');
		}

		var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
		if (!emailRegex.test(email)) {
			errors.push('Adjon meg valós e-mail címet.');
		}

		if (targy.length < 3 || targy.length > 150) {
			errors.push('A tárgy 3 és 150 karakter között legyen.');
		}

		if (uzenet.length < 10 || uzenet.length > 2000) {
			errors.push('Az üzenet 10 és 2000 karakter között legyen.');
		}

		if (errors.length > 0) {
			event.preventDefault();
			errorBox.innerHTML = '<strong>Hiba:</strong><ul><li>' + errors.join('</li><li>') + '</li></ul>';
			errorBox.style.display = 'block';
			window.scrollTo({ top: errorBox.offsetTop - 20, behavior: 'smooth' });
			return;
		}

		errorBox.style.display = 'none';
		errorBox.innerHTML = '';
	});
});
</script>
