    const getLocalRefreshToken = () => {
        const refreshToken = localStorage.getItem("refresh_token");
        
        return refreshToken;
    };
    
    const getLocalAccessToken = () => {
        const token = localStorage.getItem("token");
        
        return token;
    };
    
    const updateLocalAccessToken = (newToken: string) => {
        let token = localStorage.getItem("token");
        token = newToken;
        
        localStorage.setItem("token", token);
    };
    
    const TokenService = {
        getLocalRefreshToken,
        getLocalAccessToken,
        updateLocalAccessToken,
    };
  
  export default TokenService;