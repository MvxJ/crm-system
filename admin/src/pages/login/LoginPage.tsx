import React, { useState } from "react";
import './LoginPage.scss';
import { useNavigate } from "react-router-dom";

export interface ILoginPageProps {};

const LoginPage: React.FunctionComponent<ILoginPageProps> = (props) => {
    const navigate = useNavigate();
    const [username, setUsername] = useState("");
    const [password, setPassword] = useState("");

    const handleLogin = (event: React.FormEvent) => {
        event.preventDefault();

        if (username != '' && password != '') {
            const response = false;
            
            navigate("/");
        } else {

        }
        console.log(username, password);
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
                </form>
            </div>
        </div>
    );
}

export default LoginPage;