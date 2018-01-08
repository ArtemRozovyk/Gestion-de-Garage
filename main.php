<?php
	try {
		require_once("controleur/controleur.php");
		session_start();

		// test si le bouton 'connexion' a été cliqué
		if (isset($_POST['connexion'])) {
		    // si oui, appelle de la fonction de controle de connection
			ctlAfficherPageCorrespondante($_POST['login'], $_POST['motdepasse']);

		}
		// test si le bouton 'gestionF' a été cliqué
		elseif (isset($_POST['gestionF'])) {
		    // si oui, appelle de la fonction de controle de gestion financière
			ctlGestionFinanciere($_POST['idclientGF']);

		}
		// test si le bouton 'payerDer' a été cliqué
		elseif (isset($_POST['payerDer'])) {
		    // si oui, appelle de la fonction de controle paiement de la dernière intervention
			ctlPayerDerniere();
			ctlGestionFinanciere($_SESSION['client']->idClient);

		}
        // test si le bouton 'payer' a été cliqué
        elseif (isset($_POST['payer'])) {
		    // si oui, appelle de la fonction de controle de paiement des interventions choisies
			ctlPayerInter();
			ctlGestionFinanciere($_SESSION['client']->idClient);
		}
        // test si le bouton 'differ' a été cliqué
        elseif (isset($_POST['differer'])) {
		    // si oui, appelle de la fonction de oontrole de paiement en differer
			ctlDiffererInter($_POST['checkInter']);
			ctlGestionFinanciere($_SESSION['client']->idClient);

		}
        // test si le bouton 'rechercheID' a été cliqué
        elseif (isset($_POST['rechercheID'])) {
		    // si oui, appelle de la fonction de controle de recherche de l'id d'un client
			$nom = $_POST['nomclient'];
			$dateNaiss = date($_POST['dateNaiss']);
			ctlGetIdClient($nom, $dateNaiss);
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'deco' a été cliqué
        elseif (isset($_POST['deco'])) {
		    // si oui, appelle la fonction de controle de l'acceuil
			session_destroy(); // efface les variables session en cours
			ctlAcceuil();
		}
        // test si le bouton 'acceuil' a été cliqué
        elseif (isset($_POST['accueil'])) {
		    // si oui, appelle de la fonction de controle pour afficher la page correspondantes à l'utilisateur
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'synthese' a été cliqué
        elseif (isset($_POST['synthese'])) {
		    // si oui, appelle de la fonction de controle de la synthèse d'un client
			ctlSyntheseClient($_POST['idclient']);
		}
        // test si le bouton 'modifierClient' a été cliqué
        elseif (isset($_POST['modifierClient'])) {
		    // si oui, appelle de la fonction de controle de modification d'un client
			ctlMettreAJourClient($_POST);
			ctlSyntheseClient($_SESSION['client']->idClient);
		}
        // test si le bouton 'creerCompte' a été cliqué
        elseif (isset($_POST['creerCompte'])) {
		    // si oui, appelle de la fonction de controle pour la création d'un compte
			ctlCreerCompte($_POST['nomEmploye'], $_POST['login'], $_POST['motDePasse'], $_POST['categorie']);
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'ajouterClient' a été cliqué
        elseif (isset($_POST['ajouterClient'])) {
            // si oui, appelle de la fonction de controle d'ajout d'un client
			ctlAjouterClient();
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'afficherToutLesComptes' a été cliqué
        elseif (isset($_POST['afficherToutLesComptes'])) {
		    // si oui, appelle de la fonction de controle qui affiche tous les employés
			$_SESSION['TousLesEmploye']=ctlChercherToutLesEmploye();
			// et les affiches sur la page correspondante de l'utilisateur
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
        }
        // test si le bouton 'chercherEmployeParNom' a été cliqué
        elseif (isset($_POST['chercherEmployeParNom'])) {
		    // si oui, appelle de la fonction de controle de recherche d'un employe par son nom
			$_SESSION['EmployeDirecteur']=ctlChercherUnEmploye($_POST['nomEmploye']);
			// et l'affiche sur la page correspondante de l'utilisateur
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'modifierEmploye' a été cliqué
        elseif (isset($_POST['modifierEmploye'])) {
		    // si oui, appelle de la fonction de controle de la modification d'un employe
			ctlModifierEmploye();
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'supprimerEmploye' a été cliqué
        elseif (isset($_POST['supprimerEmploye'])) {
		    // si oui, appelle de la fonction de controle de suppression d'un employe
			ctlSupprimerEmploye();
			//si il est pas directeur;
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'afficherToutLesComptes' a été cliqué
        elseif (isset($_POST['creerIntervention'])) {
		    // appelle de la fonction de controle de creation d'un employe
			ctlCreerIntervention();
			//si il est pas directeur;
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'afficherToutLesTypeIntervention' a été cliqué
        elseif (isset($_POST['afficherToutLesTypeIntervention'])) {
            // si oui, appelle de la fonction de controle de recherche des types d'intervention
			$_SESSION['TypesDIntervention']=ctlChercherTypesIntervention();
			//si il est pas directeur;
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'modifierIntervention' a été cliqué
        elseif (isset($_POST['modifierIntervention'])) {
            // si oui, appelle de la fonction de controle de modification d'une intervention
			ctlModifierIntervention();
			$_SESSION['TypesDIntervention']=ctlChercherTypesIntervention();

			//si il est pas directeur;
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'supprimerIntervention' a été cliqué
        elseif (isset($_POST['supprimerIntervention'])) {
            // si oui, appelle de la fonction de controle de suppression d'une intervention
			ctlSupprimerIntervention();
			//si il est pas directeur;
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'syntheseDirect' a été cliqué
        elseif (isset($_POST['syntheseDirect'])) {
            // si oui, appelle de la fonction de controle de l'affichage de la synthèse d'un client
			 ctlSyntheseClient($_POST['idClient']);
		}
        // test si le bouton 'planningJournee' a été cliqué
        elseif (isset($_POST['planningJournee'])) {
            // si oui, appelle de la fonction de controle des intervention de la journée
			ctlInterJournee();
		}
        // test si le bouton 'saisirFormation' a été cliqué
        elseif (isset($_POST['saisirFormation'])) {
            // si oui, appelle de la fonction de controle de création d'une formation
			ctlFormation($_POST['dateFormation'],$_POST['heureFormation']);
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'afficherPlaningMecano' a été cliqué
        elseif (isset($_POST['afficherPlaningMecano'])) {
            // si oui, appelle de la fonction de controle d'affichage des planning des mécaniciens
			ctlPlanningUnMecano($_POST['meca'],$_POST['datePlanning']);
		}
        // test si le bouton 'synthese' a été cliqué
        elseif (isset($_POST['syntese'])) {
            // si oui, appelle de la fonction de controle de synthese d'un client
			ctlSyntheseClient($_POST['idClient']);
		}
        // test si le bouton 'planingMecanoSemaine' a été cliqué
        elseif (isset($_POST['planingMecanoSemaine'])) {
            // si oui, appelle de la fonction de controle de l'affichage du planning d'une semaine d'un mécanicien
			ctlSetPlaningMecanos($_POST['meca'],$_POST['semaines']);
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // test si le bouton 'priseRDV' a été cliqué
        elseif (isset($_POST['priseRDV'])) {
            // si oui, appelle de la fonction de controle pour la création d'une prise de rendez-vous
			ctlPrendreRendezVous($_POST['nomTI'],$_POST['dateRdvAPrendre'],$_POST['heureRdvAPrendre'],$_POST['meca'],$_POST['idClient'],$_POST['idClient']);
			ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
		}
        // à l'ouverture du site, appelle de la fonction de controle d'acceuil
        else {
			ctlAcceuil();
		}
        // catch de toutes les erreurs
	} catch (ExceptionLogin $e) { // erreur de connection
		$msg = $e->getMessage();
		CtlErreur($msg);
	} catch (ExceptionMontantDepasse $e) { // erreur du montant max differe dépasser
		$msg = $e->getMessage();
		$_SESSION['erreurMontant'] = $msg;
		// rappelle de la fonction de controle de la gestion financière
		ctlGestionFinanciere($_SESSION['client']->idClient);
	} catch (ExceptionPayerDerniere $e) { // erreur sur le paiement de la dernière intervention
        $msg = $e->getMessage();
        $_SESSION['erreurPayerDerniere'] = $msg;
        // rappelle de la fonction de controle de la gestion financière
        ctlGestionFinanciere($_SESSION['client']->idClient);
    } catch (ExceptionPayerInter $e) { // erreur lors de la saisie des interventions à payer
        $msg = $e->getMessage();
        $_SESSION['erreurPayerInter'] = $msg;
        // rappelle de la fonction de controle de la gestion financière
        ctlGestionFinanciere($_SESSION['client']->idClient);
    } catch (ExceptionMofifierClient $e) { // erreur, aucune modification effectué
        $msg = $e->getMessage();
        $_SESSION['erreurModifierClient'] = $msg;
        // rappelle de la fonction de controle de la synthèse client
        ctlSyntheseClient($_SESSION['client']->idClient);
    } catch (ExceptionClientNonTrouve $e) { // erreur de la recherche d'un client
		$msg = $e->getMessage();
		$_SESSION['erreurClient'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
		ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	} catch (ExceptionEmployeExisteDeja $e) { // erreur de création d'un employe
		$msg = $e->getMessage();
		$_SESSION['erreurExiste'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
		ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	} catch (ExceptionCategorie $e) { // erreur de catégorie d'un employé
		$msg = $e->getMessage();
		$_SESSION['erreurCat'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
        ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	} catch (ExceptionIdNonTrouveSynthese $e) { // erreur sur l'id d'un client pour afficher sa synthèse
		$msg = $e->getMessage();
		$_SESSION['erreurIdSynthese'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
        ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	} catch (ExceptionClientExiste $e) { // erreur sur l'ajout d'un nouveau client
		$msg = $e->getMessage();
		$_SESSION['erreurClientExiste'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
        ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	} catch (ExceptionIdNonTrouveGF $e) { // erreur sur l'id d'un client pour afficher sa gestion financière
		$msg = $e->getMessage();
		$_SESSION['erreurIdGF'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
        ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	}
	catch (ExceptionEmploye $e) { // erreur sur la recherche d'un employe
		$msg = $e->getMessage();
		$_SESSION['erreurChercherEmploye'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
        ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	}
	catch (ExceptionTypeInterExiste $e) { // erreur pour la création d'une intervention
		$msg = $e->getMessage();
		$_SESSION['erreurTypeInterExiste'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
        ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	}
	catch (ExceptionMontantNegatif $e) { // erreur sur le montant d'une intervention
		$msg = $e->getMessage();
		$_SESSION['TypesDIntervention']=ctlChercherTypesIntervention();
		$_SESSION['erreurMontantNegatif'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
        ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	}
	catch (ExceptionFormation $e) { // erreur sur la prise d'une formation
		$msg = $e->getMessage();
		$_SESSION['erreurFormation'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
        ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	}
	catch (ExceptionPriseRdv $e) { // erreur sur la prise d'un rdv
		$msg = $e->getMessage();
		$_SESSION['ErreurRendezVous'] = $msg;
        // rappelle de la fonction de controle de la page correspondante à l'utilisateur
        ctlAfficherPageCorrespondante($_SESSION['empl']->login, $_SESSION['empl']->motDePasse);
	}