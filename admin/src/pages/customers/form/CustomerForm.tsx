import React, { useEffect, useState } from "react";
import instance from "../../../service/api.service";
import { useNavigate, useParams } from "react-router-dom";
import CustomersService from "../../../service/customer.service";

export interface ICustomerFormPageProps {};

const CustomerForm: React.FunctionComponent<ICustomerFormPageProps> = (props) => {
    const { id } = useParams();
    const [customer, setCustomer] = useState(null);
    const [formAddData, setFormAddData] = useState(
        { 
            email: '',
            password: '',
            firstName: '',
            surname: '',
            secondName: '',
            phoneNumber: '',
            socialSecurityNumber: ''
        }
    );
        const [formEditData, setFormEditData] = useState(
        { 
            email: '',
            firstName: '',
            surname: '',
            secondName: '',
            phoneNumber: '',
            socialSecurityNumber: ''
        }
    );
    const navigate = useNavigate();

    const fetchData = async () => {
        try {
            if (id) {
                const response = await instance.get(`/customers/detail/${id}`);
                setCustomer(response.data.customer);
                setFormEditData({
                    email: response.data.customer.email,
                    firstName: response.data.customer.firstName,
                    surname: response.data.customer.surname,
                    secondName: response.data.customer.secondName,
                    phoneNumber: response.data.customer.phoneNumber,
                    socialSecurityNumber: response.data.customer.socialSecurityNumber
                });
            }
        } catch (error) {
            console.error("Error fetching data:", error);
            navigate("/customers")
        }
    };

    const handleInputEditFormChange = (event) => {
        const { name, value } = event.target;
        setFormEditData((prevFormData) => ({ ...prevFormData, [name]: value }));
    };

    const handleInputAddFormChange = (event) => {
        const { name, value } = event.target;
        setFormAddData((prevFormData) => ({ ...prevFormData, [name]: value }));
    };


    const saveCustomer = async () => {
        await CustomersService.updateCustomer(id, {
            email: formEditData.email,
            surname: formEditData.surname,
            firstName: formEditData.firstName,
            secondName: formEditData.secondName,
            phoneNumber: formEditData.phoneNumber,
            socialSecurityNumber: formEditData.socialSecurityNumber
        });
        navigate(`/customers/detail/${id}`);
    }

    const addCustomer = async () => {
        const response = await CustomersService.addCustomer({
            email: formAddData.email,
            password: formAddData.password,
            surname: formAddData.surname,
            firstName: formAddData.firstName,
            secondName: formAddData.secondName,
            phoneNumber: formAddData.phoneNumber,
            socialSecurityNumber: formAddData.socialSecurityNumber
        });
        
        if (response.data.status == 'success') {
            navigate(`/customers/detail/${response.data.customerId}`)
        }
    }

    useEffect(() => {
        fetchData();
      }, []
    );

    return (
        <div className="card">
            <div className="card-header">
                <div className="card-title">
                    { customer ? <h3>Edit #{customer.id} - {customer.email}</h3> : <h3>Add customer</h3>}
                </div>
                <div className="card-actions">
                    { customer == null? 
                        <button className="button-standard" onClick={addCustomer}>
                            Add
                        </button>
                        :
                        <button className="button-standard" onClick={saveCustomer}>
                            Save
                        </button>
                    }
                </div>
            </div>
            <div className="card-body">
                <form>
                    { 
                        !customer 
                        ?
                            <>
                                <div className="row">
                                    <div className="col">
                                        <label>Name:</label>
                                        <input type="text"
                                            name="firstName"
                                            value={formAddData.firstName}
                                            onChange={handleInputAddFormChange} />
                                    </div>
                                    <div className="col">
                                        <label>Surname:</label>
                                        <input type="text"
                                            name="surname"
                                            value={formAddData.surname}
                                            onChange={handleInputAddFormChange} />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col">
                                        <label>Second name:</label>
                                        <input type="text"
                                            name="secondName"
                                            value={formAddData.secondName}
                                            onChange={handleInputAddFormChange} />
                                    </div>
                                    <div className="col">
                                        <label>Securirt number:</label>
                                        <input type="text"
                                            name="socialSecurityNumber"
                                            value={formAddData.socialSecurityNumber}
                                            onChange={handleInputAddFormChange} />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col">
                                        <label>Phone number:</label>
                                        <input type="text"
                                            name="phoneNumber"
                                            value={formAddData.phoneNumber}
                                            onChange={handleInputAddFormChange} />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col">
                                        <label>Email:</label>
                                        <input type="text"
                                            name="email"
                                            value={formAddData.email}
                                            onChange={handleInputAddFormChange} />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col">
                                        <label>Password:</label>
                                        <input
                                            type="password"
                                            name="password"
                                            value={formAddData.password}
                                            onChange={handleInputAddFormChange} />
                                    </div>
                                </div>    
                            </>
                        :
                            <>
                                <div className="row">
                                    <div className="col">
                                        <label>Name:</label>
                                        <input type="text"
                                            name="firstName"
                                            value={formEditData.firstName}
                                            onChange={handleInputEditFormChange} />
                                    </div>
                                    <div className="col">
                                        <label>Surname:</label>
                                        <input type="text"
                                            name="surname"
                                            value={formEditData.surname}
                                            onChange={handleInputEditFormChange} />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col">
                                        <label>Second name:</label>
                                        <input type="text"
                                            name="secondName"
                                            value={formEditData.secondName}
                                            onChange={handleInputEditFormChange} />
                                    </div>
                                    <div className="col">
                                        <label>Securirt number:</label>
                                        <input type="text"
                                            name="socialSecurityNumber"
                                            value={formEditData.socialSecurityNumber}
                                            onChange={handleInputEditFormChange} />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col">
                                        <label>Phone number:</label>
                                        <input type="text"
                                            name="phoneNumber"
                                            value={formEditData.phoneNumber}
                                            onChange={handleInputEditFormChange} />
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col">
                                        <label>Email:</label>
                                        <input type="text"
                                            name="email"
                                            value={formEditData.email}
                                            onChange={handleInputEditFormChange} />
                                    </div>
                                </div>
                            </>
                    }
                </form>
            </div>
        </div>
    );
}

export default CustomerForm;