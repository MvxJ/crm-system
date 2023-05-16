import React from "react";
import { BrowserRouter, Route, Routes } from "react-router-dom";

export interface ILoginPageProps {};

const LoginPage: React.FunctionComponent<ILoginPageProps> = (props) => {
    return (
        <div>
            <p>Login Page</p>
        </div>
    );
}

export default LoginPage;