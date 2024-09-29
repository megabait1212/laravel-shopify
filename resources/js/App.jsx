import {useState} from "react";
import {AppProvider} from "@shopify/polaris";
import enTranslations from '@shopify/polaris/locales/en.json'
import MissingApiKey from "./components/MissingApiKey.jsx";
import FakeDataCreator from "./components/FakeDataCreator.jsx";

const App = () => {
    const [appBridgeConfig] = useState(() => {
        const host = new URLSearchParams(location.search).get('host') || window.__SHOPIFY_HOST
        window.__SHOPIFY_HOST = host

        return {
            host,
            apiKey: import.meta.env.VITE_SHOPIFY_API_KEY,
            forceRedirect: true
        }
    })

    if (!appBridgeConfig.apiKey) {
        return (
            <AppProvider i18n={enTranslations}>
                <MissingApiKey/>
            </AppProvider>
        )
    }

    return (
        <AppProvider i18n={enTranslations}>
            <FakeDataCreator></FakeDataCreator>
        </AppProvider>
    )
}

export default App
