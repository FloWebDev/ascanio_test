# ASCANIO TEST

## Contexte :
Dans le cadre de son développement, un client nous a demandé de créer un outil pour qu’il puisse
s’organiser dans ses tâches. Il nous a donc sollicité pour créer cet outil.

## Objectifs :
1. Créer un outil type Trello (avec trois colonnes: A faire, En cours, Fini) permettant d’ajouter des tâches avec les fonctionnalités suivantes :
    1. Nom de la tâche **=> OK**
    2. Priorité (de 1 à 5) (qui donnera un badge côté front d’une couleur lié à l’importance (ex rouge = 5) **=> OK**
    3. Descriptif **=> OK**
2. Pouvoir modifier/supprimer les tâches **=> OK**
3. Sauvegarder les tâches dans une base de données pour les retrouver quand on revient sur l’application **=> OK**
4. Possibilité d’ordonner les tâches via des boutons ↑ et ↓ **=> OK**
5. Possibilité de déplacer via des boutons < et > **=> OK**
6. Faire des fixtures pour créer une 50aine de tâches dispersées dans les colonnes aléatoirement **=> OK**
7. Pouvoir ajouter des colonnes supplémentaires par l’utilisateur **=> OK**

## Règles

Pas de requète SQL (Juste fonction native de Doctrine et DQL). **=> OK**

## Layout

Utilisation de celui donné dans les consignes, donc utlisation de SCSS. Utilisation de **Webpack Encore.**

## Tehchnologies utilisées

* Symfony 5 avec ajout du **web-server-bundle**. Ce bundle n'est plus présent sur Symfony 5, mais reste pratique pour développer sans avoir besoin d'installer un VHOST ou le client Symfony.
* Twig + JS Vanilla + jQuery + Ajax.
* MySQL 8 (les migrations sont faites en conséquence).

## Pour installer et tester le projet

Cloner le repository GitHub.

`composer install` + Modification du fichier .env en .env.local pour la BDD.

`bin/console doc:dat:crea`

`bin/console doc:mig:mig`

Soit `bin/console init:data` pour générer les données initiales (= les priorités) nécessaires au bon fonctionnement de l'application, soit `bin/console doc:fix:load` pour la génération de la cinquantaine de fixtures demandées (donnés initiales incluses). **Ne pas générer les données initiales après les fixtures (inutile et risque de bug).**

`yarn encore dev` ou `yarn encore production`

`bin/console serv:run`