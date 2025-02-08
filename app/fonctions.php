<?php

/**
 * Router.
 *
 * @param string $ressource La ressource à laquelle l'on souhaite accéder.
 * @param string $action L'action a executé.
 */
function router(string $ressource, string $action)
{
	$page = 'app/authentification/connexion/formulaire.php';

	if ($ressource == 'connexion' && $action == 'formulaire') {
		$page = 'app/authentification/connexion/formulaire.php';
	} elseif ($ressource == 'connexion' && $action == 'traitement') {
		$page = 'app/authentification/connexion/traitement.php';
	} else if ($ressource == 'inscription' && $action == 'formulaire') {
		$page = 'app/authentification/inscription/formulaire.php';
	} elseif ($ressource == 'inscription' && $action == 'traitement') {
		$page = 'app/authentification/inscription/traitement.php';
	} else if ($ressource == 'mot-de-passe-oublier' && $action == 'formulaire') {
		$page = 'app/authentification/mot-de-passe-oublier/formulaire.php';
	} elseif ($ressource == 'mot-de-passe-oublier' && $action == 'traitement') {
		$page = 'app/authentification/mot-de-passe-oublier/traitement.php';
	} else if ($ressource == 'reinitialiser-mot-de-passe' && $action == 'formulaire') {
		$page = 'app/authentification/reinitialiser-mot-de-passe/formulaire.php';
	} elseif ($ressource == 'reinitialiser-mot-de-passe' && $action == 'traitement') {
		$page = 'app/authentification/reinitialiser-mot-de-passe/traitement.php';
	}

	include $page;
}

function connexion_base_de_donnees(): PDO|string
{
	try {
		$base_de_donnees = new PDO('mysql:host=localhost;dbname=cfp-authentification', 'root', 'root');
	} catch (Exception $e) {
		//$base_de_donnees = $e->getMessage();
		$base_de_donnees = 'Impossible de se connecter a la base de données.';
	}

	return $base_de_donnees;
}

/**
 * Cette fonction permet de verifier si une adresse mail existe ou pas dans la base de donnees (table utilisateur).
 *
 * @param string $email L'adresse email
 * @return bool|string Est-ce que l'adresse mail existe ou pas dans la base de donnees (table utilisateur).
 */
function verifier_si_email_existe(string $email): bool|string
{
	$base_de_donnees = connexion_base_de_donnees();

	if (is_string($base_de_donnees)) {
		return $base_de_donnees;
	}

	$requette = "SELECT email FROM utilisateurs WHERE email = :email";
	$preparer_requette = $base_de_donnees->prepare($requette);
	$preparer_requette->execute(['email' => $email]);
	$utilisateur = $preparer_requette->fetch(PDO::FETCH_ASSOC);

	if (is_array($utilisateur)) {
		$verifier_existe = true;
	} else {
		$verifier_existe = false;
	}

	return $verifier_existe;
}

/**
 * Cette fonction permet d'ajouter un utilisateur a la base de donnees suivant les paramètres (email, mot de passe, nom, prénoms, sexe, date de naissance)
 *
 * @param string $email L'adresse email de l'utilisateur a ajouté.
 * @param string $mot_de_passe Le mot de passe de l'utilisateur a ajouté.
 * @param string|null $nom Le nom de l'utilisateur a ajouté.
 * @param string|null $prenoms Les prénoms email de l'utilisateur a ajouté.
 * @param string|null $sexe Le sexe de l'utilisateur a ajouté.
 * @param string|null $date_de_naissance La date de naissance email de l'utilisateur a ajouté.
 * @return bool|string Est-ce que l'utilisateur a pu étre a jouté ou pas.
 */
function ajouter_utilisateur(string $email, string $mot_de_passe, string|null $nom = null, string|null $prenoms = null, string|null $sexe = null, string|null $date_de_naissance = null): bool|string
{
	$base_de_donnees = connexion_base_de_donnees();

	if (is_string($base_de_donnees)) {
		return $base_de_donnees;
	}

	$requette = "INSERT INTO utilisateurs(email, mot_de_passe, nom, prenoms, sexe, date_de_naissance) values(:email, :mot_de_passe, :nom, :prenoms, :sexe, :date_de_naissance)";
	$preparer_requette = $base_de_donnees->prepare($requette);
	return $preparer_requette->execute([
		'email' => $email,
		'mot_de_passe' => sha1($mot_de_passe),
		'nom' => $nom,
		'prenoms' => $prenoms,
		'sexe' => $nom,
		'date_de_naissance' => $date_de_naissance,
	]);
}