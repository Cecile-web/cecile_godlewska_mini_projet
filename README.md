# Projet Mini - Gestion de membres

## Description

Ce projet est une application PHP simple pour gérer l'accès à un tableau de bord et ajouter des membres. Il utilise une base de données MySQL et se compose des fichiers principaux suivants :

- `connexion.php` : page de connexion pour l'accès.
- `tableau_de_bord.php` : page protégée visible après authentification.
- `ajouter_membre.php` : formulaire pour ajouter un nouveau membre.
- `modifier_membre.php` : formulaire de modification des informations d'un membre.
- `logout.php` : déconnexion de l'utilisateur.
- `BDD/company_cecile (1).sql` : export SQL de la base de données.

## Prérequis

Avant d'installer le projet, assurez-vous d'avoir :

- PHP installé (version 7.4+ recommandée).
- MySQL ou MariaDB installé.
- Un serveur local comme WAMP, XAMPP ou MAMP.
- Un navigateur web.

## Installation

1. Placer le dossier du projet dans le répertoire du serveur local.
   - Exemple WAMP : `C:\wamp64\www\cecile_godlewska_mini_projet`

2. Démarrer Apache et MySQL via votre panneau de contrôle WAMP/XAMPP.

3. Importer la base de données MySQL :
   - Ouvrir phpMyAdmin.
   - Créer une nouvelle base de données (par exemple `company_cecile`).
   - Importer le fichier SQL situé dans `BDD/company_cecile (1).sql`.

4. Vérifier la configuration de la connexion à la base de données :
   - Ouvrir éventuellement le fichier `connexion.php`.
   - Adapter l'hôte, l'utilisateur, le mot de passe et le nom de la base si nécessaire.

5. Accéder à l'application dans le navigateur :
   - `http://localhost/cecile_godlewska_mini_projet/connexion.php`

## Usage

1. Se connecter en utilisant les identifiants définis dans la base de données.
2. Une fois connecté, vous arriverez sur le `tableau_de_bord.php`.
3. Pour ajouter un membre, utiliser `ajouter_membre.php`.
4. Pour modifier un membre, cliquer sur le lien "Modifier" dans le tableau du `tableau_de_bord.php`.
5. Pour se déconnecter, cliquer sur le lien ou accéder à `logout.php`.

## Remarques

- Vérifiez les permissions du serveur si vous rencontrez des problèmes d'accès.
- Si la page de connexion ne s'affiche pas, assurez-vous que votre serveur Apache fonctionne.
- En cas d'erreur de base de données, vérifiez la configuration du fichier `connexion.php`.

## Aide

Pour toute question ou amélioration, vous pouvez modifier directement les fichiers PHP et relancer votre serveur local.
