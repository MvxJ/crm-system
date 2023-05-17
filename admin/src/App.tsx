import React, { useState } from "react";
import { BrowserRouter, Navigate, Route, Routes } from "react-router-dom";
import Dashboard from "./pages/Dashboard";
import UsersListPage from "./pages/users/UsersList";
import Navigation from "./components/navigation/Navigation";
import Footer from "./components/footer/Footer";
import LoginPage from "./pages/login/LoginPage";
import './App.scss'; 
import CustomersList from "./pages/customers/CustomersList";
import { ProtectedRoute } from "./components/protected-route/protectedRoute";
import AuthService from "./service/auth.service";

export interface IAppProps {}

const App: React.FunctionComponent<IAppProps> = (props) => {
    const authenticated = AuthService.isAuthenticated();
    const [isAuthenticated, setAuthenticated] = useState(authenticated);

        return (
            <>
                <div className="app-container">
                    <BrowserRouter>
                        { authenticated 
                            ?
                                <div className="app-navigation-container">
                                    <Navigation />
                                </div>
                            : ''
                        }
                        <div className="app-main-container">
                            <div className="app-content-container">
                                <div className="app-content-wrapper">    
                                    <Routes>
                                        <Route element={<ProtectedRoute isAllowed={authenticated} />}>
                                            <Route path="/" element={<Dashboard />} />
                                            <Route path="users" element={<UsersListPage />} />
                                            <Route path="customers" element={<CustomersList />} />
                                        </Route>
                                        <Route path="login" element={<LoginPage setIsAuthenticated={setAuthenticated} />} />
                                    </Routes>
                                </div>
                            </div>
                            <div className="app-footer-container">
                                <Footer></Footer>
                            </div>
                        </div>
                    </BrowserRouter>
                </div>
            </>
        );
}

export default App;