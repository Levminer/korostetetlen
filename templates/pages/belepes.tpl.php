<section class="auth-shell">
    <div class="auth-hero">
        <p class="auth-kicker">Üdv újra!</p>
        <h2>Belépés és gyors regisztráció egy helyen</h2>
        <p>Jelentkezzen be a meglévő fiókjába, vagy hozzon létre új felhasználót néhány másodperc alatt.</p>
    </div>

    <div class="auth-grid">
        <article class="auth-card">
            <h3>Belépés</h3>
            <p class="auth-subtitle">Ha már van fiókja, itt tud belépni.</p>

            <form class="auth-form" action="belep" method="post">
                <label for="login-felhasznalo">Felhasználónév</label>
                <input id="login-felhasznalo" type="text" name="felhasznalo" placeholder="pl. kovacsbela" required>

                <label for="login-jelszo">Jelszó</label>
                <input id="login-jelszo" type="password" name="jelszo" placeholder="Jelszó" required>

                <button class="auth-btn auth-btn-primary" type="submit" name="belepes">Belépés</button>
            </form>
        </article>

        <article class="auth-card auth-card-register">
            <h3>Regisztráció</h3>
            <p class="auth-subtitle">Nincs még fiókja? Hozza létre most.</p>

            <form class="auth-form" action="regisztral" method="post">
                <label for="reg-vezeteknev">Vezetéknév</label>
                <input id="reg-vezeteknev" type="text" name="vezeteknev" placeholder="Kovács" required>

                <label for="reg-utonev">Utónév</label>
                <input id="reg-utonev" type="text" name="utonev" placeholder="Béla" required>

                <label for="reg-felhasznalo">Felhasználónév</label>
                <input id="reg-felhasznalo" type="text" name="felhasznalo" placeholder="pl. kovacsbela" required>

                <label for="reg-jelszo">Jelszó</label>
                <input id="reg-jelszo" type="password" name="jelszo" placeholder="Legalább 1 erős jelszó" required>

                <button class="auth-btn auth-btn-secondary" type="submit" name="regisztracio">Regisztráció</button>
            </form>
        </article>
    </div>
</section>
