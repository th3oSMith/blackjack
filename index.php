<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>Black Jack</title>
		<script src="jquery.min.js"></script>
		<script src="blackjack.js"></script>
    </head>

    <body>
    <header><img src="images/banner.png" alt="Bienvenue au Casino Unplug !" /></header>
    <div id="user_area">
    	<span id="user_info">
    
    <span>
    <span id="user_area_login"></span>
     - Pot :
    <span id="user_area_pot"></span>
    UT - 
    </span>
    <a href="#" onClick="logout()">Déconnexion</a>
			</span>    
    </div>
    
    <div id="content">
    <div id="connexion_div">
    
    
    
    <?php
    session_start();
    require "php/functions.php";
    
    
    
    if (!(user_verified())){?>
	<div id="speach"><p>Tu es sur le point de rentrer au casino. Le login et le mot de passe sont ceux du 3w (prénom.nom).<br/> La première connexion doit obligatoirement se faire depuis ta chambre.</p>
	<p>En te connectant à ton compte tu acceptes d'être tranché (eh oui c'est le jeu).</p>
	<p>Si jamais tu en as marre de jouer tu peux demander au rézo de supprimer ton compte.</p>
	<p>Bon Jeu.</p>
	<p>Le Jeu est optimisé pour firefox, les autres navigateurs risquent de rencontrer quelques erreurs.</p>
	</div>
	<br/>
	<form id="connexion" action="javascript:connexion()">    
    <label for="login" >Login : </label><input type="text" value="<?php echo get_login(); ?>" name="login" id="login"/> <br/>
    <label for="password"></label>Mot de passe : <input type="password" name="password" id="password"/><br/>
    <span id="password_error">Login incorrect</span><span id="tranchage_error">Tranchage en cours...</span><br/>
    <input type="submit" value="Envoyer">
    </form>
    <script language="javascript">connexionTest=false;</script>
    
    <?php
	}
    ?>
    
    
    
    </div>
    
    
    <span id="table_choice" >
    
    <div id="tables_div">
    <p>Unplug te souhaite la bienvenue au casino.</p>
    <p>Ici tu pourras miser des heures de tranchage lors de parties de Black Jack.</p>
    <p>Si tu gagnes tu récoltera des UT que tu pourras utiliser à la <a href="shop.php">boutique</a> afin de trancher tes ennemis, ou couvrir tes arrières.</p>
    <p>Mais prends garde à toi ! Si jamais tu perds tu sera tranché du montant de ta mise, et la sanction prend effet immédiatement !</p>
    <p>Bien entendu si tu gagnes c'est ton adversaire qui est tranché !</p>
    <p>Il y a deux mode de jeu :</p>
    <ul>
    <li><b>Duel</b> : ne coûte rien à lancer ou à refuser</li>
    <li><b>Défi</b> : coûte de plus en plus cher à lancer. Si l'adversaire accepte le défi il reçoit l'argent, s'il refuse c'est lui qui doit payer. </li>
    </ul>
    <h3><a href="shop.php">Boutique</a></h3>
    </div>
    
    
    <div id="users_div">
    <table border="1" id="users">
    <caption><b>Utilisateurs connectés</b></caption>
    <tr><td></td></tr>
    </table>
    </div>
    
	</span>


	<span id="table_play" style="display:none;">
	
	<div id="table">
	
	<div id="casino">
		
	</div>
	
	<div id="ban_message"><span id="message"></span></div>
	
	<div id="players">
	
	<div id="player1"  class="player">
	<div id="cards1" class="subPlayerA" style="float:left;"></div>
	<div class="subPlayer" style="float:right;">
	<span >Joueur : <span id="nick1"></span><br/><br/>Mise : <span id="mise1"></span> jetons</span><br/><br/>Pot : <span id="pot1"></span> jetons</span>
	</div>
	</div>
	
	<div id="player2"  class="player">
	<div id="cards2" class="subPlayerA" style="float:left;"></div>
	<div  class="subPlayer" style="float:right;">
	<span >Joueur : <span id="nick2"></span><br/><br/>Mise : <span id="mise2"></span> jetons</span><br/><br/>Pot : <span id="pot2"></span> jetons</span>
	</div>
	</div>
	
	
	
	<div id="player3" class="player" style="display:none" >
	<div id="cards3" class="subPlayerA" style="float:left;"></div>
	<div  class="subPlayer" style="float:right;">
	<span >Joueur : <span id="nick3"></span><br/><br/>Mise : <span id="mise3"></span> jetons</span><br/><br/>Pot : <span id="pot3"></span> jetons</span>
	</div>
	
	
	</div>
	<div id="actions">
	<span id="actions_text"></span><br/>	
	<span id="actions_buttons">
	<input type="submit" id="start" onClick=startTable() value="Lancer la partie"/>
	<input type="submit" id="carte" onClick="jouer(1)" value="Carte"/>
	<input type="submit" id="double" onClick="jouer(2)" value="Double"/>
	<input type="submit" id="reste"  onCLick="jouer(3)" value="Reste"/>
	<input type="submit" id="reset"  onCLick="reset()" value="Nouvelle partie"/>
	</span>
	<br/>
	<input type="submit" id="rules_input"  onCLick="rules()" value="Afficher les règles"/>
	
	</div>
	
	
	</div>
	
	
	</span>

	</div>
	
	</div>
	<footer>
	Copyright : Unplug
	</footer>
	
	<div id="fond" onClick="quitMsg()"></div>
	<div id="message_fenetre" onClick="quitMsg()" class="fenetre"><p id="message_text"></p></div>
	<div id="prompt" class="fenetre">
	<span id="prompt_text"></span><br/><br/>
	<form action="javascript:setBet()">Mise : <input autocomplete="off" type="texte" id="prompt_value"> <input type="submit" value="Valider"></form>
	</div>
	<div id="menu_duel" class="fenetre">
	<span id="menu_duel_text"></span><br/><br/>
	<form action=""><input type="submit" onClick="launchDuel()" value="Duel"><p>ou</p>
	<input type="submit" id="defi_submit" onClick="launchChallenge()" value="Défi"></form>
	</div>
	
	<div id="wait_duel" class="fenetre">
	<span>Attente de réponse</span><br/>
	<input type="submit" value="Annuler" onClick="resetChallenge()">
	</div>
	
	<div id="answer_duel" class="fenetre">
	<span id="answer_duel_text"></span><br/><br/>
	<input type="submit" value="Accepter" onClick="acceptChallenge()">
	<input type="submit" value="Refuser" onClick="refuse()">
		
	</div>
	
	
	
    <div id="rules" onClick="quitMsg()" class="fenetre">
    <h2>Règles</h2>
    
    <p>Le but du jeu est de s'approcher le plus près de 21 points sans dépasser. Le joueur peut demander autant de cartes qu'il le souhaite.</p>
    
    <br/>
    <p>Valeur des cartes :</p>
    
    <ul>
    
    <li>Cartes simples : valeur faciale</li>
    <li>Figure : 10 points</li>
    <li>As : 11 ou 1 point (en fonction de ce qui avantage le joueur)</li>
    
    </ul>

	<br/>
    <p>Actions possibles :</p>

	<ul>
	<li>Carte : demander une nouvelle carte</li>
	<li>Double : demander une carte, doubler la mise et passer au joueur suivant</li>
	<li>Reste : passer au joueur suivant</li>
	
	</ul>
	
	<br/>
	<p>À l'issue de la partie les joueurs ayant le plus de points sans dépasser 21 gagne.</p>
	
    </div>
    
    </body>
</html>
