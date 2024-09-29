import axios from "axios";
import {useEffect} from "react";
import {useAppBridge} from "@shopify/app-bridge-react";

const useAxios = () => {
    const app = useAppBridge()


    useEffect(() => {
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

        const interceptor = axios.interceptors.request.use(async function (config) {
            config.headers.Authorization = `Bearer ${await shopify.idToken()}`
            config.params = {...config.params, host: window.__SHOPIFY_HOST}
            return config
        })

        const responseInterceptor = axios.interceptors.response.use(response => response, error => {
            if (error.response.status === 403 && error.response?.data?.forceRedirectUrl) {
                top.location.href = error.response.data.forceRedirectUrl;
            }

            return Promise.reject(error)
        })

        return () => {
            axios.interceptors.request.eject(interceptor)
            axios.interceptors.request.eject(responseInterceptor)
        }
    }, []);
    return {axios}
}

export default useAxios
