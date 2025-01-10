document.addEventListener("DOMContentLoaded", () => {
  function getColorClass(gCO2e) {
    const value = Number(gCO2e);

    if (value <= 200) {
      return {
        text: "text-[#617D3B]",
        bg: "bg-[#ECFDF2]",
        border: "border-[#6D874B]",
      };
    } else if (value <= 400) {
      return {
        text: "text-[#EAC13A]",
        bg: "bg-[#FFF1C5]",
        border: "border-[#EAC13A]",
      };
    } else if (value <= 600) {
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
      console.log(card);
    });
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

  browser.runtime
    .sendMessage({ type: "getgCO2e" })
    .then((response) => {
      if (response && typeof response.gCO2e === "number") {
        updateColors(response.gCO2e);
      } else {
        console.error("Valeur gCO2e invalide:", response);
      }
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération du gCO2e :", error);
    });

  const detailsButton = document.getElementById("details-button");
  detailsButton.addEventListener("click", async (event) => {
    event.preventDefault();

    try {
      const fullDetailsResponse = await browser.runtime.sendMessage({
        type: "getFullDetails",
      });
      console.log(fullDetailsResponse);

      if (fullDetailsResponse) {
        const {
          country,
          urlDomain,
          urlFull,
          totalTransferredSize,
          totalResourceSize,
          totalRequests,
          loadTime,
        } = fullDetailsResponse;

        const detailsUrl = new URL(
          "http://localhost:8000/derniere-page-web-consultee"
        );
        detailsUrl.searchParams.append("country", country || "unknown");
        detailsUrl.searchParams.append("url_domain", urlDomain || "unknown");
        detailsUrl.searchParams.append("url_full", urlFull || "unknown");
        detailsUrl.searchParams.append("totalConsu", totalTransferredSize || 0);
        detailsUrl.searchParams.append("pageSize", totalResourceSize || 0);
        detailsUrl.searchParams.append("loadingTime", loadTime || 0);
        detailsUrl.searchParams.append("queriesQuantity", totalRequests || 0);

        const tabs = await browser.tabs.query({
          active: true,
          currentWindow: true,
        });
        if (tabs.length > 0) {
          await browser.tabs.update(tabs[0].id, { url: detailsUrl.toString() });
        } else {
          console.error("Aucun onglet actif trouvé.");
        }
      } else {
        console.error("Les données nécessaires sont manquantes.");
      }
    } catch (error) {
      console.error("Erreur lors de la récupération des détails :", error);
    }
  });
});
