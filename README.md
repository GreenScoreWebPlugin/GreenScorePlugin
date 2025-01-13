# GreenScore Web

## Prérequis
- **Composer**
- **Node**
- **PHP** 
- **Firefox**
- Si vous testez avec eduroam à l'IUT, il faut mettre une VPN
  
---

## Installation et mise en route

### 1. Cloner le projet
Clonez le projet depuis le dépôt GitHub :
```bash
git clone https://github.com/GreenScoreWebPlugin/GreenScorePlugin.git
```

### 2. Configurer le site web
Dans un nouveau terminal, accédez au dossier **GreenScoreWebsite** depuis la racine du projet :
```bash
cd GreenScoreWebsite
```

#### Installer les dépendances
Installez les dépendances nécessaires à Symfony :
```bash
composer install
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
