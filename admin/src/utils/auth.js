const login = (token, refreshToken, user, expiration) => {
    localStorage.setItem("user", JSON.stringify(user));
    localStorage.setItem("token", token);
    localStorage.setItem("refresh_token", refreshToken);
    localStorage.setItem("expiration_date", expiration);
};

const logout = () => {
    localStorage.removeItem("user");
    localStorage.removeItem("token");
    localStorage.removeItem("refresh_token");
};

const getCurrentUser = () => {
    const user = localStorage.getItem("user");

    if (user) {
        return JSON.parse(user);
    }

    return null
};

const isAuthenticated = () => {
    const user = localStorage.getItem("user");
    const expiration = localStorage.getItem("expiration_date");
  
    if (!user || !expiration) {
      return false;
    }
  
    const expirationDate = new Date(expiration + 7200);
    const currentTime = new Date();
  
    const timeDifferenceInSeconds = Math.floor((expirationDate - currentTime) / 1000);
  
    return timeDifferenceInSeconds > 0;
}

const AuthService = {
  login,
  logout,
  getCurrentUser,
  isAuthenticated
};

export default AuthService;