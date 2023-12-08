import MainCard from 'components/MainCard';
import { MoreOutlined, EyeOutlined, DeleteOutlined, CheckOutlined, CloseOutlined, EditOutlined } from '@ant-design/icons';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row, Spin, notification } from '../../../../node_modules/antd/es/index';
import { DataGrid } from '@mui/x-data-grid';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import FormatUtils from 'utils/format-utils';
import './ServiceRequest.css';

const RequestsList = () => {
  const navigate = useNavigate();
  const [data, setData] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
  const [currentId, setCurrentId] = useState(null);
  const [loading, setLoading] = useState(false);
  const [paginationModel, setPaginationModel] = useState({
    pageSize: 25,
    page: 0,
  });
  const [anchorEl, setAnchorEl] = useState(null);

  const styles = {
    addButton: {
      textAlign: 'right',
      marginBottom: '15px'
    },
    userInicials: {
      width: '35px',
      height: '35px',
      borderRadius: '50%',
      border: '1px solid #1890ff',
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
      backgroundColor: '#e6f7ff',
      color: '#1890ff',
      fontWeight: 'bold'
    }
  }

  const getUserInicials = (user) => {
    if (user) {
      const name = user.name;
      const surname = user.surname;
  
      return name.slice(0,1).toUpperCase() + surname.slice(0,1).toUpperCase();
    }

    return '';
  }

  const columns = [
    { field: 'id', headerName: 'ID', width: 50 },
    {
      field: 'status',
      headerName: 'Status',
      width: 100,
      renderCell: (params) => (
        <div className='badge' style={{backgroundColor: FormatUtils.getServiceRequestStatusBadge(params.row.status).color}}>
          {FormatUtils.getServiceRequestStatusBadge(params.row.status).text}
        </div>
      ),
    },
    {
      field: 'contract',
      headerName: 'Contract',
      width: 150,
      renderCell: (params) => (
        <div>
          {params.row.contract.number}
        </div>
      ),
    },
    {
      field: 'customer',
      headerName: 'Customer',
      width: 300,
      renderCell: (params) => (
        <div>
          {params.row.customer.name + ' ' + params.row.customer.surname}
        </div>
      ),
    },
    {
      field: 'user',
      headerName: 'Technician',
      width: 300,
      renderCell: (params) => (
        <div>
          {params.row.user ? 
          <div style={{display: 'flex', justifyContent: 'center', alignItems: 'center'}}>
          <div style={styles.userInicials}>
            { getUserInicials(params.row.user) }
          </div>
            <div style={{marginLeft: '5px'}}>
              {params.row.user.name + ' ' + params.row.user.surname}
            </div>
          </div> : '-'
          }
        </div>
      ),
    },
    {
      field: 'closed',
      headerName: 'Is Closed',
      width: 100,
      renderCell: (params) => (
        <div className="badge" style={{ color: params.row.closed == true ? '#95de64' : '#ff7875', textAlign: 'center' }}>
          {params.row.closed == true ? <CheckOutlined /> : <CloseOutlined />}
        </div>
      ),
    },
    {
      field: 'createdDate',
      headerName: 'Created',
      width: 250,
      renderCell: (params) => (
        <div>
          {FormatUtils.formatDateWithTime(params.row.createdDate.date)}
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
                boxShadow: '0px 2px 4px rgba(0, 0, 0, 0.1)',
              },
            }}
          >
            <MenuItem>
              <Link to={`/service/requests/detail/${currentId}`} className="text-decoration-none">
                <EyeOutlined /> View Details
              </Link>
            </MenuItem>
            {
              currentId !== null && !data.find(row => row.id === currentId).isDeleted ?
                <MenuItem onClick={() => handleEditRequest(currentId)}>
                  <EditOutlined /> Edit Issue
                </MenuItem>
                : null
            }
            {
              currentId !== null && !data.find(row => row.id === currentId).isDeleted ?
                <MenuItem onClick={() => handleDeleteRequest(currentId)}>
                  <CheckOutlined /> Close Issue
                </MenuItem>
                : null
            }
          </Menu>
        </div>
      ),
    },
  ];

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

  const handleClick = (event, id, params) => {
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
      setLoading(true);
      var { page, pageSize } = paginationModel;

      page += 1;

      const response = await instance.get(`/service-requests/list?page=${page}&items=${pageSize}`);

      if (response.data.results != null) {
        setData(response.data.results.serviceRequests);
        setTotalRows(response.data.results.maxResults);
      }

      setLoading(false);
    } catch (error) {
      setLoading(false);
      showNotification('Error fetching service requests list', 'error');
    } finally {
      setLoading(false);
    }
  };

  const handleAddRequest = () => {
    navigate(`/service/requests/add`);
  }

  const handleEditRequest = (id) => {
    navigate(`/service/requests/edit/${id}`);
  }

  const handleDeleteRequest = async (id) => {
    try {
      const response = await instance.delete(`/service-requests/${id}/delete`);

      if (response.status != 200) {
        showNotification('An error occured during closing service request.', 'error');
      }

      showNotification('Successfully deleted service request.', 'success');
      fetchData();
    } catch (e) {
      showNotification('An error occured during closing service request.', 'error');
    }
  }

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  return (
    <>
    <Spin spinning={loading}>
      <MainCard title="Service Requests">
        <Row>
          <Col span={4} offset={20} style={styles.addButton} onClick={handleAddRequest}>
            <Button type="primary" >Add Service Request</Button>
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
  )
};

export default RequestsList;
