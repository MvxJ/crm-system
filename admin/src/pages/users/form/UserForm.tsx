import React, { useEffect, useState } from "react";
import instance from "../../../service/api.service";
import UsersService from "../../../service/users.service";
import { useNavigate, useParams } from "react-router-dom";

export interface IUserFormPageProps {};

const UserForm: React.FunctionComponent<IUserFormPageProps> = (props) => {
    const { id } = useParams();
    const [user, setUser] = useState(null);
    const [formAddData, setFormAddData] = useState(
        { 
            username: '',
            email: '',
            password: ''
        }
    );
        const [formEditData, setFormEditData] = useState(
        { 
            username: '',
            email: '',
        }
    );
    const navigate = useNavigate();

    const fetchData = async () => {
        try {
            if (id) {
                const response = await instance.get(`/users/${id}`);
                setUser(response.data.user);
                setFormEditData({
                    username: response.data.user.username,
                    email: response.data.user.email
                });
            }
        } catch (error) {
            console.error("Error fetching data:", error);
            navigate("/users")
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


    const saveUser = async () => {
        await UsersService.updateUser(id, {
            username: formEditData.username,
            email: formEditData.email,
        });
        navigate(`/users/detail/${id}`);
    }

    const addUser = async () => {
        const response = await UsersService.addUser({
            username: formAddData.username,
            email: formAddData.email,
            password: formAddData.password
        });
        
        if (response.data.status == 'success') {
            navigate(`/users/detail/${response.data.userId}`)
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
                    { user ? <h3>Edit #{user.id} - {user.username}</h3> : <h3>Add user</h3>}
                </div>
                <div className="card-actions">
                    { user == null? 
                        <button className="button-standard" onClick={addUser}>
                            Add
                        </button>
                        :
                        <button className="button-standard" onClick={saveUser}>
                            Save
                        </button>
                    }
                </div>
            </div>
            <div className="card-body">
                <form>
                    { 
                        !user 
                        ?
                            <>
                                <div className="row">
                                    <div className="col">
                                        <label>Username:</label>
                                        <input
                                            type="text"
                                            name="username"
                                            value={formAddData.username}
                                            onChange={handleInputAddFormChange} />
                                    </div>
                                    <div classNmae="col">
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
                                    <label>Username:</label>
                                    <input
                                        type="text"
                                        name="username"
                                        value={formEditData.username}
                                        onChange={handleInputEditFormChange} />
                                </div>
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

export default UserForm;