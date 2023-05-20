import React, { useEffect, useState } from "react";
import './LoginPage.scss';
import axios from "axios";
import { useNavigate } from "react-router-dom";
import AuthService from "../../service/auth.service";

export interface ILoginPageProps {
    setIsAuthenticated: React.Dispatch<React.SetStateAction<boolean>>;
};

const LoginPage: React.FunctionComponent<ILoginPageProps> = (props) => {
    const [username, setUsername] = useState("");
    const [password, setPassword] = useState("");
    const [errorMessage, setErrorMessage] = useState("");
    const navigate = useNavigate();
    const loader = document.getElementById("loader");

    useEffect(() => {
        const authenticated = AuthService.isAuthenticated();
        
        if (authenticated) {
          navigate("/");
        }
    }, [navigate]);

    const handleLogin = (event: React.FormEvent) => {
        event.preventDefault();

        if (username != '' && password != '') {
            if (loader) {
                loader.style.display="flex";
            }

            axios.post('http://localhost:8000/api/login/check', {
                username: username, 
                password: password
            }).then(response => {
                AuthService.login(response.data.token, response.data.refresh_token, response.data.user);
                setErrorMessage("");
                props.setIsAuthenticated(AuthService.isAuthenticated());
                navigate("/");
                
                if (loader) {
                    loader.style.display="none";
                }
            }).catch(e => {
                if (e.response.status == 403) {
                    setErrorMessage("You don't have permission to access this resources.")
                } else {
                    setErrorMessage(e.response.data.message);
                }
                
                if (loader) {
                    loader.style.display="none";
                }
            });
        } else {
            setErrorMessage("Please provide user data.");
        }
    }
    
    return (
        <div className="login-container">
            <div className="form-container">
                <h3>CRM System</h3>
                <form onSubmit={handleLogin}>
                    <div className="form-row">
                        <label>Username</label>
                        <input 
                            type="text" 
                            placeholder="johndoe" 
                            name="username" 
                            id="username" 
                            value={username} 
                            onChange={(e) => setUsername(e.target.value)} 
                        />
                    </div>
                    <div className="form-row">
                        <label>Password</label>
                        <input 
                            type="password" 
                            placeholder="********" 
                            name="password" id="password" 
                            value={password} 
                            onChange={(e) => setPassword(e.target.value)} 
                        />
                    </div>
                    <div className="form-row">
                        <button className="button-standard">Login</button>
                    </div>
                    <div className="form-row">
                        {errorMessage && <p className="error-message">{errorMessage}</p>}
                    </div>
                </form>
            </div>
        </div>
    );
}

export default LoginPage;