# Guide d'Installation et de Déploiement - Projet Seignobos

Ce guide est destiné aux développeurs souhaitant installer et lancer le projet sur un environnement WAMP local.

## 1. Prérequis

*   **WAMP Server** (Apache, PHP 8.2+, MySQL 8.0+)
*   **Composer** (Gestionnaire de dépendances PHP)
*   **Git**

## 2. Installation

### Cloner le projet
Récupérez le code source depuis le dépôt Git :
```bash
git clone <url_du_repo>
cd seignobos
```

### Installer les dépendances
Ouvrez un terminal dans le dossier du projet et lancez :
```bash
composer install
```
*Note : Si vous n'avez pas Composer, le dossier `vendor` n'est pas inclus dans le dépôt par défaut. Il est impératif de l'installer pour que l'export Word et l'envoi d'emails fonctionnent.*

### Configuration de la Base de Données
1.  Ouvrez **phpMyAdmin** (http://localhost/phpmyadmin).
2.  Créez une nouvelle base de données nommée `seignobos_db`.
3.  Importez le fichier SQL situé dans `BD/127_0_0_1.sql`.
    *   Ce fichier contient la structure complète et les données de test.

### Configuration du Projet
1.  Le fichier `config.php` est inclus dans le dépôt. Vérifiez que les identifiants de base de données correspondent à votre installation WAMP (par défaut `root` sans mot de passe).
2.  Si nécessaire, modifiez `DB_USER` et `DB_PASS` dans `config.php`.

## 3. Lancement

1.  Déplacez le dossier du projet dans votre répertoire `www` de WAMP (ex: `c:\wamp64\www\seignobos`).
2.  Accédez au projet via votre navigateur :
    *   [http://localhost/seignobos](http://localhost/seignobos)

## 4. Connexion (Comptes Test)

Utilisez les comptes suivants pour vous connecter (Mots de passe réinitialisés pour le développement) :

*   **Administrateur** :
    *   Username : `Mia`
    *   Email : `mialisoa3@gmail.com`
    *   Mot de passe : `password123`
*   **Assistant** :
    *   Username : `Bee`
    *   Email : `alienor.grandemange@hotmail.fr`
    *   Mot de passe : `password123`

## 5. Fonctionnalités Clés à Tester

*   **Liste des projets** : Vérifiez l'affichage des boutons d'action.
*   **Export Word** : Testez la génération de draft (nécessite `vendor`).
*   **Réinitialisation de mot de passe** : Nécessite une configuration SMTP valide dans `config.php` (Brevo est configuré par défaut).

## 6. Structure des Dossiers Importants

*   `BD/` : Contient le dump SQL.
*   `uploads/` : Stocke les fichiers uploadés par les utilisateurs (images, PDF).
*   `vendor/` : Bibliothèques tierces (PHPOffice, PHPMailer).
