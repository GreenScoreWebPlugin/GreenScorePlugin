// Récupérer les données réseau pour chaque onglet
const tabNetworkData = new Map();
const lastSentData = new Map();

// Information nécessaire pour appeler les APIs
const token = "R1oPkUx8UdRnx";
const carbonIntensityUrl =
  "https://api.electricitymap.org/v3/carbon-intensity/latest";

function getTabData(tabId) {
  if (!tabNetworkData.has(tabId)) {
    tabNetworkData.set(tabId, {
      totalTransferredSize: 0,
      totalResourceSize: 0,
      totalRequests: 0,
      loadTime: 0,
      startTime: null,
      endTime: null,
      currentUrl: null,
      country: null,
      countryCode: null,
      carbonIntensity: 0,
      lastDataSent: null,
      isProcessing: false,
      requestSizes: new Map(), // Pour tracker la taille des requêtes individuelles
    });
  }
  return tabNetworkData.get(tabId);
}

// Récupérer l'intensité carbone pour un pays
async function getLatestCarbonIntensity(countryCode) {
  try {
    const response = await fetch(`${carbonIntensityUrl}?zone=${countryCode}`, {
      method: "GET",
      headers: {
        "auth-token": token,
        "Content-Type": "application/json",
      },
    });

    if (!response.ok) {
      throw new Error(`Erreur: ${response.statusText}`);
    }

    const data = await response.json();
    console.log(
      `Intensité carbone pour la zone ${countryCode}: ${data.carbonIntensity} gCO₂/kWh`
    );
    browser.tabs.query({ active: true, currentWindow: true }).then((tabs) => {
      if (tabs.length > 0) {
        const tabData = getTabData(tabs[0].id);
        tabData.carbonIntensity = data.carbonIntensity;
      }
    });
    return data.carbonIntensity;
  } catch (error) {
    console.error(
      "Erreur lors de la récupération de l'intensité carbone:",
      error
    );
    return -1;
  }
}

// Envoyer les données au backend pour l'envoi en BD
async function sendDataToServer(data) {
  try {
    console.log("Sending data to server:", data);
    const response = await fetch("http://127.0.0.1:8080/index.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    if (!response.ok) {
      throw new Error(`Server responded with status ${response.status}`);
    }
    console.log("Data sent to the server successfully.");
  } catch (error) {
    console.error("Failed to send data to the server:", error);
  }
}

// Reset tab data
function resetTabData(tabId) {
  const tabData = getTabData(tabId);
  tabData.totalTransferredSize = 0;
  tabData.totalResourceSize = 0;
  tabData.totalRequests = 0;
  tabData.loadTime = 0;
  tabData.startTime = null;
  tabData.endTime = null;
  tabData.isProcessing = false;
  tabData.userId = null;
}

// Fonction pour extraire le domaine de l'URL
function extractDomain(url) {
  try {
    const parsedUrl = new URL(url);
    return parsedUrl.hostname.replace("www.", "");
  } catch (error) {
    return null;
  }
}

function shouldSendData(oldData, newData) {
  if (!oldData) return true;

  if (newData.lastDataSent && Date.now() - newData.lastDataSent < 10000) {
    return false;
  }

  return true;
}

async function getSymfonyUserId() {
  try {
    const cookies = await browser.cookies.getAll({
      domain: "127.0.0.1",
    });

    const sessionCookie = cookies.find((cookie) => cookie.name === "PHPSESSID");

    if (!sessionCookie) {
      console.log("Pas de cookie de session trouvé");
      return null;
    }

    const response = await fetch("http://127.0.0.1:8000/api/user-info", {
      credentials: "include",
      headers: {
        Accept: "application/json",
        Cookie: `PHPSESSID=${sessionCookie.value}`,
      },
    });

    if (!response.ok) {
      throw new Error("Failed to get user info");
    }

    const userData = await response.json();
    return userData;
  } catch (error) {
    console.error("Erreur lors de la récupération de l'ID:", error);
    return null;
  }
}

async function getUserData() {
  try {
    const userData = await getSymfonyUserId();
    return {
      isLoggedIn: !!userData,
      userId: userData ? userData.id : null,
    };
  } catch (error) {
    console.error(
      "Erreur lors de la récupération des données utilisateur:",
      error
    );
    return { isLoggedIn: false, userId: null };
  }
}

// Lock global simplifié
let sendInProgress = false;
let lastSendTime = 0;
const SEND_COOLDOWN = 1000; // 1 seconde minimum entre les envois

async function processAndSendData(tabId, tabData, isFinal = false) {
  const now = Date.now();
  if (sendInProgress || (!isFinal && now - lastSendTime < SEND_COOLDOWN)) {
    console.log("Skipping data send: cooldown in effect or send in progress.");
    return;
  }

  sendInProgress = true; // Active le verrou

  try {
    // Récupère les données utilisateur
    const { isLoggedIn, userId } = await getUserData();
    console.log(isLoggedIn, userId);
    if (!isLoggedIn || !userId) {
      return; // Abandonne si l'utilisateur n'est pas connecté
    }

    // Prépare les données à envoyer
    const domain = extractDomain(tabData.currentUrl);
    if (
      domain === "localhost" ||
      domain === "127.0.0.1" ||
      domain === "[::1]"
    ) {
      console.log(
        "Données non calculées/envoyées : URL en localhost ou domaine local."
      );
      return; // Interrompt le traitement si l'URL est locale
    }

    const carbonIntensity = tabData.countryCode
      ? await getLatestCarbonIntensity(tabData.countryCode)
      : -1;

    const emissionsData = calculateCarbonEmissions(tabData);

    const dataToSend = {
      tabId,
      domain,
      totalTransferredSize: tabData.totalTransferredSize,
      totalResourceSize: tabData.totalResourceSize,
      totalRequests: tabData.totalRequests,
      totalEmissions: emissionsData.totalEmissions,
      loadTime: (tabData.endTime - tabData.startTime) / 1000,
      url: tabData.currentUrl,
      country: tabData.country,
      userId: userId,
      carbonIntensity,
      isFinal,
    };

    // Envoie les données
    await sendDataToServer(dataToSend);
    lastSendTime = now; // Mise à jour du temps du dernier envoi
  } catch (error) {
    console.error("Erreur dans processAndSendData:", error);
  } finally {
    sendInProgress = false; // Libère le verrou
  }
}

// Variable pour tracker la dernière URL traitée par onglet
const lastProcessedUrls = new Map();

async function handleUrlChange(tabId, newUrl, isFinal = false) {
  const tabData = getTabData(tabId);
  const lastProcessedUrl = lastProcessedUrls.get(tabId);

  // Éviter de traiter plusieurs fois la même URL
  if (!isFinal && newUrl === lastProcessedUrl) {
    return;
  }

  // Met à jour l'URL traitée pour éviter les répétitions
  lastProcessedUrls.set(tabId, newUrl);

  tabData.currentUrl = newUrl;

  try {
    const { country, countryCode } = await fetchCountry();
    tabData.country = country;
    tabData.countryCode = countryCode;
  } catch (error) {
    console.error(
      "Erreur lors de la récupération des données de géolocalisation :",
      error
    );
    tabData.country = "Unknown";
    tabData.countryCode = null;
  }
  await processAndSendData(tabId, tabData, true);
}

let listenersInitialized = false;

function initializeListeners() {
  if (listenersInitialized) return;
  listenersInitialized = true;

  browser.webNavigation.onBeforeNavigate.addListener((details) => {
    if (details.frameId === 0) {
      handleUrlChange(details.tabId, details.url);
      resetTabData(details.tabId);
    }
  });

  // Détecte les changements d'URL classiques
  browser.webNavigation.onCompleted.addListener((details) => {
    if (details.frameId === 0) {
      handleUrlChange(details.tabId, details.url);
    }
  });

  // Détecte les changements d'URL dans les SPAs (pushState/replaceState)
  browser.webNavigation.onHistoryStateUpdated.addListener((details) => {
    if (details.frameId === 0) {
      handleUrlChange(details.tabId, details.url);
    }
  });

  // Gère la fermeture des onglets
  browser.tabs.onRemoved.addListener(async (tabId) => {
    const tabData = tabNetworkData.get(tabId);
    if (tabData && tabData.currentUrl) {
      await handleUrlChange(tabId, tabData.currentUrl, true);
    }
    tabNetworkData.delete(tabId);
  });
}

// Appelez cette fonction une seule fois au démarrage
initializeListeners();

// Récupérer le pays selon la géolocalisation
async function fetchCountry() {
  try {
    const response = await fetch("https://ipwhois.app/json/");
    if (!response.ok) {
      throw new Error(`API responded with status ${response.status}`);
    }

    const data = await response.json();
    if (data.success === false) {
      throw new Error(`API error: ${data.message}`);
    }

    return { country: data.country, countryCode: data.country_code };
  } catch (error) {
    console.error("Erreur lors de la récupération du pays:", error);
    return { country: "Unknown", countryCode: null };
  }
}

// Listener pour le début des requêtes
browser.webRequest.onBeforeRequest.addListener(
  (details) => {
    if (details.tabId === -1) return; // Ignore les requêtes non associées à un onglet

    const tabData = getTabData(details.tabId);
    tabData.totalRequests++;

    if (!tabData.startTime) {
      tabData.startTime = details.timeStamp;
    }

    // Initialise le suivi de cette requête spécifique
    tabData.requestSizes.set(details.requestId, {
      transferredSize: 0,
      resourceSize: 0,
    });
  },
  { urls: ["<all_urls>"] }
);

// Listener pour les headers de réponse
browser.webRequest.onHeadersReceived.addListener(
  (details) => {
    if (details.tabId === -1) return;

    const tabData = getTabData(details.tabId);
    const requestData = tabData.requestSizes.get(details.requestId);

    if (!requestData) return;

    // Récupère la taille du contenu depuis les headers
    const contentLength = details.responseHeaders?.find(
      (header) => header.name.toLowerCase() === "content-length"
    );

    if (contentLength) {
      const size = parseInt(contentLength.value, 10);
      if (!isNaN(size)) {
        requestData.resourceSize = size;
      }
    }

    // Détermine le type de compression
    const contentEncoding = details.responseHeaders?.find(
      (header) => header.name.toLowerCase() === "content-encoding"
    );

    if (contentEncoding) {
      requestData.encoding = contentEncoding.value;
    }
  },
  { urls: ["<all_urls>"] },
  ["responseHeaders"]
);

// Listener pour la fin des requêtes
browser.webRequest.onCompleted.addListener(
  (details) => {
    if (details.tabId === -1) return;

    const tabData = getTabData(details.tabId);
    const requestData = tabData.requestSizes.get(details.requestId);

    if (!requestData) return;

    // Met à jour le timestamp de fin
    tabData.endTime = details.timeStamp;

    // Calcule la taille transférée réelle
    let transferredSize = details.responseSize || 0;
    requestData.transferredSize = transferredSize;

    // Calcule la taille réelle de la ressource en fonction de la compression
    let resourceSize = requestData.resourceSize;
    if (transferredSize > 0 && !resourceSize) {
      switch (requestData.encoding) {
        case "gzip":
        case "deflate":
          resourceSize = transferredSize * 3; // Ratio de compression moyen de 3:1
          break;
        case "br":
          resourceSize = transferredSize * 5; // Ratio de compression moyen de 5:1
          break;
        default:
          resourceSize = transferredSize;
      }
    }

    // Met à jour les totaux
    tabData.totalTransferredSize += transferredSize;
    tabData.totalResourceSize += resourceSize;

    // Nettoie les données de cette requête
    tabData.requestSizes.delete(details.requestId);
  },
  { urls: ["<all_urls>"] },
  ["responseHeaders"]
);

// Listener pour les erreurs
browser.webRequest.onErrorOccurred.addListener(
  (details) => {
    if (details.tabId === -1) return;

    const tabData = getTabData(details.tabId);
    tabData.requestSizes.delete(details.requestId);
  },
  { urls: ["<all_urls>"] }
);

// Constantes pour calculer les émissions carbones
const CARBON_CONSTANTS = {
  KWH_PER_GB_TRANSFER: 0.519, // Valeur donnée selon The Shift Project 2023

  KWH_PER_GB_DATACENTER: 0.065, // Valeur donnée selon Sustainable Web Design

  KWH_PER_REQUEST: 0.00015, // Valeur approximative selon plusieurs sources

  BYTES_TO_GB: 1 / (1024 * 1024 * 1024),
  MS_TO_HOURS: 1 / (1000 * 60 * 60),
};

function calculateCarbonEmissions(tabData) {
  const bytesTransferred = tabData.totalTransferredSize || 0;
  const bytesResources = tabData.totalResourceSize || 0;
  const requests = tabData.totalRequests || 0;
  const loadTimeMs = tabData.endTime - tabData.startTime || 0;
  const carbonIntensity = tabData.carbonIntensity || 442;

  const gbTransferred = bytesTransferred * CARBON_CONSTANTS.BYTES_TO_GB;
  const gbResources = bytesResources * CARBON_CONSTANTS.BYTES_TO_GB;

  const transferEnergy = gbTransferred * CARBON_CONSTANTS.KWH_PER_GB_TRANSFER;
  const datacenterEnergy = gbResources * CARBON_CONSTANTS.KWH_PER_GB_DATACENTER;
  const requestEnergy = requests * CARBON_CONSTANTS.KWH_PER_REQUEST;

  const totalEnergy = transferEnergy + datacenterEnergy + requestEnergy;

  const emissions = totalEnergy * carbonIntensity;

  return {
    totalEmissions: Number(emissions.toFixed(2)),
    breakdown: {
      transfer: Number((transferEnergy * carbonIntensity).toFixed(2)),
      datacenter: Number((datacenterEnergy * carbonIntensity).toFixed(2)),
      requests: Number((requestEnergy * carbonIntensity).toFixed(2)),
    },
    metrics: {
      energy: {
        transfer: transferEnergy,
        datacenter: datacenterEnergy,
        requests: requestEnergy,
        total: totalEnergy,
      },
      data: {
        gbTransferred,
        gbResources,
        requests,
        loadTimeMs,
        carbonIntensity,
      },
    },
  };
}

browser.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.type === "getCountryAndUrl") {
    const handleResponse = (tabUrl) => {
      fetchCountry()
        .then(({ country }) => {
          sendResponse({ country, url: extractDomain(tabUrl) });
        })
        .catch((error) => {
          console.error("Erreur lors de la récupération du pays :", error);
          sendResponse({ country: "Unknown", url: extractDomain(tabUrl) });
        });
    };

    if (sender.tab && sender.tab.url) {
      handleResponse(sender.tab.url);
      return true;
    }

    browser.tabs
      .query({ active: true, currentWindow: true })
      .then((tabs) => {
        if (tabs.length > 0 && tabs[0].url) {
          handleResponse(tabs[0].url);
        } else {
          sendResponse({ country: "Unknown", url: "Unknown" });
        }
      })
      .catch((error) => {
        console.error(
          "Erreur lors de la récupération de l'onglet actif :",
          error
        );
        sendResponse({ country: "Unknown", url: "Unknown" });
      });

    return true;
  }

  if (message.type === "getgCO2e") {
    return browser.tabs
      .query({ active: true, currentWindow: true })
      .then((tabs) => {
        if (tabs.length > 0) {
          const tabId = tabs[0].id;
          const tabData = getTabData(tabId);

          // Étape 1 : Assurez-vous que le pays et le code du pays sont récupérés
          const countryPromise =
            !tabData.country || !tabData.countryCode
              ? fetchCountry().then(({ country, countryCode }) => {
                  tabData.country = country;
                  tabData.countryCode = countryCode;
                })
              : Promise.resolve();

          // Étape 2 : Une fois le pays obtenu, assurez-vous que l'intensité carbone est disponible
          return countryPromise.then(() => {
            const intensityPromise =
              tabData.countryCode && tabData.carbonIntensity <= 0
                ? getLatestCarbonIntensity(tabData.countryCode).then(
                    (intensity) => {
                      tabData.carbonIntensity = intensity;
                    }
                  )
                : Promise.resolve();

            // Étape 3 : Calcul des émissions après avoir récupéré les données nécessaires
            return intensityPromise.then(() => {
              const emissionsData = calculateCarbonEmissions(tabData);

              console.log("Détails du calcul des émissions pour le front :", {
                bytesTransferred: tabData.totalTransferredSize,
                bytesResources: tabData.totalResourceSize,
                requests: tabData.totalRequests,
                loadTime: tabData.endTime - tabData.startTime,
                carbonIntensity: tabData.carbonIntensity,
                result: emissionsData,
              });

              return {
                gCO2e: emissionsData.totalEmissions,
                breakdown: emissionsData.breakdown,
                metrics: emissionsData.metrics, // Inclut les métriques pour analyse
              };
            });
          });
        } else {
          // Aucun onglet actif, envoyer des données par défaut
          return {
            gCO2e: 0,
            breakdown: { transfer: 0, datacenter: 0, requests: 0 },
            metrics: {
              energy: { transfer: 0, datacenter: 0, requests: 0, total: 0 },
              data: {
                gbTransferred: 0,
                gbResources: 0,
                requests: 0,
                loadTimeMs: 0,
                carbonIntensity: 0,
              },
            },
          };
        }
      })
      .catch((error) => {
        console.error("Erreur dans le gestionnaire getgCO2e :", error);
        return {
          error:
            "Une erreur est survenue lors du calcul des émissions de carbone.",
        };
      });
  }

  if (message.type === "getFullDetails") {
    browser.tabs
      .query({ active: true, currentWindow: true })
      .then((tabs) => {
        if (tabs.length === 0) {
          sendResponse({ error: "Aucun onglet actif trouvé." });
          return;
        }

        const activeTab = tabs[0];
        const tabId = activeTab.id;
        const tabData = getTabData(tabId);

        sendResponse({
          country: tabData.country || "Unknown",
          countryCode: tabData.countryCode || "Unknown",
          urlDomain: extractDomain(tabData.currentUrl || activeTab.url),
          urlFull: tabData.currentUrl || activeTab.url,
          totalTransferredSize: tabData.totalTransferredSize || 0,
          totalRequests: tabData.totalRequests || 0,
          totalResourceSize: tabData.totalResourceSize || 0,
          loadTime: (tabData.endTime - tabData.startTime) / 1000 || 0,
        });
      })
      .catch((error) => {
        console.error(
          "Erreur lors de la récupération de l'onglet actif :",
          error
        );
        sendResponse({ error: error.message });
      });

    return true;
  }

  if (message.type === "checkLoginStatus") {
    return getUserData();
  }

  if (message.type === "getEquivalent") {
    browser.tabs.query({ active: true, currentWindow: true }).then((tabs) => {
      if (tabs.length === 0) {
        sendResponse({ success: false, error: "Aucun onglet actif trouvé." });
        return;
      }

      const tabId = tabs[0].id;
      const tabData = getTabData(tabId);

      // Calcul des émissions d'abord
      const emissions = calculateCarbonEmissions(tabData);
      const gCO2 = emissions.totalEmissions;
      const count = message.count || 1;

      // Appel direct à l'API Symfony
      fetch(
        `http://127.0.0.1:8000/api/equivalent?gCO2=${gCO2}&count=${count}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        }
      )
        .then((response) => {
          if (!response.ok) {
            throw new Error(`Erreur API Symfony : ${response.status}`);
          }
          return response.json();
        })
        .then((equivalents) => {
          console.log("Équivalents reçus :", equivalents);
          sendResponse({
            success: true,
            equivalents: equivalents.map((eq) => ({
              image:
                "https://greenscoreweb.alwaysdata.net/public/equivalents/" +
                  eq.icon || "../assets/images/account.svg",
              value: eq.value,
              name: eq.name,
            })),
          });
        })
        .catch((error) => {
          console.error("Erreur dans getEquivalent:", error);
          sendResponse({
            success: false,
            error: error.message,
          });
        });
    });

    // Important: retourner true pour indiquer que sendResponse sera appelé de manière asynchrone
    return true;
  }
});
