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
import NotFound from "./components/not-found/NotFound";
import OffersList from "./pages/offers/OfferList";
import OfferDetailPage from "./pages/offers/detail/OfferDetail";
import OfferForm from "./pages/offers/form/OfferForm";
import UserForm from "./pages/users/form/UserForm";
import UserDetailPage from "./pages/users/detail/UserDetail";
import Settings from "./pages/settings/Settings";
import CustomerForm from "./pages/customers/form/CustomerForm";
import CustomerDetailPage from "./pages/customers/detail/CustomerDetailPage";

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
                                    <Navigation setIsAuthenticated={setAuthenticated} />
                                </div>
                            : ''
                        }
                        <div className="app-main-container">
                            <div className="app-content-container">
                                <div className="app-content-wrapper">    
                                    <Routes>
                                        <Route element={<ProtectedRoute isAllowed={authenticated} />}>
                                            <Route path="/" element={<Dashboard />} />
                                            <Route path="/offers" element={<OffersList />} />
                                            <Route path="/offers/detail/:id" element={<OfferDetailPage />} />
                                            <Route path="/offers/add" element={<OfferForm />} />
                                            <Route path="/offers/edit/:id" element={<OfferForm />} />
                                            <Route path="/users" element={<UsersListPage />} />
                                            <Route path="/users/add" element={<UserForm />} />
                                            <Route path="/users/edit/:id" element={<UserForm />} />
                                            <Route path="/users/detail/:id" element={<UserDetailPage />} />
                                            <Route path="customers" element={<CustomersList />} />
                                            <Route path="/customers/add" element={<CustomerForm />} />
                                            <Route path="/customers/edit/:id" element={<CustomerForm />} />
                                            <Route path="/customers/detail/:id" element={<CustomerDetailPage />} />
                                            <Route path="/settings" element={<Settings />} />
                                        </Route>
                                        <Route path="login" element={<LoginPage setIsAuthenticated={setAuthenticated} />} />
                                        <Route path="*" element={<NotFound />} />
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