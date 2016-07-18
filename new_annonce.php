<?php
include('include/functions.php');
include('include/config.php');
?>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Nouvelle annonce</title>
		<link rel="stylesheet" href="style.css" />
	</head>
	<body>
		<?php include_content('top'); ?>
		<section id="main">	
			<h1>Poster une nouvelle annonce</h1>
			<?php
			
			if(!$user->data['is_registered']) include('include/not_registered.php');

			else {
				secure_get();
				
				//Si on a pas encore rempli le formulaire
				if(!isset($_POST['Valider'])) print_form_new_annonce([]);
				
				else {
					$lieu = $request->variable('lieu', '');
					$superf_h = $request->variable('superf_h', 0);
					$superf_t = $request->variable('superf_t', 0);
					$link = $request->variable('link', '');
					$habit = $request->variable('habit', '');
					$time = $request->variable('time', 0);
					$price = $request->variable('price', 0.0);
					$depart = $request->variable('depart', 0);
					$note = $request->variable('note', '');
					
					$habit = convert_str_nb($habit);
					$note = convert_str_nb($note);
					
					if($lieu < 0) $lieu = 0;
					if($superf_h < 0) $superf_h = 0;
					if($superf_t < 0) $superf_t = 0;
					if($time < 0) $time = 0;
					if($price < 0.0) $price = 0.0;
					if($depart < 0) $depart = 0;
					
					$param_array = ['lieu' => $lieu, 'superf_h' => $superf_h, 'superf_t' => $superf_t, 'link' => $link, 'habit' => $habit, 'time' => $time, 'price' => $price, 'depart' => $depart, 'note' => $note];
					
					$print_form = false;
					
					//Si tous les champs sont remplis
					if(!empty($lieu) && $superf_h != 0 && $superf_t != 0 && !empty($link) && $habit != -1 && $time != 0 && $price != 0) {
						if(preg_match('#^https?://(www.)?[a-zA-Z0-9]+\.[a-z0-9]{1,4}\??#', $link)) {
							if(preg_match('#^[a-zA-Z][a-zA-Z- ]+#', $lieu)) {
								if($time <= $sort_array['max_time']) {
									if($superf_h <= $sort_array['max_superf_h']) {
										if($superf_t <= $sort_array['max_superf_t']) {
											if($price <= $sort_array['max_price']) {
												if($depart <= $sort_array['max_depart']) {
													$req = $bdd->prepare('INSERT INTO annonces(lieu, superf_h, superf_t, link, habit, time, price, date, auteur, departement)
																		VALUES(:lieu, :superf_h, :superf_t, :link, :habit, :time, :price, NOW(), :auteur, :departement)');
													
													$req->execute(array(
														'lieu' => $lieu,
														'superf_h' => $superf_h,
														'superf_t' => $superf_t,
														'link' => $link,
														'habit' => $habit,
														'price' => $price,
														'auteur' => $user->data['username'],
														'time' => $time,
														'departement' => $depart));
													
													$req->closeCursor();
													
													$get_id = $bdd->query('SELECT id, time, price, link, auteur FROM annonces WHERE time = '.$time.' AND price = '.$price.' AND link = \''.$link.'\' AND auteur = \''.$user->data['username'].'\'');
													$annonce = $get_id->fetch();
													$id_annonce = $annonce['id'];
													$get_id->closeCursor();
													
													$req = $bdd->prepare('INSERT INTO notes(auteur, annonce, value) VALUES(:auteur, :annonce; :value)');
													
													$req->execute(array('auteur' => $user->data['username'], 'annonce' => $id_annonce, 'value' => $note));
													
													$req->closeCursor();
													
													echo('<p id="form" class="success">L\'annonce a bien été ajoutée, bien joué !</p>');
												}
												
												else {
													$print_form = true;
													echo('<p id="form" class="error">Le département doit être inférieur à 96 !</p>');
												}
											}
											
											else {
												$print_form = true;
												echo('<p id="form" class="error">Le prix doit être inférieur à 1000 k€ ! Faut pas déconner !</p>');
											}
										}
										
										else {
											$print_form = true;
											echo('<p id="form" class="error">La superficie du terrain doit être comprise entre 1 et 65536 !');
										}
									}
									
									else {
										$print_form = true;
										echo('<p id="form" class="error">La superficie de la maison doit être comprise entre 1 et 65536 !');
									}
									
								}
								
								else{
									$print_form = true;
									echo('<p id="form" class="error">Le temps doit être compris entre 1 et 255 inclus !</p>');
								}
							}
							
							else {
								$print_form = true;
								echo('<p id="form" class="error">Le lieu ne doit contenir que des lettres, des tirets et des espaces, et doit commencer par une lettre !</p>');
							}
						}
						
						else{
							$print_form = true;
							echo('<p id="form" class="error">Le lien n\'est pas correct !<p>');
						}
					}
					
					//S'il manque des champs
					else {
						$print_form = true;
						echo('<p id="form" class="error">Il faut remplir tous les champs !</p>');
					}
					
					//Si une ou plus des valeurs du formulaire sont mauvaises
					if($print_form) print_form_new_annonce($param_array);
				}
			} ?>
		</section>
		<?php include_content('bottom'); ?>
	</body>
</html>