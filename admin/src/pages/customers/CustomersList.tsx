import React, { useEffect, useState } from "react";
import instance from "../../service/api.service";
import { DataGrid, GridRenderCellParams } from "@mui/x-data-grid";
import { Link } from "react-router-dom";
import { FiMoreVertical, FiTrash2 } from "react-icons/fi";
import { Menu, MenuItem } from "@mui/material";
import { BsEye } from "react-icons/bs";
import CustomersService from "../../service/customer.service";

export interface ICustomersListPageProps {}

const CustomersList: React.FunctionComponent<ICustomersListPageProps> = (
  props
) => {
  const [data, setData] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
  const [currentId, setCurrentId] = useState(null);
  const [paginationModel, setPaginationModel] = useState({
    pageSize: 10,
    page: 0,
  });
  const [anchorEl, setAnchorEl] = useState(null);

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  const handlePageChange = (newModel) => {
    setPaginationModel(newModel);
  };

  const handleDelete = async (id) => {
    try {
      await CustomersService.deleteCustomer(id);
      fetchData();
      setAnchorEl(null);
      setCurrentId(null);
    } catch (error) {
      console.error("Error deleting customer:", error);
    }
  };

  const handleClick = (event, id) => {
    event.stopPropagation();
    setAnchorEl(event.currentTarget);
    setCurrentId(id);
  };

  const handleClose = () => {
    setAnchorEl(null);
    setCurrentId(null);
  };


  const columns = [
    { field: "id", headerName: "ID", width: 100 },
    { field: "email", headerName: "Email", width: 300 },
    { field: "name", headerName: "Name", width: 300 },
    { field: "surname", headerName: "Surname", width: 300 },
    { field: "phoneNumber", headerName: "Phone number", width: 300},
    {
      field: "actions",
      headerName: "Actions",
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
                    <Link to={`/customers/detail/${currentId}`} className="text-decoration-none">
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

  const fetchData = async () => {
    try {
      const { page, pageSize } = paginationModel;
      const response = await instance.get(
        `/customers/list?page=${page}&items=${pageSize}`
      );
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
          <h3>Customers list</h3>
        </div>
        <div className="card-actions">
          <Link to="/customers/add" className="text-decoration-none">
            <button className="button-standard">Add customer</button>
          </Link>
        </div>
      </div>
      <div className="card-body">
        <div style={{ height: "85vh", width: "100%" }}>
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
    </div>
  );
};

export default CustomersList;
