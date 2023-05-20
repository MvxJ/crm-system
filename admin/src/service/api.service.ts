import axios from "axios";
import TokenService from "./token.service";
import AuthService from "./auth.service";

const loader = document.getElementById("loader");
const instance = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
        "Content-Type": "application/json",
    },
});

let isRefreshing = false;
let failedRequests: Array<[]> = [];

instance.interceptors.request.use(
    (config) => {
        const loader = document.getElementById("loader");
        if (loader) {
            loader.style.display = "flex";
        }

        const token = TokenService.getLocalAccessToken();
        if (token) {
            config.headers["Authorization"] = "Bearer " + token;
        }

        return config;
    },
    (error) => {
        const loader = document.getElementById("loader");
        if (loader) {
            loader.style.display = "flex";
        }

        return Promise.reject(error);
    }
);

instance.interceptors.response.use(
    (res) => {
        const loader = document.getElementById("loader");
        if (loader) {
            loader.style.display = "none";
        }

        return res;
    },
    async (err) => {
        const originalConfig = err.config;

        if (
            originalConfig.url !== "/login/check" &&
            err.response &&
            err.response.status === 401
        ) {
            if (!isRefreshing) {
                isRefreshing = true;

                try {
                    const rs = await instance.post("/token/refresh", {
                        refresh_token: TokenService.getLocalRefreshToken(),
                    });

                    const accessToken = rs.data.token;
                    TokenService.updateLocalAccessToken(accessToken);

                    failedRequests.forEach((request) => {
                        request.headers["Authorization"] = "Bearer " + accessToken;
                    });
                    failedRequests = [];

                    return instance(originalConfig);
                } catch (_error) {
                    AuthService.logout();
                    const navigateToLogin = () => {
                        window.location.href = "/login?error=JWT_EXPIRED";
                    };
                    navigateToLogin();

                    if (loader) {
                        loader.style.display = "none";
                    }

                    return Promise.reject(_error);
                } finally {
                    isRefreshing = false;
                }
            } else {
                if (loader) {
                    loader.style.display = "none";
                }

                return new Promise((resolve, reject) => {
                    failedRequests.push({ resolve, reject });
                }).then((config) => instance(config)).catch((error) => Promise.reject(error));
            }
        }


        if (loader) {
            loader.style.display = "none";
        }

        return Promise.reject(err);
    }
);

export default instance;
