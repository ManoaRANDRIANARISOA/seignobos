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
git clone https://github.com/ManoaRANDRIANARISOA/seignobos.git
cd seignobos
```

### Installer les dépendances
Ouvrez un terminal dans le dossier du projet et lancez :
```bash
composer install
```
*Note : Si vous n'avez pas Composer, le dossier `vendor` n'est pas inclus dans le dépôt par défaut. Il est impératif de l'installer pour que l'export Word et l'envoi d'emails fonctionnent.*

### Configuration de la Base de Données

**Option A : Installation complète (Nouvelle BD)**
1.  Ouvrez **phpMyAdmin** (http://localhost/phpmyadmin).
2.  Créez une nouvelle base de données nommée `seignobos_db`.
3.  Importez le fichier SQL le plus récent : `bd-new/127_0_0_1.sql` (Ce fichier contient les dernières données et la structure à jour avec reset_code).

**Option B : Mise à jour d'une BD existante (Pour les devs ayant déjà le projet)**
Si vous avez déjà la base de données mais qu'il vous manque les colonnes `reset_code` (Erreur "Column not found"), n'écrasez pas tout !
1.  Ouvrez **phpMyAdmin**.
2.  Sélectionnez votre base `seignobos_db`.
3.  Importez le fichier de patch : `BD/patch_add_reset_columns.sql`.
    *   Cela ajoutera uniquement les colonnes manquantes sans supprimer vos données.

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

*   `bd-new/` : Contient le dump SQL MAÎTRE (Structure + Données complètes).
*   `BD/` : Contient les patchs et anciennes versions.
*   `uploads/` : Stocke les fichiers uploadés par les utilisateurs (images, PDF).
*   `vendor/` : Bibliothèques tierces (PHPOffice, PHPMailer).
