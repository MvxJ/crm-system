import axios from "axios";
import TokenService from "./token.service";
import AuthService from "./auth.service";

const loader = document.getElementById("loader");
const instance = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
        "Content-Type": "application/json"
    }
})

instance.interceptors.request.use(
    (config) => {
      if (loader) {
        loader.style.display="flex";
      }

      const token = TokenService.getLocalAccessToken();
      if (token) {
        config.headers["Authorization"] = 'Bearer ' + token;
      }
      
      return config;
    },
    (error) => {
      if (loader) {
        loader.style.display="flex";
      }

      return Promise.reject(error);
    }
  );
  
  instance.interceptors.response.use(
    (res) => {
      if (loader) {
        loader.style.display="none";
      } 

      return res;
    },
    async (err) => {
      const originalConfig = err.config;
  
      if (originalConfig.url !== "/login/check" && err.response) {
        if (err.response.status === 401 && !originalConfig._retry) {
          originalConfig._retry = true;
  
          try {
            const rs = await instance.post("/token/refresh", {
              refresh_token: TokenService.getLocalRefreshToken(),
            });
  
            const accessToken = rs.data.token;
            TokenService.updateLocalAccessToken(accessToken);
  
            return instance(originalConfig);
          } catch (_error) {
            AuthService.logout();
            
            if (loader) {
              loader.style.display="none";
            }

            return Promise.reject(_error);
          }
        }
      }

      if (loader) {
        loader.style.display="none";
      }
  
      return Promise.reject(err);
    }
  );
  
  export default instance;