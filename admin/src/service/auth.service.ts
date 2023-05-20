import axios from "axios";

const login = (token: string, refreshToken: string, user: any) => {
    localStorage.setItem("user", JSON.stringify(user));
    localStorage.setItem("token", token);
    localStorage.setItem("refresh_token", refreshToken);
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

    if (user) {
        return true;
    }

    return false;
}

const AuthService = {
  login,
  logout,
  getCurrentUser,
  isAuthenticated
};

export default AuthService;