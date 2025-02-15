<?php
//echo "Ceci est la page de traitement de la validation de l'inscription";

$erreurs = [];
$donnees = $_GET;
$message = '';

if (empty($_GET['email'])) {
	$erreurs['email'] = 'Le champ adresse email est obligatoire';
}

if (!empty($erreurs)) {
	$message = 'Oups! Un ou plusieurs champ(s) sont incorrectes.';
} else {
	$utilisateur = recuperer_utilisateur_a_partir_d_email($_GET['email']);
	if (empty($utilisateur)) {
		$erreurs['email'] = 'Aucun utilisateur n\' a été trouvé pour cet adresse email.';
	} elseif (!empty($utilisateur['activer_le'])) {
		$erreurs['email'] = 'Le compte est deja valider pour cet adresse email. Veuille vous connecter directement.';
	} elseif (empty($utilisateur['activer_le'])) {
		echo "Super je peux activer le compte de l'utilisateur";
	}
}

//header('Location: index.php?ressource=inscription&action=formulaire&donnees=' . json_encode($donnees) . '&erreurs=' . json_encode($erreurs) . '&message=' . json_encode($message));