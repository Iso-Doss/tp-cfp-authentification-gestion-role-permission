<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

//Load Composer's autoloader
require 'vendor/autoload.php';

/**
 * Router.
 *
 * @param string $ressource La ressource à laquelle l'on souhaite accéder.
 * @param string $action L'action a executé.
 */
function router(string $ressource, string $action)
{
	$page = 'app/authentification/connexion/formulaire.php';

	if (!empty($ressource)) {
		// Cas des pages d'authentification.
		if (
			($ressource == 'connexion' || $ressource == 'inscription' || $ressource == 'valider-inscription' || $ressource == 'mot-de-passe-oublier' || $ressource == 'reinitialiser-mot-de-passe')
			&&
			($action == 'formulaire' || $action == 'traitement')
			&&
			file_exists('app/authentification/' . $ressource . '/' . $action . '.php')
		) {
			$page = 'app/authentification/' . $ressource . '/' . $action . '.php';
		} else {
			$page = 'app/404.php';
		}
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

/**
 * Cette fonction permet d'envoyer un mail.
 *
 * @param string $to_email L'adresse email du destinataire.
 * @param string $subject Le titre du mail.
 * @param string $message Le corps du mail.
 *
 * @return bool Est-ce que le mail a pu être envoyé ou pas.
 */
function envoyer_mail(string $to_email = 'dossou.israel48@gmail.com', string $subject = 'Inscription', string $message = ''): bool
{
	//Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer(true);

	try {
		//Server settings
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'votre adresse gmail';
		$mail->Password = 'le mot de passe de votre adresse gmail';
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$mail->Port = 587;

		//Recipients
		$mail->setFrom('mesepreuvesafrica@gmail.com', 'TP CFP Authentification');
		$mail->addAddress($to_email, 'Client');

		//Content
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $message;

		$mail->send();
		return true;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Cette fonction permet de récupérer les informations d'un utilisateur dans la base de donnees à partir de son adresse email (table utilisateur).
 *
 * @param string $email L'adresse email
 * @return array Les informations sur l'utilisateur.
 */
function recuperer_utilisateur_a_partir_d_email(string $email): array
{
	$base_de_donnees = connexion_base_de_donnees();

	if (is_string($base_de_donnees)) {
		return [];
	}

	$requette = "SELECT * FROM utilisateurs WHERE email = :email";
	$preparer_requette = $base_de_donnees->prepare($requette);
	$preparer_requette->execute(['email' => $email]);
	$utilisateur = $preparer_requette->fetch(PDO::FETCH_ASSOC);

	if (is_array($utilisateur)) {
		$verifier_existe = $utilisateur;
	} else {
		$verifier_existe = [];
	}

	return $verifier_existe;
}