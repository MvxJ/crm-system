import React from "react";

export interface ICustomersListPageProps {};

const CustomersList: React.FunctionComponent<ICustomersListPageProps> = (props) => {
    return (
        <div className="card">
            <p>Customers list</p>
        </div>
    );
}

export default CustomersList;