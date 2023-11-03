import { Typography } from '@mui/material';
import  { MoreOutlined, DeleteOutlined, EyeOutlined, UserAddOutlined, EditOutlined } from '@ant-design/icons';
import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row, notification } from '../../../../node_modules/antd/es/index';
import { DataGrid } from '@mui/x-data-grid';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';

const UsersList = () => {
  const navigate = useNavigate();
  const [data, setData] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
  const [currentId, setCurrentId] = useState(null);
  const [paginationModel, setPaginationModel] = useState({
    pageSize: 25,
    page: 1,
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
      // await UsersService.deleteUser(id);
      fetchData();
      setAnchorEl(null);
      setCurrentId(null);
      showNotification('User deleted successfully', 'success');
    } catch (error) {
      showNotification('Failed to delete user', 'error');
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

  const fetchData = async () => {
    try {
      var { page, pageSize } = paginationModel;

      if (page < 1) {
        page = 1;
      }

      const response = await instance.get(`/users/list?page=${page}&items=${pageSize}`);
      setData(response.data.results.users);
      setTotalRows(response.data.results.maxResults);
    } catch (error) {
      showNotification('Error fetching users list', 'error');
    }
  };

  const handleAddUserClick = () => {
    navigate('/administration/users/add');
  }

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
          <MoreOutlined onClick={(event) => handleClick(event, params.row.id)} />
          <Menu
            anchorEl={anchorEl}
            open={Boolean(anchorEl)}
            onClose={handleClose}
            onClick={handleClose}
          >
            <MenuItem>
              <Link to={`/administration/users/detail/${currentId}`} className="text-decoration-none">
                <EyeOutlined /> View Details
              </Link>
            </MenuItem>
            <MenuItem>
              <Link to={`/administration/users/edit/${currentId}`} className="text-decoration-none">
                <EditOutlined /> Edit user
              </Link>
            </MenuItem>
            <MenuItem onClick={() => handleDelete(currentId)}>
              <DeleteOutlined /> Delete
            </MenuItem>
          </Menu>
        </div>
      ),
    },
  ];

  return (
    <>
      <MainCard title="Users">
      <Row>
        <Col span={4} offset={20} style={styles.addUserButton}>
          <Button type="primary" onClick={handleAddUserClick}><UserAddOutlined /> Add User</Button>
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
    </>
  );
};

export default UsersList;