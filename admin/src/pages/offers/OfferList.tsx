import React, { useEffect, useState } from "react";
import instance from "../../service/api.service";
import { Link } from "react-router-dom";
import { DataGrid } from '@mui/x-data-grid';
import { BsEye } from "react-icons/bs";
import { FiMoreVertical, FiTrash2 } from "react-icons/fi";
import { Alert, Menu, MenuItem, Snackbar, SnackbarOrigin } from "@mui/material";
import MuiAlert from '@mui/material/Alert';
import OfferService from "../../service/offer.service";

export interface IOffersListPageProps {};

const OffersList: React.FunctionComponent<IOffersListPageProps> = (props) => {
    const [data, setData] = useState([]);
    const [totalRows, setTotalRows] = useState(0);
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

    const fetchData = async () => {
        try {
            const { page, pageSize } = paginationModel;
            const response = await instance.get(`/offer/list?page=${page}&items=${pageSize}`);
            setData(response.data.offers);
            setTotalRows(response.data.totalItems);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const columns = [
        { field: 'id', headerName: 'ID', width: 100 },
        { field: 'title', headerName: 'Name', width: 200 },
        { field: 'description', headerName: 'Description', width: 300 },
        { field: 'price', headerName: 'Price', width: 300 },
        {
            field: 'actions',
            headerName: 'Actions',
            width: 150,
            renderCell: (params) => (
                <div className="actionColumn">
                    <FiMoreVertical onClick={handleClick} />
                    <Menu
                        anchorEl={anchorEl}
                        open={Boolean(anchorEl)}
                        onClose={handleClose}
                        onClick={handleClose}
                    >
                        <MenuItem>
                            <Link to={`/offers/detail/${params.row.id}`} className="text-decoration-none">
                                <BsEye /> View Details
                            </Link>
                        </MenuItem>
                        <MenuItem onClick={() => handleDelete(params.row.id)}>
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
            await OfferService.deleteOffer(id);
            fetchData();
            showNotification('Offer deleted successfully', 'success');
        } catch (error) {
            showNotification('Failed to delete offer', 'error');
        }
    };

    const showNotification = (message, type) => {
        setNotification({
            open: true,
            message,
            type
        });
    };

    const closeNotification = () => {
        setNotification(prevState => ({
            ...prevState,
            open: false
        }));
    };

    const [anchorEl, setAnchorEl] = useState(null);
    const handleClick = (event) => {
        setAnchorEl(event.currentTarget);
    };
    const handleClose = () => {
        setAnchorEl(null);
    };

    const snackbarPosition: SnackbarOrigin = { vertical: 'bottom', horizontal: 'right' };

    return (
        <div className="card">
            <div className="card-header">
                <div className="card-title">
                    <h3>Offers list</h3>
                </div>
                <div className="card-actions">
                    <Link to="/offers/add" className="text-decoration-none">
                        <button className="button-standard">
                            Add offer
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

export default OffersList;
