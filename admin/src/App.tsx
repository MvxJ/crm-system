import React from "react";
import { BrowserRouter, Navigate, Route, Routes } from "react-router-dom";
import Dashboard from "./pages/Dashboard";
import UsersListPage from "./pages/users/UsersList";
import Navigation from "./components/navigation/Navigation";
import Footer from "./components/footer/Footer";
import LoginPage from "./pages/login/LoginPage";
import './App.css'; 

export interface IAppProps {}

const App: React.FunctionComponent<IAppProps> = (props) => {
    const authenticated = false;

        return (
            <>
                <div className="app-container">
                    <div className="app-navigation-container">
                        <Navigation />
                    </div>
                    <div className="app-content-container">
                        <BrowserRouter>
                            <Routes>
                                <Route path="/" element={<Dashboard />} />
                                <Route path="users" element={<UsersListPage />} />
                                <Route path="login" element={<LoginPage/>} />
                            </Routes>
                        </BrowserRouter>
                    </div>
                </div>
            </>
        );
}

export default App;