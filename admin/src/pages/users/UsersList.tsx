import React, { useEffect, useState } from "react";
import instance from "../../service/api.service";
import { Link } from "react-router-dom";
import { DataGrid } from '@mui/x-data-grid';
import { BsEye } from "react-icons/bs";
import { FiMoreVertical, FiTrash2 } from "react-icons/fi";
import { Alert, Menu, MenuItem, Snackbar } from "@mui/material";
import UsersService from "../../service/users.service";

export interface IUsersListPageProps {};

const UsersListPage: React.FunctionComponent<IUsersListPageProps> = (props) => {
    const [data, setData] = useState([]);
    const [totalRows, setTotalRows] = useState(0);
    const [currentId, setCurrentId] = useState(null);
    const [paginationModel, setPaginationModel] = useState({
        pageSize: 10,
        page: 0,
    });
    const [notification, setNotification] = useState({
        open: false,
        message: '',
        type: 'success'
    });

    useEffect(() => {
        fetchData();
    }, [paginationModel]);

    const columns = [
        { field: 'id', headerName: 'ID', width: 100 },
        { field: 'username', headerName: 'Username', width: 200 },
        { field: 'email', headerName: 'Email', width: 300 },
        { field: 'name', headerName: 'Name', width: 300 },
        { field: 'surname', headerName: 'Surname', width: 300 },
        {
            field: 'actions',
            headerName: 'Actions',
            type: 'actions',
            width: 150,
            renderCell: (params) => (
                <div className="actionColumn">
                    <FiMoreVertical onClick={(event) => handleClick(event, params.row.id)} />
                    <Menu
                        anchorEl={anchorEl}
                        open={Boolean(anchorEl)}
                        onClose={handleClose}
                        onClick={handleClose}
                    >
                        <MenuItem>
                            <Link to={`/users/detail/${currentId}`} className="text-decoration-none">
                                <BsEye /> View Details
                            </Link>
                        </MenuItem>
                        <MenuItem onClick={() => handleDelete(currentId)}>
                                <FiTrash2 /> Delete
                        </MenuItem>
                    </Menu>
                </div>
            ),
        },
    ];

    const handlePageChange = (newModel) => {
        setPaginationModel(newModel);
    };

    const handleDelete = async (id) => {
        try {
            await UsersService.deleteUser(id);
            fetchData();
            setAnchorEl(null);
            setCurrentId(null);
            showNotification('User deleted successfully', 'success');
        } catch (error) {
            showNotification('Failed to delete user', 'error');
        }
    };

    const [anchorEl, setAnchorEl] = useState(null);
    const handleClick = (event, id) => {
        event.stopPropagation();
        setAnchorEl(event.currentTarget);
        setCurrentId(id);
    };
    const handleClose = () => {
        setAnchorEl(null);
        setCurrentId(null);
    };

    const showNotification = (message, type) => {
        setNotification({
            open: true,
            message,
            type
        });
    };

    const snackbarPosition: SnackbarOrigin = { vertical: 'bottom', horizontal: 'right' };

    const closeNotification = () => {
        setNotification(prevState => ({
            ...prevState,
            open: false
        }));
    };

    const fetchData = async () => {
        try {
            const { page, pageSize } = paginationModel;
            const response = await instance.get(`/users/list?page=${page}&items=${pageSize}`);
            setData(response.data.items);
            setTotalRows(response.data.totalItems);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    return (
        <div className="card">
            <div className="card-header">
                <div className="card-title">
                    <h3>Users list</h3>
                </div>
                <div className="card-actions">
                    <Link to="/users/add" className="text-decoration-none">
                        <button className="button-standard">
                            Add user
                        </button>
                    </Link>
                </div>
            </div>
            <div className="card-body">
            <div style={{ height: '85vh', width: '100%' }}>
                    <DataGrid
                        rows={data}
                        columns={columns}
                        pagination
                        paginationMode="server"
                        rowCount={totalRows}
                        paginationModel={paginationModel}
                        onPaginationModelChange={handlePageChange}
                        pageSizeOptions={[10, 25, 50]}
                    />
                </div>
            </div>

            <Snackbar 
                open={notification.open} 
                autoHideDuration={5000} 
                onClose={closeNotification}
                anchorOrigin={snackbarPosition}
            >
                <Alert
                    onClose={closeNotification}
                    severity={notification.type}
                    sx={{ width: '100%' }}
                >
                    {notification.message}
                </Alert>
            </Snackbar>
        </div>
    );
}

export default UsersListPage;