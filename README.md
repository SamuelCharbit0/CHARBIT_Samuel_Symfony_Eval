**APPLICATION SYMFONY DE GESTION DE NOTES (AVEC DOCKER)**

**PRESENTATION DU PROJET**

Cette application est une application de gestion de notes développée avec Symfony, conçue pour être exécutée via Docker. Elle permet aux utilisateurs de :

- Créer un compte et se connecter.

- Gérer leurs notes : créer, modifier, supprimer et consulter.

- Avoir des permissions différentes selon le rôle : utilisateur standard ou administrateur.

Le projet est pensé pour être simple, pédagogique et facilement exécutable sur n’importe quel environnement grâce à Docker.

**ARCHITECTURE DOCKER**

Le projet utilise Docker Compose pour gérer l’environnement :
<img width="935" height="210" alt="image" src="https://github.com/user-attachments/assets/87c29dd5-391c-451e-8482-d846561b5076" />

**COMMANDES UTILES POUR DOCKER**

- Démarrer les conteneurs :
  - docker compose up -d

- Se connecter au conteneur PHP :
  - docker compose exec sf_php bash

- Créer la base de données :
  - php bin/console doctrine:database:create

- Exécuter les migrations :
  - php bin/console doctrine:migrations:migrate

- Se connecter à la base de données MySQL :
  - docker compose exec sf_db mysql -uroot -p

**FONCTIONNALITES PRINCIPALES**

**1. Authentification**

- Inscription (/register) :
  - Création d’un compte utilisateur.
  - Le mot de passe est hashé via Symfony.
  - Connexion automatique après inscription.
  - Redirection vers /note.
  - Si l’utilisateur est déjà connecté et tente d’accéder à /register, il est redirigé automatiquement vers /note.

- Connexion (/login) :
  - Connexion avec email et mot de passe.
  - Redirection vers /note si déjà connecté.

- Déconnexion (/logout) :
  - Accessible depuis toutes les pages (notes, accueil, login, register) quand l’utilisateur est connecté.

- Session persistante :
  - L’utilisateur reste connecté tant qu’il ne se déconnecte pas.

 **2. Gestion des notes**

Routes principales gérées par NoteController :

<img width="958" height="331" alt="image" src="https://github.com/user-attachments/assets/22da9dfb-90d9-4521-8ade-80e0ce3abc3d" />

- Les actions éditer/supprimer sont contrôlées via is_granted.
- Les pages affichent un bouton de déconnexion et un lien vers la page d’accueil si nécessaire.

 **3. Pages principales**

- Page d’accueil (/) :
  - Affiche login/register si non connecté.
  - Affiche un lien vers /note et bouton de déconnexion si connecté.

- Page login (/login) :
  - Formulaire de connexion.
  - Lien vers page register si utilisateur non connecté.

- Page register (/register) :
  - Formulaire de création de compte.
  - Lien vers login.
  - Connexion automatique après inscription.

- Page notes (/note) :
  - Liste des notes.
  - Gestion complète des notes selon permissions.
  - Boutons création, édition, suppression, et visualisation.

 **4. Rôles et permissions**

- ROLE_USER :
  - Accès complet à ses propres notes.

- ROLE_ADMIN :
  - Accès à toutes les notes.
  - Peut modifier et supprimer n’importe quelle note.

- Les redirections et autorisations sont gérées via security.yaml et les annotations dans les contrôleurs.

 **5. Création d’un compte admin**

- Un script est fourni pour créer un compte admin :
  - create_admin_sql.php
  - Permet de créer un admin via une connexion SQL directe à la base de données.
  - Nécessite de fournir email, mot de passe et attribue automatiquement le rôle ROLE_ADMIN.
  - J'ai laissé ce fichier pour que vous puissiez voir qu'on peut créer un admin.

 **6. Structure des fichiers importants**
<img width="367" height="522" alt="image" src="https://github.com/user-attachments/assets/d3a33464-411a-4416-bd09-39efa3a6c9e4" />

- Les pages Twig utilisent base.html.twig comme layout.
- Les boutons et liens sont affichés dynamiquement selon l’état de connexion (app.user).

 **7. Sécurité**

- Hashage des mots de passe :
  - via UserPasswordHasherInterface lors de l’inscription.
- Gestion CSRF sur tous les formulaires :
  - activée automatiquement sur les formulaires Symfony (login, register, suppression de note, etc.).
- Firewall main avec form_login et logout :
  - gestion de l’authentification avec form_login.
  - point de déconnexion /logout.
  - redirection automatique après login vers /note.
- Access Control :
  - toutes les routes liées aux notes (/note, /note/new, /note/{id}…) sont protégées et nécessitent d’être connecté.
- Redirections automatiques :
  - Si un utilisateur déjà connecté tente d’accéder à /login ou /register, il est redirigé vers /note.
- NoteVoter (autorisations avancées)
  - Vérifie si un utilisateur a le droit d’éditer ou supprimer une note.
  - Un utilisateur peut modifier uniquement ses propres notes.
  - Un administrateur (ROLE_ADMIN) peut modifier toutes les notes.
  - Utilisé dans Twig avec is_granted('edit', note).

 **8. Expérience utilisateur**

Non connecté :
- Accès / : voir login/register.
- Accès /register : création + connexion automatique --> /note.
- Accès /login : connexion --> /note.

Connecté :
- Accès / : lien vers /note et possibilité de déconnexion.
- Accès /login ou /register : redirection automatique vers /note.
- Accès /note : gestion des notes selon rôle et possibilité de déconnexion.

Admin :
- Peut voir, modifier et supprimer toutes les notes et possibilité de déconnexion.

 **9. Points de suivi**

- L’application fonctionne entièrement sous Docker.
- Scripts et fichiers sont laissés visibles (ex : create_admin_sql.php) sauf /vendor/ et /var/ comme vous l'avez demandé.
- Toutes les redirections et autorisations sont testées et fonctionnelles.
