import React from "react";

export interface IUsersListPageProps {};

const UsersListPage: React.FunctionComponent<IUsersListPageProps> = (props) => {
    return (
        <div className="card">
            <p>Users list</p>
        </div>
    );
}

export default UsersListPage;