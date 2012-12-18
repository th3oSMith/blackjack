<?php 
	session_start();
	
	require('functions.php');
	
	$db = db_connect();
	
	if(isset($_POST['immunity_start'])){
		update_tokens($_SESSION['id'],immunity_cost($_SESSION['id'],$db),$db);
		if(is_immunized($_SESSION['id'],$db)){ // On étend son immunité s'il est déjà immmunisé
			//extend_immunity($_SESSION['id'],$db);
		}else{ // Sinon, on créé une nouvelle immunité
			immunize($_SESSION['id'],$_POST['immunity_start'],$db);
			echo("Vous avez été immunisé !");
			}
	}elseif(isset($_POST['user_cut'])){
		// TODO
		}
	elseif(isset($_POST['malus_quantity'])){
		update_tokens($_SESSION['id'],$_POST['malus_quantity'],$db);
		add_malus($_POST['level'],$_POST['malus_quantity'],$db); // TODO
		}
	?>
	<h2>Bienvenue sur la boutique Unplug !<h2>
	Venez dépenser vos jetons pour acheter de merveilleux prix !<br /><br />
	
	<h3>Immunité :</h3>
	Protégez-vous de tous les tranchages à l'heure de votre choix !
		<form method="post" action="shop.php">
		<?php
			if(is_immunized($_SESSION['id'],$db)){
				?>
			<input type="submit" value="Renouveller" />
			
		<?php }else{
			?>
			<p>Faire démarrer l'immunité à : <input type="number" name="immunity_start" min=0 max=23 step=1 />h</p>
			<input type="submit" value="Acheter" /><?php } ?>
		</form>
	
	<h3>Trancher quelqu'un :</h3>
	Débranchez quelqu'un du réseau pendant un certain temps !<br />
		<form method="post" action="php/shop.php">
			<p>Trancher instantanément : <input type="text" name="user_cut" /></p>
			<input type="submit" value="Trancher !" />
		</form>
	
	<h3>Donner des malus à un étage :</h3>
	Augmenter le quota de malus d'un étage, et tranchez-le pendant 24h !<br /><br />
		<form method="post" action="php/shop.php">
			<label for="malus_quantity">Montant de malus : </label><input type="number" name="malus_quantity" id="malus_quantity" min=10 step=10 /><br /><br />
			<label for="level"/>Etage :</label>
				<select name="level" id="level">
					<optgroup label="Rez 1">
						<option value="1.A.1">Rez I A-1</option>
						<option value="1.A.2">Rez I A-2</option>
						<option value="1.A.3">Rez I A-3</option>
						<option value="1.A.4">Rez I A-4</option>
						<option value="1.B.1">Rez I B-1</option>
						<option value="1.B.2">Rez I B-2</option>
						<option value="1.B.3">Rez I B-3</option>
						<option value="1.B.4">Rez I B-4</option>
						<option value="1.C.1">Rez I C-1</option>
						<option value="1.C.2">Rez I C-2</option>
						<option value="1.C.3">Rez I C-3</option>
						<option value="1.C.4">Rez I C-4</option>
						<option value="1.D.1">Rez I D-1</option>
						<option value="1.D.2">Rez I D-2</option>
						<option value="1.D.3">Rez I D-3</option>
						<option value="1.D.4">Rez I D-4</option>
					<optgroup label="Rez 2">
						<option value="2.AG.0">Rez II AG0</option>
						<option value="2.AG.1">Rez II AG1</option>
						<option value="2.AG.2">Rez II AG2</option>
						<option value="2.AG.3">Rez II AG3</option>
						<option value="2.AD.0">Rez II AD0</option>
						<option value="2.AD.1">Rez II AD1</option>
						<option value="2.AD.2">Rez II AD2</option>
						<option value="2.AD.3">Rez II AD3</option>
						<option value="2.BG.0">Rez II BG0</option>
						<option value="2.BG.1">Rez II BG1</option>
						<option value="2.BG.2">Rez II BG2</option>
						<option value="2.BG.3">Rez II BG3</option>
						<option value="2.BD.0">Rez II BD0</option>
						<option value="2.BD.1">Rez II BD1</option>
						<option value="2.BD.2">Rez II BD2</option>
						<option value="2.BD.3">Rez II BD3</option>
						<option value="2.CG.0">Rez II CG0</option>
						<option value="2.CG.1">Rez II CG1</option>
						<option value="2.CG.2">Rez II CG2</option>
						<option value="2.CG.3">Rez II CG3</option>
						<option value="2.CD.0">Rez II CD0</option>
						<option value="2.CD.1">Rez II CD1</option>
						<option value="2.CD.2">Rez II CD2</option>
						<option value="2.CD.3">Rez II CD3</option>
						<option value="2.D.0">Rez II D0</option>
						<option value="2.D.1">Rez II D1</option>
						<option value="2.D.2">Rez II D2</option>
						<option value="2.D.3">Rez II D3</option>
				</select><br /><br />
			<input type="submit" value="Verser un malus !" />
		</form>