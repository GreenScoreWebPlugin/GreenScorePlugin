const CONFIG = {
    BACKEND: {
      PLUGIN_BACKEND_URL: "http://127.0.0.1:8080/index.php", // URL du backend du plugin

      DOMAIN: "127.0.0.1", // Utilisé pour récupérer l'ID utilisateur
      
      WEBSITE_URL: "http://127.0.0.1:8000", // URL du site Greenscore
      
      LOGIN_URL: "http://127.0.0.1:8000/login", // URL de la page de connexion
      DETAILS_URL: "http://127.0.0.1:8000/derniere-page-web-consultee" // URL de la page de détails
    }
  };
  
  if (typeof browser !== 'undefined') {
  } else if (typeof module !== 'undefined') {
    module.exports = CONFIG;
  }