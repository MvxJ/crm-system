import React, { useEffect, useState } from "react";
import instance from "../../service/api.service";

export interface ICustomersListPageProps {};

const CustomersList: React.FunctionComponent<ICustomersListPageProps> = (props) => {
    const [customers, setCustomers] = useState('');
    
    useEffect(() => {
        const fetchData = async () => {
          try {
            const response = await instance.get("/clients/list");
            setCustomers(response.data.message);
          } catch (error) {
            console.error("Error fetching data:", error);
          }
        };
    
        fetchData();
      }, []);
    
    return (
        <div className="card">
            <p>{customers}</p>
        </div>
    );
}

export default CustomersList;