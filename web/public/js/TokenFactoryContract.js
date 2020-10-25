
window.postMessage({ message: { type: 'CONNECT_QRYPTO' }}, '*');
const contractAddress = 'aaaf8c094e375c5b6fcde73dbdaf1cdcc2c04a79';

function handleQryptoInstalledOrUpdated(event) {
    if (event.data.message && event.data.message.type === 'QRYPTO_INSTALLED_OR_UPDATED') {
        // Refresh the page
        window.location.reload()
    }
}  
window.addEventListener('message', handleQryptoInstalledOrUpdated, false);

function handleQryptoAcctChanged(event) {
    if (event.data.message && event.data.message.type === "QRYPTO_ACCOUNT_CHANGED") {
        if (event.data.message.payload.error){
            // handle error
        }
        console.log("account:", event.data.message.payload.account)
    }
}
window.addEventListener('message', handleQryptoAcctChanged, false);

