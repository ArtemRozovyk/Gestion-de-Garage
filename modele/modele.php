<?php
	require_once('modele/connect.php');

	// fonction qui permet de se connecter à la base de données
	function getConnect()
	{
		$connexion = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BDD, USER, PASSWORD);
		$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connexion->query('SET NAMES UTF8');
		return $connexion;

	}

	// fonction qui permet de récupérer toutes les informations d'un employé avec son login et mdp
	function getEmploye($login, $motdepasse)
	{
		$connexion = getConnect();
		//' union select * from employe where login ='ar
		$requete = "select *  from employe where login='$login' and motDePasse='$motdepasse'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$employe = $resultat->fetch();
		$resultat->closeCursor();
		return $employe;

	}

	// fonction qui permet de récupérer les informations d'un client selon son id
	function getClient($id)
	{
		$connexion = getConnect();
		$requete = "select *  from client where idClient=$id";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$client = $resultat->fetch();
		$resultat->closeCursor();
		return $client;
	}

	// fonction qui permet de récupérer l'ensemble des interventions en 'differe' d'un client selon son id
	function getInterDiff($id)
	{
		$connexion = getConnect();
		$requete = "select *  from intervention NATURAL JOIN typeintervention where idClient=$id and etat='differe' order by dateIntervention desc";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$diff = $resultat->fetchAll();
		$resultat->closeCursor();
		return $diff;
	}

    // fonction qui permet de récupérer l'ensemble des interventions en 'attente de paiement' d'un client selon son id
    function getInterEnAttente($id)
	{
		$connexion = getConnect();
		$requete = "select *  from intervention NATURAL JOIN typeintervention where idClient=$id and etat='en attente de payement' order by dateIntervention desc";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$enatt = $resultat->fetchAll();
		$resultat->closeCursor();
		return $enatt;
	}

	// fonction qui permet de changer l'état d'une intervention en 'paye'
	function payerInter($code)
	{
		$connexion = getConnect();
		$requete = " update intervention set etat='paye' where code=$code";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet de récupérer toutes les interventions en 'en attente de paiement'
	function getEnAttente($inter)
	{
		$connexion = getConnect();
		$requete = "select *  from intervention NATURAL JOIN typeintervention where code=$inter and etat='en attente de payement'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$enatt = $resultat->fetch();
		$resultat->closeCursor();
		return $enatt;
	}

    // fonction qui permet de changer l'état d'une intervention en 'differe'
    function differer($inter)
	{
		$connexion = getConnect();
		$requete = " update intervention set etat='differe' where code=$inter";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet de récupérer les informations d'un client selon son nom et sa date de naissance
	function getIdClient($nom, $date)
	{
		$connexion = getConnect();
		$requete = "select *  from client where dateNaiss='$date' and nom='$nom'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$client = $resultat->fetch();
		$resultat->closeCursor();
		return $client;
	}

	// fonction qui permet de modifier les informations d'un client
	function modifierClient($id, $modifs)
	{
		$connexion = getConnect();
		$requete = "update client set ";
		foreach ($modifs as $key => $val) {
			$requete .= " $key='$val' ,";
		}
		$requete = substr($requete, 0, strlen($requete) - 1);
		$requete .= " where idClient=$id";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet de récupérer toutes les interventions passées d'un client selon son id
	function getInterventionsPasses($id)
	{
		$connexion = getConnect();
		$requete = "select *  from intervention natural join typeintervention where idClient=$id and 
dateIntervention<=curdate() and 
heureIntervention+1 < hour(now()) order by dateIntervention desc";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$inters = $resultat->fetchAll();
		$resultat->closeCursor();
		return $inters;
	}

	// fonction qui permet de renvoyer les informations d'un client
	function existeClient($nom, $prenom, $date)
	{
		$connexion = getConnect();
		$requete = "select *  from client where dateNaiss='$date' and nom='$nom' and prenom='$prenom'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$client = $resultat->fetch();
		$resultat->closeCursor();
		return $client;
	}

	// fonction qui permet d'ajouter un client dans la base de données
	function ajouterClient($infos)
	{
		$connexion = getConnect();
		// on crée la requête grâce au tableau entré en paramètre
		$requete = "insert into client (";
		foreach ($infos as $key => $val) {
			$requete .= "$key,";
		}
		$requete = substr($requete, 0, strlen($requete) - 1);
		$requete .= ") values (";
		foreach ($infos as $key => $val) {
			$requete .= "'$val',";
		}
		$requete = substr($requete, 0, strlen($requete) - 1);
		$requete .= ")";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet d'ajouter un compte dans la base de données
	function creerCompte($nom, $login, $mdp, $categorie)
	{
		$connexion = getConnect();
		$requete = " insert into employe VALUES ('$nom','$login','$mdp','$categorie')";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet de récupérer les informations d'un employé selon son login et mdp
	function chercherEmploye($nom, $login)
	{
		$connexion = getConnect();
		$requete = "select *  from employe where login='$login' or nomEmploye='$nom'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$employe = $resultat->fetch();
		$resultat->closeCursor();
		return $employe ;
	}

    // fonction qui permet de récupérer les informations d'un employé selon son nom
    function chercherUnEmploye($nom)
{
	$connexion = getConnect();
	$requete = "select *  from employe where nomEmploye='$nom'";
	$resultat = $connexion->query($requete);
	$resultat->setFetchMode(5);
	$employe = $resultat->fetch();
	$resultat->closeCursor();
	return $employe ;
}

    // fonction qui permet de supprimer un employé selon son nom
	function supprimerEmploye($nom){
		$connexion = getConnect();
		$requete = " delete from employe where nomEmploye='$nom'";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet de récupérer l'ensemble des employés du garage avec leurs informations
	function chercherToutLesEmploye(){
		$connexion = getConnect();
		$requete = "select *  from employe";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$employe  = $resultat->fetchAll();
		$resultat->closeCursor();
		return $employe ;
	}

	// fonction qui permet de modifié les informations d'un employé dans la base de données
	function modifierEmploye($attributAModifier,$valeur,$nomInitial){
		$connexion = getConnect();
		$requete = " update employe set $attributAModifier='$valeur' where nomEmploye='$nomInitial'";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet d'ajouter une intervention dans la base de données
	function creerTypeIntervention($nomTI,$montant,$listePieces) {
		$connexion = getConnect();
		$requete = " insert into typeintervention VALUES ('$nomTI','$montant','$listePieces')";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet de récupérer le type d'une intervention selon son nom
	function chercherTypeIntervention($nom){
		$connexion = getConnect();
		$requete = "select *  from typeintervention where nomTI='$nom'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$ti  = $resultat->fetch();
		$resultat->closeCursor();
		return $ti ;
	}

    // fonction qui permet de récupérer l'ensemble des types d'intervention
	function chercherToutTypeIntervention(){
		$connexion = getConnect();
		$requete = "select *  from typeintervention";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$ti  = $resultat->fetchAll();
		$resultat->closeCursor();
		return $ti ;
	}

	// fonction qui permet de modifié les informations d'une intervention dans la base de données
	function modifierIntervention($nomTI,$montant,$listePieces){
		$connexion = getConnect();
		$requete = " update typeintervention set  montant='$montant',listePieces='$listePieces' where nomTI='$nomTI'";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet de supprimer une intervention dans la base de données
	function supprimerIntervention($nom){
		$connexion = getConnect();
		$requete = " delete from typeintervention where nomTI='$nom'";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet de récupérer les informations d'un mécanicien selon son nom
	function getMecanicien($nom){
		$connexion = getConnect();
		$requete = "select nomEmploye from employe where categorie='mecanicien' and nomEmploye='$nom' ";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$mecanicien = $resultat->fetch();
		$resultat->closeCursor();
		return $mecanicien;
	}

	// fonction qui permet de récupérer l'ensemble des interventions d'un mécanicien
	function getInter($employe){
		$connexion = getConnect();
		$requete = "select * from intervention where nomMeca='$employe'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$inter = $resultat->fetchAll();
		$resultat->closeCursor();
		return $inter;
	}

	// fonction qui permet de récupérer les interventions et formations d'un employé un jour donné
	function getJournee($employe,$date){
		if(empty($date)){
			$date= date('Y-m-d');
		}
		$connexion = getConnect();
		$requete = "select nom,prenom,nomMeca,heureIntervention,nomTI,idClient,code,dateIntervention from client natural join intervention where
 nomMeca='$employe' and dateIntervention='$date' union select '','',nomEmploye,heureForm,'formation','','',dateForm from formation where nomEmploye='$employe' and dateForm='$date' order by heureIntervention";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$inter = $resultat->fetchAll();
		$resultat->closeCursor();
		return $inter;
	}

	// fonction qui permet de récupérer l'ensemble de toutes les formations d'un employé
	function getFormation($employe){
		$connexion = getConnect();
		$requete = "select * from formation where nomEmploye='$employe'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$formation = $resultat->fetchAll();
		$resultat->closeCursor();
		return $formation;
	}

	// Fonction qui permet de récupérer une formation  selon le mécanicien, la date et l'heure
	function getFormationParDateHeure($employe,$date,$heure){
		$connexion = getConnect();
		$requete = "select * from formation where nomEmploye='$employe' and dateForm='$date' and heureForm='$heure'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$formation = $resultat->fetch();
		$resultat->closeCursor();
		return $formation;
	}

	// fonction qui permet d'ajouter une formation à un mécanicien à une heure et date données dans la base de données
	function ajouterFormation($date,$heure,$employe){
		$connexion = getConnect();
		$requete = "INSERT INTO `formation`(dateForm,heureForm,nomEmploye) VALUES ('$date', '$heure','$employe')";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}

	// fonction qui permet de récupérer l'intervention selon son code et l'id du client
	function getInterventionParIdCode($id,$code){
		$connexion = getConnect();
		$requete = "select * from typeintervention NATURAL JOIN intervention where code='$code' and idClient='$id' ";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$inter = $resultat->fetch();
		$resultat->closeCursor();
		return $inter;
	}

	// fonction qui permet de récupérer une intervention selon le mécanicien, la date et l'heure
	function getInterventionParDateHeure($nomMecano,$date,$heureIntervention){
		$connexion = getConnect();
		$requete = "select * from intervention  where nomMeca='$nomMecano' and dateIntervention='$date' and heureIntervention='$heureIntervention' ";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$inter = $resultat->fetch();
		$resultat->closeCursor();
		return $inter;
	}

	// fonction qui permet de récupérer l'ensemble des mécaniciens
	function getToutLesMecanos(){
		$connexion = getConnect();
		$requete = "select * from employe where categorie='mecanicien'";
		$resultat = $connexion->query($requete);
		$resultat->setFetchMode(5);
		$mecanos = $resultat->fetchAll();
		$resultat->closeCursor();
		return $mecanos;
	}

	// fonction qui permet d'ajouter un rdv avec le nom de l'intervention, la date, l'heure, le mécanicien et l'id du client dans la base de données
	function prendreRdv($nomTI,$date,$heureIntervention,$nomMecano,$idClient){
		$connexion = getConnect();
		$requete = "INSERT INTO `intervention`(nomTI,dateIntervention,heureIntervention,nomMeca,idClient,etat) VALUES 
					('$nomTI','$date','$heureIntervention','$nomMecano','$idClient','en attante de payment')";
		$resultat = $connexion->query($requete);
		$resultat->closeCursor();
	}