import React, { useEffect, useState } from "react";
import instance from "../../../service/api.service";
import { useNavigate, useParams } from "react-router-dom";
import CustomersService from "../../../service/customer.service";

export interface ICustomerDetailPageProps {};

const CustomerDetailPage: React.FunctionComponent<ICustomerDetailPageProps> = (props) => {
    const { id } = useParams();
    const [customer, setCustomer] = useState([]);
    const navigate = useNavigate();

    const fetchData = async () => {
        try {
            const response = await instance.get(`/customers/detail/${id}`);
            setCustomer(response.data.customer);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const editCustomer = () => {
        navigate(`/customers/edit/${customer.id}`);
    }

    const deleteCustomer = async () => {
        await CustomersService.deleteCustomer(customer.id);
        navigate(`/customers`);
    }

    useEffect(
        () => {
            fetchData();
        }, []
    );

    return (
        <div className="card">
            <div className="card-header">
                <div className="card-title">
                    <h3>Customer #{customer.id} - {customer.email}</h3>
                </div>
                <div className="card-actions">
                    <button className="button-standard" onClick={editCustomer}>
                        Edit
                    </button>
                    <button className="button-standard danger" onClick={deleteCustomer}>
                        Delete
                    </button>
                </div>
            </div>
            <div className="card-body">
                <div className="row">
                    <div className="col">
                        <h4>Name:</h4>
                        <p>{customer.firstName}</p>
                    </div>
                    <div className="col">
                        <h4>Surname:</h4>
                        <p>{customer.surname}</p>
                    </div>
                </div>
                <div className="row">
                    <div className="col">
                        <h4>Second name:</h4>
                        <p>{customer.secondName}</p>
                    </div>
                    <div className="col">
                        <h4>Phone number:</h4>
                        <p>{customer.phoneNumber}</p>
                    </div>
                </div>
                <div className="row">
                    <div className="col">
                        <h4>Social security number:</h4>
                        <p>{customer.socialSecurityNumber}</p>
                    </div>

                </div>
                <div className="row">
                    <div className="col">
                        <h4>Email:</h4>
                        <p>{customer.email}</p>
                    </div>
                    <div className="col">
                        <h4>Authenticated: </h4>
                        <p>{customer.is_verified ? 'Yes' : 'No'}</p>
                    </div>
                </div>
                <div className="row">
                    <div className="col">
                        <h4>2FA: </h4>
                        <p>{customer.email_auth ? 'Yes' : 'No'}</p>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default CustomerDetailPage;