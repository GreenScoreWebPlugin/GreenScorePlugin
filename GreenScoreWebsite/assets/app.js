import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import { Application } from "stimulus";
import { definitionsFromContext } from "stimulus/webpack-helpers";
import MyAccountController from './controllers/my_account_controller';

// Créer l'application Stimulus
const application = Application.start();

// Charger les contrôleurs à partir des fichiers
const context = require.context("./controllers", true, /\.js$/);
application.load(definitionsFromContext(context));

// Ou spécifiquement enregistrer ton contrôleur
application.register("my-account", MyAccountController);