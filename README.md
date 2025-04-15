# GreenScore Web

## Prérequis
- **Composer**
- **Node**
- **PHP** 
- **Firefox**
- Si vous testez avec eduroam à l'IUT, il faut mettre un VPN
  
---

## Installation et mise en route

### 1. Cloner le projet
Clonez le projet depuis le dépôt GitHub :
```bash
git clone https://github.com/GreenScoreWebPlugin/GreenScorePlugin.git
```

### 2. Configurer le site web
Accédez à la racine du projet puis au dossier **GreenScoreWebsite** :
```bash
cd GreenScorePlugin
cd GreenScoreWebsite
```

#### Ajouter les .env.local
Pour que le projet fonctionne en local, il vous faut les identifiants de la base de données dans deux fichier .env.local. Pour cela, vous pouvez utiliser les deux .env.local suivant :

- Site : [.env.local](./generic_env_local/website.env.local), que vous devrez mettre à la racine du dossier 'GreenScoreWebsite'
- Plugin : [.env.local](./generic_env_local/plugin.env.local), que vous devrez mettre à la racine du dossier 'BackendPlugin'

Pensez a correctement les renommer '.env.local' et à changer les valeurs dans les .env.local pour qu'ils fonctionnent correctement avec votre base de données.
Pour que la récupération de l'empreinte carbone fonctionne, pensez à créer une clé API et l'ajouter correctement dans le .env.local du site.

#### Base de données
Pour que la base de données puisse fonctionner, il vous faut utiliser le .sql suivant :

[greenscore.sql](./generic_env_local/greenscore.sql)

#### Installer les dépendances
Installez les dépendances nécessaires à Symfony :
```bash
composer install
```
Si vous obtenez des erreurs lors de cette commande, rendez-vous dans votre php.ini puis ajoutez cette ligne :
;entension=xml
Si vous avez encore des erreurs, supprimez le ; devant l'extension suivante :
- pdo_mysql

#### Installer les dépendances front-end
Installez les dépendances front-end nécessaires avec npm :
```bash
npm install
npm run build
```

#### Démarrer le serveur Symfony
Lancez le serveur local Symfony :
```bash
php -S 127.0.0.1:8000 -t public
```

Laissez ce terminal ouvert.

### 4. Configurer le backend du plugin
Ouvrez un autre terminal et retournez à la racine du projet :
```bash
cd BackendPlugin
```

Démarrez le serveur PHP pour le backend :
```bash
php -S 127.0.0.1:8080
```

### 5. Configurer le plugin dans Firefox
1. Ouvrez **Firefox**.
2. Cliquez sur l’icône en forme de puzzle en haut à droite.
3. Cliquez sur **Gérer les extensions.**
4. Cliquez sur la roue dentée pour accéder à **Gestion de vos extensions**.
5. Sélectionnez **Déboguer les modules**.
6. Cliquez sur **Charger un module complémentaire temporaire...**.
7. Accédez au dossier `Plugin` à la racine du projet.
8. Sélectionnez le fichier `manifest.json` et cliquez sur **Ouvrir**.

### 6. Tester le plugin
Le plugin est maintenant actif ! 

1. Rendez-vous sur [amazon.fr](https://www.amazon.fr) avec Firefox.
2. Testez les fonctionnalités du plugin.
