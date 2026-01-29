# 📝 Rapport des Modifications - Projet Seignobos

Ce document synthétise l'ensemble des travaux réalisés sur le projet, incluant la configuration système, l'authentification, la modernisation de l'interface (UI/UX) et les corrections techniques.

## 1. 🎨 Interface Utilisateur (UI) & Expérience (UX)
Modernisation globale pour respecter l'identité visuelle de l'entreprise (Orange) et améliorer l'ergonomie.

- **Styles & Design System**
  - `style.css` : Intégration de la charte graphique (Orange `#f5a623`/`#f76b1c`) et de la police professionnelle **Poppins**.
  - `dashboard.css` : Nettoyage des styles globaux (`box-sizing`), suppression du soulignement des liens de navigation et amélioration de la sidebar.
  - `dashboard.php` : Intégration des CDN (FontAwesome, Google Fonts) et mise à jour de la structure de navigation avec des icônes intuitives.

- **Page d'Accueil**
  - `page_accueil.php` : Mise en place de **cartes statistiques horizontales** (Nouveaux projets, En cours, Total) et remplacement des émojis textuels par des icônes FontAwesome vectorielles.

## 2. 🚀 Gestion des Projets (Améliorations Fonctionnelles)
Optimisation des flux de travail pour éviter les rechargements de page inutiles.

- **Création de Projet (AJAX)**
  - `new_project.php` : Refonte du formulaire (padding corrigé, design épuré) et passage en **AJAX** pour la soumission (plus de redirection forcée vers l'accueil).
  - `create_project_action.php` (Nouveau) : Script dédié au traitement asynchrone de la création de projet.

- **Consultation & Édition**
  - `view_project.php` : Modernisation de l'interface (Tableau responsive, boutons stylisés), intégration de la police Poppins et ajout d'un bouton de retour.
  - `edit_project.php` : Harmonisation graphique (Police, couleurs) et ajout d'un bouton "Retour" pour améliorer la navigation.
  - `projects_list.php` : Isolation des styles CSS spécifiques pour éviter les conflits avec la navigation globale.

## 3. 🔐 Authentification & Sécurité
Mise en place d'un système robuste de gestion des accès et de récupération de compte.

- **Connexion & Session**
  - `login_action.php` : Correction critique de la gestion de session (sauvegarde complète des informations utilisateur).
  - `index.php` : Mise en place d'une redirection automatique vers le tableau de bord pour les utilisateurs connectés.

- **Mot de Passe Oublié (Nouveau Système)**
  - `forgot_password_action.php` : Logique d'envoi de code de vérification à 6 chiffres (avec expiration).
  - `verify_code.php` & `verify_code_action.php` : Interface et logique de vérification du code.
  - `new_password.php` & `new_password_action.php` : Formulaire et enregistrement sécurisé du nouveau mot de passe.

## 4. ⚙️ Configuration & Système
Mise en place de l'infrastructure technique.

- **Base de Données & Mail**
  - `config.php` : Configuration de la connexion PDO et intégration du serveur SMTP (Brevo) pour les emails.
  - `setup_db.php` : Script d'importation et de mise à jour de la structure de la base de données.
  - `.gitignore` (Nouveau) : Exclusion des fichiers sensibles (`config.php`) et des dépendances (`vendor/`) pour la sécurité du dépôt Git.

## 5. 👥 Gestion des Utilisateurs
- `manage_user.php` : Refonte esthétique du tableau de gestion, ajout d'icônes d'action (suppression) et harmonisation des polices.
- `profile_form_include.php` : Ajout d'icônes sur les boutons d'action (Mise à jour, Déconnexion) et correction des largeurs de champs.

## 6. 🧹 Corrections & Nettoyage Code
Stabilisation du code pour éviter les erreurs PHP.

- **Correction des Inclusions**
  - Remplacement de `require` par `require_once` pour éviter les erreurs de redéfinition multiple dans :
    - `profile_form_include.php`
    - `new_project.php`
    - `ongoing_projects.php`
    - `projects_list.php`
