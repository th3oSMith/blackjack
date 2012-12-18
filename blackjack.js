var reloadTime = 1000;
var mvt=-1;
var phase;
var joueur=1;
var main = new Array();
var connexionTest=true;
var refreshTables;
var refreshUsers;
var bet_var=false;
var score_joueur;
var blackjack_possible=true;
var target=null;
var waitReply;
var challenger;



function connexion(){
	
	$.post("php/connexion.php",{ login : $("#login").val(), password : $("#password").val()}, function(data){
		
		if (data['error']==0){	
			$("#connexion").fadeOut();
			connexionTest=true;
			$("#user_info").fadeIn();
		}
		else{
			$("#password_error").fadeIn();
		}
	},'json');
}

$(document).ready(function() {
	if(document.getElementById('users')) {
		// actualisation des messages
		refreshUsers = window.setInterval(getOnlineUsers, reloadTime);
		//refreshTables = window.setInterval(getTables, reloadTime);
	}
});



function logout(){
	
	$.getJSON("php/logout",function(data){
		
		window.location.reload();		
		
		
	});
		
	}



function getOnlineUsers(){
	
	if (connexionTest){
	
		$("#table_choice").fadeIn();
		
		$("#user_info").fadeIn();	
		
		$.getJSON('php/get-online.php',function(data){
			
			$('#users tr').html("");
			
			if (data['error']==0){
				
				if (data['challenger']){
					
					challenge(data['challenger']['type'],data['challenger']['login']);
					challenger=data['challenger']['id'];
				}
				else{
					
					$("#answer_duel").fadeOut();
					
				}
				
				var online='';
				for (var id in data['list']) {
					
					$('#users tr:last').after('<tr><td class="td_joueur"><a href="#" onClick=duel('+data['list'][id]['id']+',"'+data['list'][id]['login']+'")>'+data['list'][id]['login']+'</a></td><td>'+data['list'][id]['pot']+' UT</td></tr>');
				}	
			}
		
			
			displayLogin(data['login'], data['pot']);
			
		
		});
	}
}

function getTables(){
	
	if (connexionTest){
		$.post('php/get-tables.php',function(data){
			var contenu='';
			if(data['error']==0){
				
				$('#tables_list tr').html("");
				
				for (var id in data['list']){
				
					$('#tables_list tr:last').after('<tr><td width="100%">'+data['list'][id]['nom']+' - '+data['list'][id]['nbJ']+' Joueurs</td><td width="100%"><a href= "#" onCLick=rejoindreTable('+data['list'][id]['id']+')>Rejoindre</a></td></tr>');					
				 }	
			}else{
					
					$('#tables_list tr').html("");
					$('#tables_list tr:last').after('<tr><td width="100%">Pas de table disponible</td><td width="100%">Désolé</td></tr>');	
				
			}
		},'json');
	}
}

function createTable(){
	
		$.post("php/create-table.php",{ challenger : challenger},function(data){
			
			if (data['error']==0){
				
				//Evenements à effectuer après la création de la table
				//Changement de l'interface, on passe à la table de Jeu
				
				displayTable();
				joueur=1;
				//
							
				}else{
					
					if (data['error']==2){
						msg("Vous n'avez plus de jetons");
						
					}else{
						$("#nameExists").fadeIn()
					}
				}
			
			},'json');	

}

function displayCard(player,cards){

		
		

	if (player!=0){ //Pour les joueurs sauf le croupier
	
		$("#cards"+player).html('');
		
	
		for (x=0;x<cards.length;x++){
		
		var position=x*150-x*20;
		
		
		$("#cards"+player).append('<image class="card" style="height:150px;display:block;position:relative;top:-'+position+'px;left:'+x*15+'px;"  src="cartes/'+cards[x][0]+''+cards[x][1]+'.png" />');
		
		}
		
	}else{
		

		$("#casino").html('');
		for (x=0;x<cards.length;x++){
		
		
		
		$("#casino").append('<image style="height:150px;display:inline-block;position:relative;left:-'+x*20+'px;" src="cartes/'+cards[x][0]+''+cards[x][1]+'.png" />');
		
		}
		
		
		
	}

}


function displayTable(){
	

	
	clearInterval(refreshTables);
	clearInterval(refreshUsers);
	
	$("#table_choice").fadeOut();
	$("#table_play").fadeIn();
	
	window.setInterval(listen, reloadTime);
	window.setInterval(getMains, reloadTime);
	
	
	message("Bienvenue, la partie va commencer dans quelques instants");
	
}


function listen(){
	
	
	$.getJSON("php/listen.php",function(data){
		
		mvt=parseInt(data['mvt']);
		phase=parseInt(data['phase']);
		
		
		if (joueur==parseInt(data['mvt'])) { //À changer en IF
		

		switch (parseInt(data['phase'])){
			
			
			case -2: //Creation du jeu de cartes
				$.get("php/do.php",function(data){
			
			//Rien à faire	
				
			});
			break;
			
			case -1: //Tour de mise
				if (bet_var==false){
					$.getJSON("php/get-pot.php",function(data){
						
						
						
						if (data['pot']==0){
							kick();
						}
						
					});
					
					
					message("");
					bet("Miser ?");
				}
			break;
			
			 
			case 0: //Récupération de la main et du Casino
			
			$.getJSON("php/do.php",function(data){ });
			
			
			
			break;
			
			case 1: //Tour de jeu
			
			if (score_joueur>21){
				
				msg("Vous avez dépassé 21",true);
				jouer(3);
				
			}
			
			if (score_joueur==21){
				
				msg("Blackjack !",true);
				jouer(3);
				
			}
			
			break;
			
			case 2:
			
			$.get("php/do.php",function(data){
				
				
				 });
			
			break;
			
				 
			case 3:
			
			$.getJSON("php/do.php",function(data){
				
				
				if (data['gain']>=0){
				
				message("Partie terminée - Vous avez gagné "+data["gain"]+" jetons");
				}
				else{
					
					message("Partie terminée - Vous avez perdu "+Math.abs(data["gain"])+" jetons");
				}
				
				
			});
			
			break;
			
			case 4: //Attente de la nouvelle partie
			
			
			break;
			
			case -7:
			
			
			$.getJSON("php/do.php",function(data){
				
				
				
			});
			
			break;
			
			case -8: //Gestion de la déconnexion d'un joueur
			
			$.getJSON("php/do.php",function(data){
				
				
				joueur=data['joueur'];
			
				
				effacer(parseInt(data['nb_joueur'])+1,null);
				
				
			});
			
			break;
			
			
			
		}
		
		
		
	
		

	}



		});	
	
	
}

function getMains(){
	
	$.getJSON("php/get-mains.php",function(data){
			
			
			
			for (var x=0;x<=data['nb_joueur'];x++){
			
			
			
				displayCard(x,data['main'][x]);
				
				var nick=data['nick'][x];
				
				if (mvt==x){
					
					
					nick="<b>"+data['nick'][x]+"</b>";
					
				}
					
				
				
				if (joueur==x){
					
					$("#user_area_pot").html(data['pot'][x]);
					score_joueur=score(data['main'][x]);
					
				}
				
				$("#nick"+x).html(nick);
				$("#mise"+x).html(data['mise'][x]);
				$("#pot"+x).html(data['pot'][x]);

			
			}
				
			});
			
		if (joueur==1 && phase==-2 ){
		
		//On retarde l'apparition du bouton le temps que la partie s'initialise
		var clear = setTimeout(function() {
				$("#message").fadeIn();
				},6000);
			
		
		
		$("#start").fadeIn(0);
		}else
		{
		$("#start").fadeOut(0);
			}
		
		if (joueur==1 && phase==4 ){
		$("#reset").fadeIn(0);
		}else
		{
		$("#reset").fadeOut(0);
			}
		
	
}

function startTable(table_id){
	
	$.getJSON("php/start-table.php",function(data){ 
		
			
		
	});
	
}


function rejoindreTable(table_id){
	
	$.post("php/join-table-user.php",{ id : table_id},function(data){
		

		if (data['error']==0){
			
			//Evenements à effectuer après la création de la table
			joueur=data['joueur'];
			displayTable();
			
						
			}else{
				
				if (data['error']==2){
					
					msg("Vous n'avez pas de jetons !");
				}else{
					
					msg("Connexion impossible");
				}	
			}
		
		},'json');	
	
	
	
	
}

function jouer(action){
	
	if (mvt==joueur && phase==1){
		
		switch (action){
			
			case 1:
			$.getJSON("php/draw-card.php",function(data){});
			blackjack_possible=false;
			break;
			
			case 2:
			$.getJSON("php/double.php",function(data){
				
				
				
				if (data['error']==0){

					jouer(1);
					setTimeout(function() {jouer(3);},200);
					
					
				}else{
					
					msg("Vous n'avez pas assez de jetons pour doubler");
					
				}
				
				
					
			});

			
			break;
			
			case 3:
			$.getJSON("php/do.php",function(data){});
			break; 
			
		}
	}else{
		
		msg("Ce n'est pas votre tour de jeu",true);
		
	}
	
	
	
	
}

function reset(){
	
	$.getJSON("php/table-reset.php",function(data){
		
	});	
	
	}





function message(msg,temp){
	
	$("#message").html(msg);
	$("#message").fadeIn();
	
	
	if (temp){
		$("#message").fadeIn(3,function(){
			var clear = setTimeout(function() {
				$("#message").fadeOut();
				},2000);
			})
			
			};
	
}


function effacer(player){
	
	$("#nick"+player).html("OUT");
	$("#mise"+player).html("OUT");
	$("#cards"+player).html('');

}

function kick(){
	
	msg("Vous n'avez plus de jetons, vous êtes hors jeu");
	setTimeout(function() {
				window.location.reload();		
				},2000);
		
}


function quitMsg(){
	
	$("#fond").fadeOut();
	$("#message_fenetre.fenetre").fadeOut();
	$("#rules.fenetre").fadeOut();
	$("#menu_duel.fenetre").fadeOut();
	
}

function msg(txt,temp){
	
	
	$("#message_text").html(txt);
	
	$("#message_fenetre.fenetre").fadeIn();
	
		if (temp){

			var clear = setTimeout(quitMsg,2000);
			
			}
		else{
			
			$("#fond").fadeIn();
		}
	
	
	
}

function displayLogin(login, pot){
	
	$("#user_area_login").html(login);
	$("#user_area_pot").html(pot);
	
	}

function bet(txt){
	
	$("#prompt_text").html(txt);
	$("#fond").fadeIn();
	$("#prompt").fadeIn();
	bet_var=true;
    document.getElementById('prompt_value').focus() 


	
}


function unbet(){
	
	$("#fond").fadeOut();
	$("#prompt").fadeOut();
	bet_var=false;
	
}

function setBet(){
	
	if (phase!= -1){
		
		unbet();
		
		}
	
	else{
	
		if (parseInt($("#prompt_value").val())>0){

			unbet()
			$.post("php/do.php",{mise : parseInt($("#prompt_value").val())},function(data){
							
							
							if(data['error']==0){
								
								//$("#mise"+joueur).val(data['mise']);
								
							}else{
								
								switch (data['error']){
									
									case 1:
									bet("Vous n'avez pas assez de jetons pour miser");
									break;
									
									case 2:
									bet("Hep Hep Hep");
									break;
								
								}
							}
						
							
							
							
							},'json');
			}
	 
	}

 }

function rules(){
	
	$("#fond").fadeIn();
	$("#rules.fenetre").fadeIn();
	
}


function score(main)
{
	total=0;
	nbAs=0;
			
			
			for (y=0;y<main.length;y++){
				
				
				if (main[y][1]<11){
					
					total+=main[y][1];
					
					
				}else{
					
					total+=10;
				}
				
				if (main[y][1]==1){
					
					total+=10; //on ajoute 10 pour avoir 11
					nbAs++; // On note qu'on a eu un as
					
				}
				
				
			}
			
			while (total > 21 && nbAs>0) {
				
				total-=10;
				nbAs--;
				
			}
	
	return total;
	
	
	}

function duel(id,nom){
	
	
	target=id;
	$("#menu_duel_text").html("Défier "+nom);
	$("#fond").fadeIn();
	$("#menu_duel").fadeIn();
	
}


function launchDuel(){
	
	quitMsg();
	
	$.post("php/set-defi.php",{ id : target}, function(data){
		
		
		if (data['error']==0){
			
			$("#fond").fadeIn();
			$("#wait_duel").fadeIn();
			waitReply=window.setInterval(waitForReply, reloadTime);
			
		}else{
			
			if (data['error']==2){
				
				msg("Il a déjà dit non !");
				
			}else{
			
				msg("Impossible de lancer le défi");
			}
		}
		
		
	},'json');
	
}

function waitForReply(){
	
	$.getJSON("php/get-answer.php", function(data){
		
		if (data['error']==1){
			
			$("#fond").fadeOut();
			$("#wait_duel").fadeOut();
			clearInterval(waitReply);
		
		msg(data["table_id"]);
		
		rejoindreTable(data["table_id"]);
		
			
		}else if (data['error']==2) {
			
			
			$("#fond").fadeOut();
			$("#wait_duel").fadeOut();
			clearInterval(waitReply);
			msg("L'adversaire décline votre offre");
			
		}
		
		
	});
	
}

function challenge(type,nom) {
	
	if (type=="duel"){
		
	$("#answer_duel_text").html(nom+" vous défie !");
	$("#answer_duel").fadeIn();
		
	}
	
	
}

function resetChallenge()
{
	
	$.get("php/reset-challenge.php", function(data){
		
			$("#fond").fadeOut();
			$("#wait_duel").fadeOut();
			clearInterval(waitReply);
			
		
		
		
	});
	
	}

function refuse()
{
	$.get("php/refuse-defi.php", function(data){
		
	
		
	});
	
}


function acceptDefi(){
	
	createTable();
	
	}
