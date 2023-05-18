import React, { useEffect, useState } from "react";
import instance from "../../service/api.service";

export interface IUsersListPageProps {};

const UsersListPage: React.FunctionComponent<IUsersListPageProps> = (props) => {
    const [users, setUsers] = useState('');
    
    useEffect(() => {
        const fetchData = async () => {
          try {
            const response = await instance.get("/users/list");
            setUsers(response.data.message);
          } catch (error) {
            console.error("Error fetching data:", error);
          }
        };
    
        fetchData();
      }, []);

    return (
        <div className="card">
            <p>{users}</p>
        </div>
    );
}

export default UsersListPage;