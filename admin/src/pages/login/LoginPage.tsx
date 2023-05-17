import React, { useState } from "react";
import './LoginPage.scss';
import axios from "axios";
import { useNavigate } from "react-router-dom";

export interface ILoginPageProps {};

const LoginPage: React.FunctionComponent<ILoginPageProps> = (props) => {
    const [username, setUsername] = useState("");
    const [password, setPassword] = useState("");
    const navigate = useNavigate();

    const handleLogin = (event: React.FormEvent) => {
        event.preventDefault();

        axios.post('http://localhost:8000/api/login/check', {
            username: "mvxj", 
            password: "password"
        }).then(res => {
            localStorage.setItem("user", 'true');
            navigate("/");
        }).catch(e => {
            console.log(e)
        });

        if (username != '' && password != '') {
        } else {
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
                </form>
            </div>
        </div>
    );
}

export default LoginPage;