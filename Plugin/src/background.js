// Track network data per tab
const tabNetworkData = new Map();

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
      country: null, // Full country name
      countryCode: null, // Reduced country code for API
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
    console.error('Erreur lors de la récupération de l’intensité carbone:', error);
    return -1; // Retourne -1 en cas d'erreur
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

// Handle URL change and send data before updating the URL
async function handleUrlChange(tabId, newUrl) {
  const tabData = getTabData(tabId);

  if (newUrl !== tabData.currentUrl) {
    console.log(`URL change detected for tab ${tabId}:`, newUrl);

    // Send data if requests exist
    if (tabData.totalRequests > 0 && tabData.startTime && tabData.endTime) {
      // Get carbon intensity if countryCode is available
      const carbonIntensity = tabData.countryCode
        ? await getLatestCarbonIntensity(tabData.countryCode)
        : -1;

      const data = {
        tabId,
        domain: extractDomain(tabData.currentUrl), // Send data for the previous URL
        totalTransferredSize: tabData.totalTransferredSize,
        totalResourceSize: tabData.totalResourceSize,
        totalRequests: tabData.totalRequests,
        loadTime: tabData.endTime - tabData.startTime,
        url: tabData.currentUrl,
        country: tabData.country, // Full country name
        carbonIntensity, // Intensity data from API
      };

      await sendDataToServer(data);
    }

    // Update current URL and reset data
    tabData.currentUrl = newUrl;
    resetTabData(tabId);
  }
}

// Network request tracking
browser.webRequest.onCompleted.addListener(
  (details) => {
    const tabId = details.tabId;
    const tabData = getTabData(tabId);

    if (
      details.url.startsWith("http") &&
      details.statusCode >= 200 &&
      details.statusCode < 300
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

// Detect URL changes
browser.webNavigation.onCompleted.addListener((details) => {
  if (details.frameId === 0) {
    handleUrlChange(details.tabId, details.url);
  }
});

// Clean up tab data on tab removal
browser.tabs.onRemoved.addListener((tabId) => {
  tabNetworkData.delete(tabId);
});

// Fetch the country based on geolocation using ipwhois.io
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

// Periodic monitoring for dynamic URL changes
function monitorDynamicUrlChanges(tabId) {
  function checkUrlChange() {
    browser.tabs.get(tabId, (tab) => {
      if (browser.runtime.lastError) return;

      const tabData = getTabData(tabId);
      const newUrl = tab.url;

      if (newUrl !== tabData.currentUrl) {
        fetchCountry().then(({ country, countryCode }) => {
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

browser.tabs.onCreated.addListener((tab) => {
  monitorDynamicUrlChanges(tab.id);
});

browser.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.type === 'getCountryAndUrl') {
      // Récupérer l'URL et le pays
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

      // Cas 1 : l'onglet est fourni par le message
      if (sender.tab && sender.tab.url) {
          handleResponse(sender.tab.url);
          return true; // Indique une réponse asynchrone
      }

      // Cas 2 : Pas d'onglet dans sender, chercher l'onglet actif
      browser.tabs.query({ active: true, currentWindow: true }).then((tabs) => {
          if (tabs.length > 0 && tabs[0].url) {
              handleResponse(tabs[0].url);
          } else {
              sendResponse({ country: 'Unknown', url: 'Unknown' });
          }
      }).catch((error) => {
          console.error('Erreur lors de la récupération de l’onglet actif :', error);
          sendResponse({ country: 'Unknown', url: 'Unknown' });
      });

      return true; // Indique une réponse asynchrone
  }
});



