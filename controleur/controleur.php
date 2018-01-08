<?php
	require_once("vue/vue.php");
	require_once("modele/modele.php");

	// Fonction de controle de l'acceuil
	function ctlAcceuil()
	{
		afficherAccueil(); // appelle la fonction pour afficher l'acceuil de site
	}

	/*
	 * Liste de toutes les Erreurs
	 * Cela nous permet de gérer les différentes erreurs
	 */
	class ExceptionMontantDepasse extends Exception{} // Erreur sur le montant de différé dépassé selon le montant max du client
	class ExceptionLogin extends Exception{} // Erreur sur la connection d'un Employé
	class ExceptionFormation extends Exception{} // Erreur sur la prise d'une Formation d'un mécanicien
	class ExceptionPriseRdv extends Exception{} // Erreur sur la prise d'un rdv pour une intervention
	class ExceptionMontantNegatif extends Exception{} // Erreur sur le Montant
	class ExceptionIdNonTrouveGF extends Exception{} // Erreur sur l'id d'un client
	class ExceptionTypeInterExiste extends  Exception{} // Erreur sur la création d'une nouveau type d'intervention
	class ExceptionIdNonTrouveSynthese extends Exception{} // Erreur sur l'id d'un client pour afficher sa synthèse
	class ExceptionClientNonTrouve extends Exception{} // Erreur sur la recherche de l'id d'un client
	class ExceptionClientExiste extends Exception{} // Erreur pour la création d'un nouveau client
	class ExceptionEmploye extends Exception{} // Erreur pour la recherche d'un employé
	class ExceptionEmployeExisteDeja extends Exception{} // Erreur pour la création d'un nouvel employé
	class ExceptionCategorie extends Exception{} // Erreur pour le choix d'une catégorie d'employé
    class ExceptionPayerDerniere extends Exception{} // Erreur sur le paiement de la dernière intervention
    class ExceptionPayerInter extends Exception{} // Erreur sur le choix des interventions
    class ExceptionMofifierClient extends Exception{} // Erreur sur la modification d'un client

    // Fonction de controle pour le paiement de la dernière intervention
	function ctlPayerDerniere()
	{
		$int = getInterEnAttente($_SESSION['client']->idClient);
		if (!empty($int)) {
			payerInter($int[0]->code); // appelle la fonction pour changer l'état de l'intervention en 'paye'
		} else {
            throw new ExceptionPayerDerniere("Paiement impossible"); // sinon renvoie une erreur
        }
	}

    // Fonction de controle pour le paiement d'un ensemble d'intervention
	function ctlPayerInter()
	{
		 // parcours l'ensemble des interventions choisies
            if(!empty($_POST['checkInter'])){
                foreach ($_POST['checkInter'] as $inter) {
                    payerInter($inter); // appelle la fonction pour changer l'état de l'intervention en 'paye'

                }
            }else
                throw new ExceptionPayerInter("Aucune intervention choisie"); // sinon renvoie une erreur



	}

	// Focntion de controle pour le paiement en différé d'un ensemble d'intervention
	function ctlDiffererInter($checkInter)
	{
		$montant = 0;
		$codes = [];
		foreach ($checkInter as $inter) {
			//parcours de tous les checkbox et recuperer ceux en attente
			if (!empty($interEnAtt = ctlGetEnAttente($inter))) {
				$montant = $montant + $interEnAtt->montant; // récupère la somme total des montants des interventions
				$codes[] = $inter;
			}
		}
        // test si le montant total des interventions est inferieur ou égal au montant différé max d'un client
		if ($_SESSION['client']->montantMax - ($_SESSION['diffEnCours'] + $montant) >= 0) {
			foreach ($codes as $inter) {
				differer($inter); // si oui, appelle la fonction pour changer l'état des interventions choisies en 'difere'
			}
		} else {
			throw new ExceptionMontantDepasse("Montant autorisé est depassé"); // sinon renvoie une erreur
		}
	}

	// Fonction de controle si une intervention est 'en attente de paiement' selon son id
	function ctlGetEnAttente($inter)
	{
		return getEnAttente($inter); // appelle la fonction qui cherche dans la base de données
	}

    // Fonction de controle qui permet d'afficher la page correspondante d'un employe
	function ctlAfficherPageCorrespondante($login, $motdepasse)
	{
	    // appelle la fonction de controle de connection afin d'avoir ses informations et afficher sa page correspondantes selon sa catégorie
		$employe = ctlChercherIdentifiantsEmploye($login, $motdepasse);
		$_SESSION['empl'] = $employe; // stock ses informations dans une variable session
		switch ($employe->categorie) {
			case'agent':
			    // si c'est un agent, la fonction qui affiche l'acceuil d'un agent est appelé
				afficherAccueilAgent($employe,getToutLesMecanos(),chercherToutTypeIntervention());
				break;
			case'mecanicien':
                // si c'est un mécanicien, la fonction qui affiche l'acceuil d'un mécanicien est appelé
				afficherAccueilMecanicien(getMecanicien($_SESSION['empl']->nomEmploye),getToutLesMecanos());
				break;
			case'directeur':
                // si c'est le directeur, la fonction qui affiche l'acceuil du directeur est appelé
				afficherAccueilDirecteur($employe);
				break;
		}
	}

    // Fonction de controle pour la recherche du planning d'un mécanicien d'une semaine
	function ctlSetPlaningMecanos($nomMecano,$dateDebutSemaine){
			//cherche le planning de mecanicien pour chaque jour
		// de la semaine a partir de la date passe en parametre

			//creation d'un objet DateTime modifiable
			$date=  new \DateTime(date('Y-m-d', strtotime($dateDebutSemaine)));
			$jours=array();
			for ($i=0; $i<7;$i++){
			    // appelle la fonction qui va renvoyer le planning d'une journée
                // on fait ça pour tous les jours de la semaine
				$x=getJournee($nomMecano,$date->format('Y-m-d'));
				$jours[$i]=$x;
				//le jour suivant
				$date->modify('+ 1 days');
			}
        // on stock le planning d'une semaine du mécanicien dans une variable
		$_SESSION['PlaningSemaineMecano']=$jours;
	}



    // Fonction de controle de connection d'un employé
	function ctlChercherIdentifiantsEmploye($login, $motdepasse)
	{
	    // appelle la fonction qui recherche le compte d'un employé selon son login et mot de passe
		if ($employe = getEmploye($login, $motdepasse)) {
		    // si oui, renvoie toutes les informations de l'employé
			return $employe;
		} else {
		    // si non, renvoie une erreur
			throw  new ExceptionLogin("Login ou mot de passe incorrect");
		}
	}

	// Fonction de controle qui affiche la gestion financière d'un client si l'id est valide
	function ctlGestionFinanciere($id)
	{
	    // on récupère le client en question et on stock ses informations dans une variable
		if ($client = ctlGetClient($id)) {
			$_SESSION['client'] = $client;
			// on récupère toutes ses interventions en 'differe'
			$diff = getInterDiff($id);
			// on récupère toutes ses interventions en 'attente de paiement'
			$enatt = getInterEnAttente($id);
			$sommediff=0;
			// calcul de la somme du montant des interventions en 'differe'
			foreach ($diff as $intd) {
				$sommediff += $intd->montant;
			}
			$_SESSION['diffEnCours'] = $sommediff;
			// appelle la fonction qui affiche la gestion financière du client avec les interventions en 'attente de paiement' et en 'differe'
			afficherGestionFinanciere($diff, $enatt);
		} else {
		    // si l'id du client n'est pas valide, on renvoie une erreur
			throw new ExceptionIdNonTrouveGF("Id non trouvé");
		}
	}

	// Fonction de controle pour mettre à jour les informations d'un client
	function ctlMettreAJourClient($infos)
	{
		$modifs=array();
		$client =(array)$_SESSION['client'];
		// parcours les informations du clients et test si celles si sont modifiés
		foreach($infos as $key=>$val){
			if($key != 'modifierClient' && $val != $client[$key]){
				$modifs[$key]=$val;
                // si modifié on stock les informations changé dans un tableau
			}
		}
		// ensuite on appelle, si une info a été modifié, la fonction qui va modifier les informations du clients dans la base de données
		if(!empty($modifs)){
			modifierClient($client['idClient'],$modifs);
            $_SESSION['modifierClient']='Les informations ont bien été mises à jour';
		}else {
            throw new ExceptionMofifierClient("Aucune modification effectuée"); // sinon renvoie une erreur
        }
	}

	// Fonction de controle du planning d'un mécanicien
	function ctlPlanningUnMecano($nom,$date){
	    // appelle la fonction qui permet de récupérer le planing d'une journée d'un mécanicien
		$j=getJournee($nom,$date);

		// appelle la fonction qui va afficher le planning du mécanicien
	    afficherPlanningMecanicien(getMecanicien($nom),$j);
	}

	// Fonction de controle de la synthèse d'un client
	function ctlSyntheseClient($id)
	{
	    // appelle la fonction de controle de l'id du client
        // si celle-ci est valide on va stocké ses informations dans une variable session
		if($client=ctlGetClient($id)) {
			$_SESSION['client'] = $client;
			// si l'utilisateur est un agent, on va stocker les interventions passées du client dans une variable
			if($_SESSION['empl']->categorie=='agent')
			    $interventions = getInterventionsPasses($id);
			// si l'utilisateur est un mécanicien, on va stocker toutes les interventions du client
			elseif($_SESSION['empl']->categorie=='mecanicien')
				$interventions = getInterventionParIdCode($id,$_POST['code']);
			// appelle de la fonction qui nous permet de récupérer les interventions en 'differe' du client
			$diff = getInterDiff($id);
			$sommediff = 0;
			// calcul de la somme du montant des interventions en 'differe'
			foreach ($diff as $intd) {
				$sommediff += $intd->montant;
			}
			// appelle de la fonction d'affichage de la synthèse d'un client
			afficherSynthese($client,$interventions,$sommediff,$client->montantMax-$sommediff);
		}
		// renvoie une erreur si l'id du client n'est pas valide
		else throw new ExceptionIdNonTrouveSynthese("Id non trouvé");
	}


	// Fonction de controle pour récupérer un client avec son id
	function ctlGetClient($id)
	{
	    // appelle le fonction qui va cherché le client dans la base de données
		$client = getClient($id);
		// si trouvé on renvoie le client, false sinon
		return $client ? $client : false;
	}


	// Fonction de controle pour trouver l'id d'un client selon sa date de naissance et son nom
	function ctlGetIdClient($nom, $dateNaiss)
	{
	    // appelle de la fonction qui va rechercher les informations du client dans la base de données
		if ($client = getIdClient($nom, $dateNaiss)) {
			$_SESSION['rechercheIdClient'] = $client;
		} else {
		    // renvoie une erreur si non trouvé
			throw new ExceptionClientNonTrouve("Aucun client trouvé.");
		}
	}

    // Fonction de controle pour ajouter un client
	function ctlAjouterClient(){
	    // on stock les informations du nouveau client dans un tableau
		$infos = array();
		// on ajoute les informations dans le tableau avec la clé de l'information et sa valeur
		foreach ($_POST as $key => $val) {
			if ($key != 'ajouterClient') {
				if ($key == 'dateNaiss') {
					$infos[$key] = date($val);
				} else {
					$infos[$key] = $val;
				}
			}
		}
		// appelle la fonction de controle qui test si le client existe déjà
		if(!ctlExisteClient($infos['nom'],$infos['prenom'],$infos['dateNaiss'])){
		    // si le client n'existe pas, on appelle le la fonction qui va ajouter le client dans la base de données
			ajouterClient($infos);
			$_SESSION['nouveauClient']='Nouveau Client '.$infos['nom'].' a bien été ajouté';
		}
		// renvoie une erreur si le client existe déjà
		else throw new ExceptionClientExiste("Le client existe déjà.");
	}

    // Fonction de controle qui test si un client existe déjà
	function ctlExisteClient($nom,$prenom,$date){
	    // renvoie true or false
		return !empty($client=existeClient($nom,$prenom,$date));
	}

	// Fonction de controle pour la création d'un nouvel employé
	function ctlCreerCompte()
	{
	    // on vérifie si la catégorie correspond bien à une des catégories existantes
		if (in_array($_POST['categorie'], array("mecanicien", "directeur", "agent"))) {
		    // on verifie si l'employe n'existe pas
			if ($empl = chercherEmploye($_POST['nomEmploye'], $_POST['login']) == null) {
			    // appelle la fonction qui va créer un compte dans la base de données
				creerCompte($_POST['nomEmploye'], $_POST['login'], $_POST['motDePasse'], $_POST['categorie']);
				// variable qu'on va afficher afin de montrer que un compte à bien été créé
				$_SESSION['nouveauCompte']='Nouveau compte de '.$_POST['nomEmploye'].' a été créé';
			} else {
			    // si l'employe existe déjà on renvoie une erreur
				throw new ExceptionEmployeExisteDeja("Employe avec ce nom ou login existe deja");
			}
		} else {
		    // si la catégorie n'est pas valide on renvoie une erreur
			throw new ExceptionCategorie("Categorie non autorise");
		}
	}

    // Fonction de controle qui va chercher tous les employes
	function ctlChercherToutLesEmploye(){
	    // appelle de la fonction qui va renvoyer tous les employes présent dans la base de données
		return  chercherToutLesEmploye();
	}

    // Fonction de controle pour chercher un employe selon son nom
	function ctlChercherUnEmploye($nom){
	    // test si $nom n'est pas vide, sinon renvoie une erreur
		if(empty($nom))
			throw new ExceptionEmploye("Veuillez entrer le nom d'employe");
		// test si l'employe existe, si oui, on renvoie ses informations
		if(($employe=chercherUnEmploye($nom))!=null){
			return $employe;
		}
		else{
		    // si l'employe n'existe pas, on renvoie une erreur
			throw new ExceptionEmploye("Pas d'employe avec ce nom");
		}
	}

    // Fonction de controle pour modifier les informations d'un employe
	function ctlModifierEmploye(){
		$modifications='';
		// on stock toutes les informations d'un employé
		$employe=(array)chercherUnEmploye($_POST['nomEmploye']);
		foreach ($employe as $cle => $valeur)
		    // on remplace les informations selon sa clé
			if(!empty($_POST[$cle])){
				if($_POST[$cle]!=$valeur){
				    // appelle de la fonction qui va modifier les informations dans la base de données
					modifierEmploye($cle,$_POST[$cle],$_POST['nomEmploye']);
					$modifications .=ucfirst($cle).' de '.$_POST['nomEmploye'].' a ete modifie. ';
				}
                if(!empty($_SESSION['TousLesEmploye'])){
                    unset($_SESSION['TousLesEmploye']);
                }

                $_SESSION['EmployeDirecteur']=chercherUnEmploye($_POST['nomEmploye']);
                if($_SESSION['empl']->nomEmploye==$_POST['nomEmploye']){
                    //si on a modifié notre propre compte, le mettre à jour
                    $_SESSION['empl']=$_SESSION['EmployeDirecteur'];
                }
                // affiche un message que l'employe a bien été modifié ou non
				if(!empty($modifications))
					$_SESSION['EmployeMidifie']='Employe a bien été mofifie. '.$modifications;
				else
					$_SESSION['EmployeMidifie']='Aucun changement n\'a été éffectué';

			}else {
			    // renvoie une erreur si l'un des champs des informations de l'employé est vide
				throw new ExceptionEmploye("L'un des champs est vide veuillez garder tout les champs remplis");
			}

	}

    // Fonction de controle qui permet de chercher les types des interventions
	function ctlChercherTypesIntervention(){
	    // appelle de la fonction qui va chercher tous les types d'intervention dans la base de données
		return chercherToutTypeIntervention();
	}

	// Fonction de controle pour supprimer un employe
	function ctlSupprimerEmploye(){
		if($_POST['nomEmploye']==$_SESSION['empl']->nomEmploye){
		    // envoie un message d'erreur si l'utilisateur veut supprimer son propre compte
			throw new ExceptionEmploye("Vous pouvez pas supprimer votre propre compte sinon vous etes coincé");
		}

		else{
		    // appelle de la fonction qui va supprimer l'employé dans la base de données
			supprimerEmploye($_POST['nomEmploye']);
			// affiche un message que l'employé a bien été supprimé
			$_SESSION['EmployeSupprime']=$_POST['nomEmploye'].' a bien ete supprimé.';
			unset($_SESSION['TousLesEmploye']);
		}

	}

    // Fonction de controle pour modifier une intervention
	function ctlModifierIntervention(){
		$_SESSION['InterModifie']=$_POST['nomTI'];
		if($_POST['montant']<0){
		    // renvoie une erreur si le montant n'est pas valide
			throw new ExceptionMontantNegatif("Le montant doit être positif");
		}
		// appelle de la fonction qui va modifier l'intervention dans la base de données
		modifierIntervention($_POST['nomTI'],$_POST['montant'],$_POST['listePieces']);

	}

    // Fonction de controle pour supprimer une intervention
	function ctlSupprimerIntervention(){
	    // appelle de la fonction qui va supprimer l'intervention dans la base de données
		supprimerIntervention($_POST['nomTI']);
	}

    // Fonction de controle pour créer une intervention
	function ctlCreerIntervention(){
	    // test si l'intervention que l'on veut créer existe déjà
		if(($intervention=chercherTypeIntervention($_POST['nomTI']))!=null){
		    // renvoie une erreur si le nom est déjà utilisé
			throw new ExceptionTypeInterExiste("Intervention avec ce nom existe deja");
		}else{
		    // appelle la fonction qui va créer une intervention dans la base de données
			creerTypeIntervention($_POST['nomTI'],$_POST['montant'],$_POST['listePieces']);
		}
	}

	// Fonction de controle qui va afficher la journée d'un mécanicien
	function ctlJournee($mecanicien){
	    // appelle la fonction d'affichage d'une journée d'un mécanicien
		afficherJournee(getJournee($mecanicien));
	}

    // Fonction de controle pour ajouter une formation au planning d'un mécanicien
	function ctlFormation($date,$heure){
        $aujr = new DateTime(date('Y-m-d'));
        $date2 = new DateTime($date);
        //teste si la date de rdv est passée
        if($aujr>$date2){
            throw new ExceptionFormation("La date est passée");
        }
	    // test si la formation se trouve bien dans ses horaires
		if($heure<8||$heure>19)
		    // sinon renvoie une erreur
			throw new ExceptionFormation("heure doit etre entre 8 et 19 h, sinon je viens pas :p");
		$employe=$_SESSION['empl']->nomEmploye;
		// on récupère les interventions et les formations du mécanicien connecté
		$inter=getInter($employe);
		$formation=getFormation($employe);
		// test si l'heure de la formation que l'on veut prendre est possible
		foreach ($inter as $i){
			if ($i->dateIntervention == $date && $i->heureIntervention == $heure){
                // renvoie une erreur si l'heure est déjà prise
				throw new ExceptionFormation("Formation impossible (Intervention ou Formation déjà présente)");
			}
		}
		foreach ($formation as $f){
			if ($f->dateForm == $date && $f->heureForm == $heure){
			    // renvoie une erreur si l'heure est déjà prise
				throw new ExceptionFormation("Formation impossible (Intervention ou Formation déjà présente)");
			}
		}
		// si toutes les contraintes sont évitées, on appelle la fonction qui va ajouter une formation au mécanicien dans la base de données
		ajouterFormation($date,$heure,$employe);
		$_SESSION['formationInsere']='Formation a été ajouté le '.$date.' à '.$heure;
	}

    // Fonction de controle pour prendre un rdv au garage
	function ctlPrendreRendezVous($nomTI,$date,$heureIntervention,$nomMecano,$idClient)
    {

        $aujr = new DateTime(date('Y-m-d'));
        $date2 = new DateTime($date);
        //teste si la date de rdv est passée
        if($aujr>$date2){
            throw new ExceptionPriseRdv("La date est passée");
        }

        // test si l'heure de l'intervention est bien comprise dans les heures des mécaniciens
        if ($heureIntervention > 19 || $heureIntervention < 8) {
            throw new ExceptionPriseRdv("Tout les mecaniciens travaillent entre 8 et 19 h");
        }
        // test si le mécanicien n'effectue pas une formation ou bien une intervention pour l'heure choisie
        if (($form = getFormationParDateHeure($nomMecano, $date, $heureIntervention)) != null) {
            throw new ExceptionPriseRdv("Il existe deja une formation a cette heure pour ce mecanicien");
        }
        if (($interv = getInterventionParDateHeure($nomMecano, $date, $heureIntervention)) != null) {
            throw new ExceptionPriseRdv("Il existe deja une intervention a cette heure pour ce mecanicien");
        }
        // si toutes les contraintes sont évitées, on appelle la fonction qui va ajouter un rdv dans la base de données
        prendreRdv($nomTI, $date, $heureIntervention, $nomMecano, $idClient);
        $intercree = getInterventionParDateHeure($nomMecano, $date, $heureIntervention);

        $_SESSION['succesRendezVousListePieces'] = 'Pieces a fournir: ' . getInterventionParIdCode($idClient, $intercree->code)->listePieces;
    }

    // Fonction de controle des erreurs de connection
	function CtlErreur($erreur)
	{
	    // appelle la fonction qui va afficher les erreurs de connection
		afficherErreurLogin($erreur);
	}