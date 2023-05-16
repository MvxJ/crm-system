import React from "react";

export interface IDashboardPageProps {};

const Dashboard: React.FunctionComponent<IDashboardPageProps> = (props) => {
    return (
        <div className="card">
            <p>Dashboard!</p>
        </div>
    );
}

export default Dashboard;