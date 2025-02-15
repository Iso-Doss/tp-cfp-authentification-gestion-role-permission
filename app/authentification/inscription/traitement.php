<?php
//echo "Ceci est la page de traitement de l'inscription";

$erreurs = [];
$donnees = $_POST;
$message = '';

if (empty($_POST['email'])) {
	$erreurs['email'] = 'Le champ adresse email est obligatoire';
}

if (empty($_POST['mot_de_passe'])) {
	$erreurs['mot_de_passe'] = 'Le champ mot de passe est obligatoire';
}

if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	$erreurs['email'] = 'Le champ adresse email doit être une adress mail valide. Exemple : noreply@gestion-role-permission.bj';
}

if (!empty($_POST['mot_de_passe'] && strlen($_POST['mot_de_passe']) < 8)) {
	$erreurs['mot_de_passe'] = 'Le champ mot de passe doit contenir au minimum huit caractères pour des raisons de sécurité.';
}

if (!empty($erreurs)) {
	$message = 'Oups! Un ou plusieurs champ(s) sont incorrectes.';
} else {
	$adresse_email_existe = verifier_si_email_existe($_POST['email']);
	if (is_string($adresse_email_existe)) {
		$erreurs['erreur_base_de_donnees'] = $adresse_email_existe;
		$message = $adresse_email_existe;
	} else if ($adresse_email_existe) {
		$erreurs['email'] = 'Le champ adresse email est déja prit. Veuillez en choisir une autre.';
		$message = 'Oups! Un ou plusieurs champ(s) sont incorrectes.';
	} else {
		$ajouter_utilisateur = ajouter_utilisateur($_POST['email'], $_POST['mot_de_passe']);
		if (is_string($ajouter_utilisateur)) {
			$erreurs['erreur_base_de_donnees'] = $adresse_email_existe;
			$message = $adresse_email_existe;
		} else if ($ajouter_utilisateur) {
			$erreurs = [];
			$message = 'Inscription effectué avec succès. Veuillez consulter vos mails afin de valider votre inscription.';
			// Mettre en place une fonction qui permet d'envoyer un mail de validation de compte.
			$message_mail = 'Merci de vous êtes inscrit sur le site. Afin de valider votre inscription, veuillez cliquer sur le lien suivant : <a href="http://localhost:8080/index.php?ressource=valider-inscription&action=traitement&email=' . $_POST['email'] .'">Cliquer ici</a>.';
			$envoyer_mail = envoyer_mail($_POST['email'], 'Validation d\'inscription', $message_mail);
		} else {
			$erreurs['erreur_base_de_donnees'] = 'Impossible de finaliser le processus d\'inscription. Merci de réessayer';
			$message = 'Impossible de finaliser le processus d\'inscription. Merci de réessayer';
		}
	}
}

header('Location: index.php?ressource=inscription&action=formulaire&donnees=' . json_encode($donnees) . '&erreurs=' . json_encode($erreurs) . '&message=' . json_encode($message));