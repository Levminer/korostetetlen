<?php session_start(); ?>
<?php if(file_exists('./logicals/'.$keres['fajl'].'.php')) { include("./logicals/{$keres['fajl']}.php"); } ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?= $ablakcim['cim'] . ( (isset($ablakcim['mottó'])) ? ('|' . $ablakcim['mottó']) : '' ) ?></title>
	<link rel="stylesheet" href="./styles/stilus.css" type="text/css">
	<?php if(file_exists('./styles/'.$keres['fajl'].'.css')) { ?><link rel="stylesheet" href="./styles/<?= $keres['fajl']?>.css" type="text/css"><?php } ?>
</head>
<body>
	<header>
		<div class="brand-row">
			<img width="56" src="./images/<?=$fejlec['kepforras']?>" alt="<?=$fejlec['kepalt']?>">
			<div class="titles">
				<h1><?= $fejlec['cim'] ?></h1>
				<?php if (isset($fejlec['motto'])) { ?><p class="motto"><?= $fejlec['motto'] ?></p><?php } ?>
			</div>
			<?php if(isset($_SESSION['login'])) { ?><p class="user-status">Bejelentkezve: <strong><?= $_SESSION['csn']." ".$_SESSION['un']." (".$_SESSION['login'].")" ?></strong></p><?php } ?>
		</div>
		<nav>
			<ul>
				<?php foreach ($oldalak as $url => $oldal) { ?>
					<?php if(! isset($_SESSION['login']) && $oldal['menun'][0] || isset($_SESSION['login']) && $oldal['menun'][1]) { ?>
						<li<?= (($oldal == $keres) ? ' class="active"' : '') ?>>
						<a href="<?= ($url == '/') ? '.' : $url ?>">
						<?= $oldal['szoveg'] ?></a>
						</li>
					<?php } ?>
				<?php } ?>
			</ul>
		</nav>
	</header>
	<main id="wrapper">
		<div id="content">
			<?php include("./templates/pages/{$keres['fajl']}.tpl.php"); ?>
		</div>
	</main>
    <footer>
        <?php if(isset($lablec['copyright'])) { ?>&copy;&nbsp;<?= $lablec['copyright'] ?> <?php } ?>
		&nbsp;
        <?php if(isset($lablec['ceg'])) { ?><?= $lablec['ceg']; ?><?php } ?>
    </footer>
</body>
</html>
