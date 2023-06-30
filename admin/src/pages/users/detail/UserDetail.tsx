import React, { useEffect, useState } from "react";
import instance from "../../../service/api.service";
import { useNavigate, useParams } from "react-router-dom";
import UsersService from "../../../service/users.service";

export interface IUserDetailPageProps {};

const UserDetailPage: React.FunctionComponent<IUserDetailPageProps> = (props) => {
    const { id } = useParams();
    const [user, setUser] = useState([]);
    const navigate = useNavigate();

    const fetchData = async () => {
        try {
            const response = await instance.get(`/users/${id}`);
            setUser(response.data.user);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const editUser = () => {
        navigate(`/users/edit/${user.id}`);
    }

    const deleteUser = async () => {
        await UsersService.deleteUser(user.id);
        navigate(`/users`);
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
                    <h3>User #{user.id} - {user.username}</h3>
                </div>
                <div className="card-actions">
                    <button className="button-standard" onClick={editUser}>
                        Edit
                    </button>
                    <button className="button-standard danger" onClick={deleteUser}>
                        Delete
                    </button>
                </div>
            </div>
            <div className="card-body">
                <div className="row">
                    <div className="col">
                        <h4>Name:</h4>
                        <p></p>
                    </div>
                    <div className="col">
                        <h4>Surname:</h4>
                        <p></p>
                    </div>
                </div>
                <div className="row">
                    <div className="col">
                        <h4>Email:</h4>
                        <p>{user.email}</p>
                    </div>
                    <div className="col">
                        <h4>Authenticated: </h4>
                        <p>{user.is_verified ? 'Yes' : 'No'}</p>
                    </div>
                </div>
                <div className="row">
                    <div className="col">
                        <h4>2FA: </h4>
                        <p>{user.email_auth ? 'Yes' : 'No'}</p>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default UserDetailPage;