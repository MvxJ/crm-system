import axios from "axios";
import TokenService from "./Token";
import AuthService from "./auth";

const instance = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
        "Content-Type": "application/json",
    },
});

let isRefreshing = false;
let failedRequests = [];

const navigateToLogin = () => {
    window.location.href = "/login?error=JWT_EXPIRED";
};

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
      return res;
    },
    async (err) => {
      const originalConfig = err.config;
  
      if (
        originalConfig.url !== "/login/check" &&
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
  
            originalConfig.headers["Authorization"] = "Bearer " + accessToken;
            return instance(originalConfig);
          } catch (_error) {
            AuthService.logout();
            navigateToLogin();
          } finally {
            isRefreshing = false;
          }
        } else {
          AuthService.logout();
          navigateToLogin();
        }
      }
  
      return Promise.reject(err);
    }
  );

export default instance;