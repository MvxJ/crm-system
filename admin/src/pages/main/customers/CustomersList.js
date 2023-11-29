import { Typography } from '@mui/material';
import  { MoreOutlined, DeleteOutlined, EyeOutlined, UserAddOutlined, EditOutlined } from '@ant-design/icons';
import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row, Spin, notification } from '../../../../node_modules/antd/es/index';
import { DataGrid } from '@mui/x-data-grid';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import './Customers.css';

const CustomersList = () => {
  const navigate = useNavigate();
  const [data, setData] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
  const [loading, setLoading] = useState(false);
  const [currentId, setCurrentId] = useState(null);
  const [currentStateRowParams, setCurrentStateRowParams] = useState(null);
  const [paginationModel, setPaginationModel] = useState({
    pageSize: 25,
    page: 0,
  });
  const [anchorEl, setAnchorEl] = useState(null);

  const styles = {
    addUserButton: {
      textAlign: 'right',
      marginBottom: '15px'
    }
  }

  const showNotification = (message, type) => {
    if (type == 'error') {
      notification.error({
        message: message,
        type: type,
        placement: 'bottomRight'
      });
      return;
    }

    notification.success({
      message: message,
      type: type,
      placement: 'bottomRight'
    });
  }

  const handlePageChange = (newModel) => {
    setPaginationModel(newModel);
  };

  const handleDelete = async (id) => {
    try {

      fetchData();
      setAnchorEl(null);
      setCurrentId(null);
      showNotification('Customer deleted successfully.', 'success');
    } catch (error) {
      showNotification('Failed to delete customer.', 'error');
    }
  };

  const handleClick = (event, id, params) => {
    event.stopPropagation();
    setAnchorEl(event.currentTarget);
    setCurrentStateRowParams(params);
    setCurrentId(id);
  };

  const handleClose = () => {
    setAnchorEl(null);
    setCurrentId(null);
    setCurrentStateRowParams(null);
  };

  const fetchData = async () => {
    try {
      setLoading(true);
      var { page, pageSize } = paginationModel;

      page += 1;

      const response = await instance.get(`/customers/list?page=${page}&items=${pageSize}`);
      setData(response.data.results.customers);
      setTotalRows(response.data.results.maxResults);
      setLoading(false);
    } catch (error) {
      setLoading(false);
      showNotification('Error fetching customers list', 'error');
    } finally {
      setLoading(false);
    }
  };

  const handleAddUserClick = () => {
    navigate('/customers/add');
  }

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  const columns = [
    { field: 'id', headerName: 'ID', width: 50 },
    { field: 'firstName', headerName: 'First Name', width: 200 },
    { field: 'secondName', headerName: 'Second Name', width: 200 },
    { field: 'lastName', headerName: 'Surname', width: 200 },
    { field: 'email', headerName: 'Email', width: 350 },
    { field: 'phoneNumber', headerName: 'Phone Number', width: 150 },
    {
      field: 'isVerified',
      headerName: 'Verified',
      width: 100,
      renderCell: (params) => (
        <div className="badge" style={{ backgroundColor: params.row.isVerified ? 'hsl(102, 53%, 61%)' : '#f50' }}>
          {params.row.isVerified ? 'Verified' : 'No-Verified'}
        </div>
      ),
    },
    {
      field: 'isActive',
      headerName: 'Active',
      width: 100,
      renderCell: (params) => (
        <div className="badge" style={{ backgroundColor: params.row.isActive ? 'hsl(102, 53%, 61%)' : '#f50' }}>
          {params.row.isActive ? 'Active' : 'Deleted'}
        </div>
      ),
    },
    {
      field: 'actions',
      headerName: 'Actions',
      type: 'actions',
      width: 100,
      renderCell: (params) => (
        <div className="actionColumn">
          <MoreOutlined onClick={(event) => handleClick(event, params.row.id, params)} />
          <Menu
            anchorEl={anchorEl}
            open={Boolean(anchorEl)}
            onClose={handleClose}
            onClick={handleClose}
            PaperProps={{
              style: {
                boxShadow: '0px 2px 4px rgba(0, 0, 0, 0.1)', // Adjust the shadow as needed
              },
            }}
          >
            <MenuItem>
              <Link to={`/customers/detail/${currentId}`} className="text-decoration-none">
                <EyeOutlined /> View Details
              </Link>
            </MenuItem>
            <MenuItem>
              <Link to={`/customers/edit/${currentId}`} className="text-decoration-none">
                <EditOutlined /> Edit Customer
              </Link>
            </MenuItem>
            {currentStateRowParams && currentStateRowParams.row.isActive ? (
              <MenuItem onClick={() => handleDelete(currentId)}>
                <DeleteOutlined /> Delete
              </MenuItem>
            ) : null}
          </Menu>
        </div>
      ),
    },
  ];

  return (
    <>
      <Spin spinning={loading}>
      <MainCard title="Users">
        <Row>
          <Col span={4} offset={20} style={styles.addUserButton}>
            <Button type="primary" onClick={handleAddUserClick}><UserAddOutlined /> Add Customer</Button>
          </Col>
        </Row>
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
      </MainCard>
      </Spin>
    </>
  );
}

export default CustomersList;
