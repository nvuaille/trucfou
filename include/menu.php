﻿<nav>
		<ul>
			<?php
			$host = $request->server('HTTP_HOST', '');
			$is_localhost = $host == 'localhost';
			
			echo('<li><a href="'.append_sid('index.php').'">Accueil</a></li>');
			echo('<li><a href="'.append_sid('forum/index.php').'">Forum</a></li>');
			
			if($user->data['is_registered']) {
				echo('<li><a href="'.append_sid('docs.php').'">Documents</a></li>');
				echo('<li><a href="'.append_sid('annonces.php', 'reverse=true').'">Annonces</a></li>');
				echo('<li><a href="'.append_sid('new_annonce.php').'">Nouvelle annonce</a></li>');
				
				if(!$is_localhost) {
					echo('<li><a href="'.append_sid('survey/index.php').'">Sondages</a></li>');
					echo('<li><a href="'.append_sid('booked/index.php').'">Calendrier</a></li>');
				}
				
				echo('<li><a href="'.append_sid('forum/ucp.php').'">Mon Profil</a></li>');
				echo('<li><a href="'.append_sid('forum/ucp.php', 'mode=logout', true, $user->session_id).'">Déconnexion</a></li>');
				
				if($user->data['username'] == 'Belette' && $is_localhost)
					echo('<li><a href="'.append_sid('phpmyadmin/').'">PMA</a></li>');
			}
			
			else
				echo('<li><a href="'.append_sid('forum/ucp.php', 'mode=register', true, $user->session_id).'">Inscription</a></li>');
			?>
		</ul>
</nav>