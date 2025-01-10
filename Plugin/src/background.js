// Track network data per tab
const tabNetworkData = new Map();
const lastSentData = new Map();

// API details
const token = 'R1oPkUx8UdRnx';
const carbonIntensityUrl = 'https://api.electricitymap.org/v3/carbon-intensity/latest';

// Helper to get or initialize tab data
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
      lastDataSent: null,
      isProcessing: false // Flag pour éviter les envois simultanés
    });
  }
  return tabNetworkData.get(tabId);
}

// Fetch the carbon intensity for a country code
async function getLatestCarbonIntensity(countryCode) {
  try {
    const response = await fetch(`${carbonIntensityUrl}?zone=${countryCode}`, {
      method: 'GET',
      headers: {
        'auth-token': token,
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error(`Erreur: ${response.statusText}`);
    }

    const data = await response.json();
    console.log(`Intensité carbone pour la zone ${countryCode}: ${data.carbonIntensity} gCO₂/kWh`);
    return data.carbonIntensity;
  } catch (error) {
    console.error("Erreur lors de la récupération de l'intensité carbone:", error);
    return -1;
  }
}

// Send data to server
async function sendDataToServer(data) {
  try {
    console.log("Sending data to server:", data);
    const response = await fetch("http://127.0.0.1/index.php", {
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
}

// Extract domain from URL
function extractDomain(url) {
  try {
    const parsedUrl = new URL(url);
    return parsedUrl.hostname.replace("www.", "");
  } catch (error) {
    return null;
  }
}

// Function to check if data has changed significantly
function hasSignificantChanges(oldData, newData) {
  if (!oldData) return true;
  
  // Check if enough time has passed (5 seconds minimum)
  if (newData.lastDataSent && (Date.now() - newData.lastDataSent < 5000)) {
    return false;
  }

  // Check if metrics have changed significantly
  const transferredSizeChange = Math.abs(newData.totalTransferredSize - oldData.totalTransferredSize);
  const requestsChange = newData.totalRequests - oldData.totalRequests;
  
  return transferredSizeChange > 1000 || requestsChange > 0;
}

// Process and send data
async function processAndSendData(tabId, tabData, isFinal = false) {
  if (tabData.isProcessing || (tabData.totalRequests === 0 && !isFinal)) return;
  
  tabData.isProcessing = true;
  
  try {
    const domain = extractDomain(tabData.currentUrl);
    const carbonIntensity = tabData.countryCode
      ? await getLatestCarbonIntensity(tabData.countryCode)
      : -1;

    const dataToSend = {
      tabId,
      domain,
      totalTransferredSize: tabData.totalTransferredSize,
      totalResourceSize: tabData.totalResourceSize,
      totalRequests: tabData.totalRequests,
      loadTime: tabData.endTime - tabData.startTime,
      url: tabData.currentUrl,
      country: tabData.country,
      carbonIntensity,
      isFinal
    };

    // Update last sent timestamp
    tabData.lastDataSent = Date.now();
    
    // Save last sent data
    lastSentData.set(domain, {
      totalTransferredSize: tabData.totalTransferredSize,
      totalRequests: tabData.totalRequests,
      lastDataSent: tabData.lastDataSent
    });

    await sendDataToServer(dataToSend);
  } finally {
    tabData.isProcessing = false;
  }
}

// Handle URL change and send data
async function handleUrlChange(tabId, newUrl) {
  const tabData = getTabData(tabId);
  
  if (newUrl === tabData.currentUrl) {
    const domain = extractDomain(newUrl);
    const currentData = {
      totalTransferredSize: tabData.totalTransferredSize,
      totalRequests: tabData.totalRequests,
      lastDataSent: tabData.lastDataSent
    };

    const lastSent = lastSentData.get(domain);

    if (hasSignificantChanges(lastSent, currentData)) {
      await processAndSendData(tabId, tabData);
    }
    return;
  }

  // Si l'URL est différente, envoyer les données de l'ancienne URL
  if (tabData.currentUrl) {
    await processAndSendData(tabId, tabData);
  }

  // Mettre à jour l'URL et réinitialiser les données
  tabData.currentUrl = newUrl;
  resetTabData(tabId);
}

// Fetch country based on geolocation
async function fetchCountry() {
  try {
    const response = await fetch('https://ipwhois.app/json/');
    if (!response.ok) {
      throw new Error(`API responded with status ${response.status}`);
    }

    const data = await response.json();
    if (data.success === false) {
      throw new Error(`API error: ${data.message}`);
    }

    return { country: data.country, countryCode: data.country_code };
  } catch (error) {
    console.error('Erreur lors de la récupération du pays:', error);
    return { country: 'Unknown', countryCode: null };
  }
}

// Monitor dynamic URL changes
function monitorDynamicUrlChanges(tabId) {
  let lastCheckedUrl = null;
  
  function checkUrlChange() {
    browser.tabs.get(tabId, (tab) => {
      if (browser.runtime.lastError) return;

      const newUrl = tab.url;
      if (newUrl !== lastCheckedUrl) {
        lastCheckedUrl = newUrl;
        fetchCountry().then(({ country, countryCode }) => {
          const tabData = getTabData(tabId);
          tabData.country = country;
          tabData.countryCode = countryCode;
          handleUrlChange(tabId, newUrl);
        });
      }
    });
  }

  const intervalId = setInterval(checkUrlChange, 500);
  return () => clearInterval(intervalId);
}

// Network request tracking
browser.webRequest.onCompleted.addListener(
  (details) => {
    const tabId = details.tabId;
    if (tabId < 0) return; // Ignorer les requêtes qui ne sont pas associées à un onglet

    const tabData = getTabData(tabId);

    if (
      details.url.startsWith("http") &&
      details.statusCode >= 200 &&
      details.statusCode < 300 &&
      details.url !="about:newtab"
    ) {
      tabData.totalRequests++;

      const transferredSize = details.responseSize || 0;
      let contentLength = 0;
      for (const header of details.responseHeaders || []) {
        if (header.name.toLowerCase() === "content-length") {
          contentLength = parseInt(header.value) || 0;
          break;
        }
      }

      const encoding = details.responseHeaders?.find(
        (header) => header.name.toLowerCase() === "content-encoding"
      );

      if (contentLength < transferredSize) {
        tabData.totalTransferredSize += transferredSize;
      } else {
        tabData.totalTransferredSize += contentLength + transferredSize;
      }

      let estimatedRatio = 1;
      let resourceSize = contentLength;
      if (encoding) {
        if (encoding.value === "br") {
          estimatedRatio = 0.2;
        } else if (encoding.value === "gzip") {
          estimatedRatio = 0.3;
        }
        resourceSize = contentLength / estimatedRatio;
      }

      tabData.totalResourceSize += resourceSize;

      const timeStamp = details.timeStamp;
      if (!tabData.startTime || timeStamp < tabData.startTime) {
        tabData.startTime = timeStamp;
      }
      if (!tabData.endTime || timeStamp > tabData.endTime) {
        tabData.endTime = timeStamp;
      }
    }
  },
  { urls: ["<all_urls>"] },
  ["responseHeaders"]
);

// Event listeners
browser.webNavigation.onCompleted.addListener((details) => {
  if (details.frameId === 0) {
    handleUrlChange(details.tabId, details.url);
  }
});

browser.webNavigation.onBeforeNavigate.addListener((details) => {
  const tabData = tabNetworkData.get(details.tabId);
  if (tabData && tabData.currentUrl) {
    processAndSendData(details.tabId, tabData, true);
  }
});

// Handle tab removal - send final data before removing
browser.tabs.onRemoved.addListener(async (tabId) => {
  const tabData = tabNetworkData.get(tabId);
  if (tabData && tabData.currentUrl) {
    await processAndSendData(tabId, tabData, true);
  }
  tabNetworkData.delete(tabId);
});

browser.tabs.onCreated.addListener((tab) => {
  monitorDynamicUrlChanges(tab.id);
});

// Message handling
browser.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.type === 'getCountryAndUrl') {
    const handleResponse = (tabUrl) => {
      fetchCountry()
        .then(({ country }) => {
          sendResponse({ country, url: extractDomain(tabUrl) });
        })
        .catch((error) => {
          console.error('Erreur lors de la récupération du pays :', error);
          sendResponse({ country: 'Unknown', url: extractDomain(tabUrl) });
        });
    };

    if (sender.tab && sender.tab.url) {
      handleResponse(sender.tab.url);
      return true;
    }

    browser.tabs.query({ active: true, currentWindow: true }).then((tabs) => {
      if (tabs.length > 0 && tabs[0].url) {
        handleResponse(tabs[0].url);
      } else {
        sendResponse({ country: 'Unknown', url: 'Unknown' });
      }
    }).catch((error) => {
      console.error("Erreur lors de la récupération de l'onglet actif :", error);
      sendResponse({ country: 'Unknown', url: 'Unknown' });
    });

    return true;
  }

  if (message.type === 'getgCO2e') {
    browser.tabs.query({ active: true, currentWindow: true }).then((tabs) => {
      if (tabs.length > 0) {
        const tabData = getTabData(tabs[0].id);
        
        let gCO2e = 0;
        if (tabData.totalTransferredSize > 0) {
          const kbTransferred = tabData.totalTransferredSize / 1024;
          gCO2e = Math.round(kbTransferred * 0.2);
        }

        sendResponse({ gCO2e: gCO2e });
      } else {
        sendResponse({ gCO2e: 0 });
      }
    });
    return true;
  }

  if (message.type === 'getFullDetails') {
    browser.tabs.query({ active: true, currentWindow: true }).then((tabs) => {
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
            loadTime: tabData.endTime - tabData.startTime || 0,
        });
    }).catch((error) => {
        console.error("Erreur lors de la récupération de l'onglet actif :", error);
        sendResponse({ error: error.message });
    });

    return true;
}
  
});