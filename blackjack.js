var reloadTime = 1000;
var mvt=-1;
var phase;
var joueur=1;
var main = new Array();
var connexionTest=true;
var refreshTables;
var refreshUsers;
var bet_var=false;


function connexion(){
	
	$.post("php/connexion.php",{ login : $("#login").val(), password : $("#password").val()}, function(data){
		
		if (data['error']==0){	
			$("#connexion").fadeOut();
			connexionTest=true;
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
		refreshTables = window.setInterval(getTables, reloadTime);
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
		
		$.getJSON('php/get-online.php',function(data){
			
			$('#users tr').html("");
			
			if (data['error']==0){
				var online='';
				for (var id in data['list']) {
					$('#users tr:last').after('<tr><td>'+data['list'][id]['login']+'</td><td></td></tr>');
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
	
	$("#nameExists").fadeOut()
	
	if ($("#newTable").val()!=""){
	
		$.post("php/create-table.php",{ name : $("#newTable").val()},function(data){
			
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
					
				}
				
				$("#nick"+x).html(nick);
				$("#mise"+x).html(data['mise'][x]);
				$("#pot"+x).html(data['pot'][x]);

			
			}
				
			});
			
		if (joueur==1 && phase==-2 ){
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
			break;
			
			case 2:
			$.getJSON("php/double.php",function(data){
				
				if (data['error']==0){

					jouer(1);
					jouer(3);
					
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
			})};
	
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
	
}

function msg(txt){
	
	
	$("#message_text").html(txt);
	$("#fond").fadeIn();
	$("#message_fenetre.fenetre").fadeIn();
	
	
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

function rules(){
	
	$("#fond").fadeIn();
	$("#rules.fenetre").fadeIn();
	
}
