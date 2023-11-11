import MainCard from 'components/MainCard';
import { MoreOutlined, EyeOutlined, DeleteOutlined, CheckOutlined, CloseOutlined, AppstoreAddOutlined, EditOutlined } from '@ant-design/icons';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row, notification } from '../../../../node_modules/antd/es/index';
import { DataGrid } from '@mui/x-data-grid';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import './Device.css';
import FormatUtils from 'utils/format-utils';

const DevicesList = () => {
  const navigate = useNavigate();
  const [data, setData] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
  const [currentId, setCurrentId] = useState(null);
  const [paginationModel, setPaginationModel] = useState({
    pageSize: 25,
    page: 0,
  });
  const [anchorEl, setAnchorEl] = useState(null);

  const styles = {
    addButton: {
      textAlign: 'right',
      marginBottom: '15px'
    }
  }

  const columns = [
    { field: 'id', headerName: 'ID', width: 50 },
    {
      field: 'model',
      headerName: 'model',
      width: 300,
    },
    {
      field: 'macAddress',
      headerName: 'Mac Address',
      width: 300,
    },
    {
      field: 'serialNo',
      headerName: 'Serial NO',
      width: 300
    },
    {
      field: 'status',
      headerName: 'Status',
      width: 150,
      renderCell: (params) => (
        <div className='badge' style={{backgroundColor: FormatUtils.getDeviceStatus(params.row.status).color}}>
          {FormatUtils.getDeviceStatus(params.row.status).text}
        </div>
      ),
    },
    {
      field: 'type',
      headerName: 'Type',
      width: 100,
      renderCell: (params) => (
        FormatUtils.getDeviceType(params.row.type)
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
              <Link to={`/service/devices/details/${currentId}`} className="text-decoration-none">
                <EyeOutlined /> View Details
              </Link>
            </MenuItem>
            {
              currentId !== null && !data.find(row => row.id === currentId).isDeleted ?
                <MenuItem onClick={navigateEdit}>
                  <EditOutlined /> Edit Device
                </MenuItem>
                : null
            }
            {
              currentId !== null && !data.find(row => row.id === currentId).isDeleted ?
                <MenuItem onClick={handleDelete}>
                  <DeleteOutlined /> Delete Device
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
      var { page, pageSize } = paginationModel;

      page += 1;

      const response = await instance.get(`/devices/list?page=${page}&items=${pageSize}`);
      setData(response.data.results.devices);
      setTotalRows(response.data.results.maxResults);
    } catch (error) {
      showNotification('Error fetching devices list', 'error');
    }
  };

  const handleDelete = async () => {
    try {
      const response = await instance.delete(`/devices/${currentId}/delete`);

      if (response.status != 200) {
        notification.error({
          message: "An error occured during deleting customer.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      notification.success({
        message: "Successfully deleted device.",
        type: "success",
        placement: "bottomRight"
      });

      fetchData();
    } catch (e) {
      notification.error({
        message: "An error occured during deleting customer.",
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      })
    }
  }

  const navigateEdit = () => {
    navigate(`/service/devices/edit/${currentId}`);
  }

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  return (
  <>
    <MainCard title="Devices">
    <Row>
          <Col span={4} offset={20} style={styles.addButton} >
            <Button type="primary" ><AppstoreAddOutlined /> Add Device</Button>
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
)};

export default DevicesList;
