let gCO2eValue;

async function updateEquivalents() {
  try {
    console.log("Envoi de la requête à background.js...");
    const response = await browser.runtime.sendMessage({
      type: "getEquivalent",
      count: 3, // Nombre d'équivalents à récupérer
    });
    console.log("Réponse reçue :", response);

    if (response && response.success && response.equivalents) {
      const cards = document.querySelectorAll(".comparison-card");
      response.equivalents.forEach((equivalent, index) => {
        if (cards[index]) {
          const card = cards[index];
          const img = card.querySelector("img");
          const valueElement = card.querySelector("p.text-xl");
          const description = card.querySelector("p.text-xs");

          img.src = equivalent.image || "../assets/images/default.svg";
          valueElement.textContent = equivalent.value;
          description.textContent = equivalent.name;
        }
      });
    } else {
      console.error(
        "Erreur dans la réponse reçue :",
        response ? response.error : "Réponse indéfinie"
      );
    }
  } catch (error) {
    console.error("Erreur dans updateEquivalents :", error);
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  function getColorClass(gCO2e) {
    const value = Number(gCO2e);

    if (value <= 0.3) {
      return {
        text: "text-[#617D3B]",
        bg: "bg-[#ECFDF2]",
        border: "border-[#6D874B]",
      };
    } else if (value <= 0.7) {
      return {
        text: "text-[#EAC13A]",
        bg: "bg-[#FFF1C5]",
        border: "border-[#EAC13A]",
      };
    } else if (value <= 1) {
      return {
        text: "text-[#E98035]",
        bg: "bg-[#F9D2B6]",
        border: "border-[#E98035]",
      };
    } else {
      return {
        text: "text-[#BD2525]",
        bg: "bg-[#FFB7B7]",
        border: "border-[#BD2525]",
      };
    }
  }

  function updateColors(gCO2e) {
    const colorClasses = getColorClass(gCO2e);

    const gCO2eContainer = document.getElementById("gCO2e-container");
    const gCO2eValue = document.getElementById("gCO2e-value");

    if (gCO2eContainer && gCO2eValue) {
      gCO2eContainer.className = `flex items-baseline font-outfit ${colorClasses.text}`;
      gCO2eValue.textContent = `${gCO2e}\u00A0`;
    }

    const comparisonCards = document.querySelectorAll(".comparison-card");
    comparisonCards.forEach((card) => {
      card.className = `comparison-card flex flex-col p-2 w-[120px] h-[120px] ${colorClasses.bg} ${colorClasses.text} gap-2 border ${colorClasses.border} rounded-[4px]`;
    });
  }

  function updateAverageConsumption(gCO2e) {
    const AVERAGE_CONSUMPTION = 0.74;
    let multiplier = gCO2e / AVERAGE_CONSUMPTION;

    if (multiplier > 1) {
      document.getElementById("average-consumption").textContent =
          `${multiplier.toFixed(2)}x supérieur`;
    } else {
      document.getElementById("average-consumption").textContent =
          `${(1 / multiplier).toFixed(2)}x inférieur`;
    }
  }

  // Vérification du statut de connexion
  try {
    const userData = await browser.runtime.sendMessage({
      type: "checkLoginStatus",
    });
    const loginSection = document.querySelector(
      ".flex.font-outfit.text-sm.justify-center"
    );
    const detailsButton = document.getElementById("details-button");

    if (loginSection) {
      if (userData.isLoggedIn) {
        loginSection.innerHTML =
          '<span class="text-sm text-grey-950">Vous êtes connecté</span>';
      } else {
        loginSection.innerHTML = `
          <span class="text-sm text-grey-950">Vous souhaitez enregistrer ce résultat ?&nbsp;</span>
          <a href="http://127.0.0.1:8000/login" class="text-[#6D874B] font-bold underline">Se connecter</a>
        `;
      }
    }

    // Mise à jour des couleurs en fonction de gCO2e
    try {
      const response = await browser.runtime.sendMessage({ type: "getgCO2e" });
      if (response && typeof response.gCO2e === "number") {
        updateColors(response.gCO2e);
        updateAverageConsumption(response.gCO2e);
        gCO2eValue = response.gCO2e;
      }
    } catch (error) {
      console.error("Erreur lors de la récupération du gCO2e:", error);
    }

    // Gestion du bouton "Plus de détails"
    if (detailsButton) {
      detailsButton.addEventListener("click", async (e) => {
        e.preventDefault();

        // Récupérer les détails actuels
        const response = await browser.runtime.sendMessage({
          type: "getFullDetails",
        });

        let url = "http://localhost:8000/derniere-page-web-consultee";

        if (!userData.isLoggedIn) {
          // Construction des paramètres d'URL si non connecté
          const params = new URLSearchParams({
            country: response.country || "",
            url_full: response.urlFull || "",
            totalConsu: gCO2eValue || 0,
            pageSize: response.totalResourceSize || 0,
            loadingTime: response.loadTime || 0,
            queriesQuantity: response.totalRequests || 0,
          });
          url += "?" + params.toString();
        }

        // Ouvrir l'URL dans un nouvel onglet
        window.open(url, "_blank");
      });
    }
  } catch (error) {
    console.error(
      "Erreur lors de la vérification de l'état de connexion:",
      error
    );
  }

  browser.runtime
    .sendMessage({ type: "getCountryAndUrl" })
    .then((response) => {
      if (response && response.country && response.url) {
        const urlElement = document.getElementById("site-url");
        const countryElement = document.getElementById("site-country");

        if (countryElement && urlElement) {
          countryElement.textContent = `Dans votre pays (${response.country}), cette page consomme`;
          urlElement.textContent = response.url;
        }
      } else if (response.error) {
        console.error("Erreur :", response.error);
      }
    })
    .catch((error) => {
      console.error(
        "Erreur lors de la récupération du pays ou de l'URL :",
        error
      );
    });

    try {
      await updateEquivalents();
    } catch (error) {
      console.error("Erreur lors de la mise à jour des équivalents :", error);
    }
    
});
