import { Typography } from '@mui/material';
import  { MoreOutlined, EyeOutlined, MailOutlined } from '@ant-design/icons';
import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row, notification } from '../../../../node_modules/antd/es/index';
import { DataGrid } from '@mui/x-data-grid';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import './Messages.css';

// ==============================|| SAMPLE PAGE ||============================== //

const MessagesList = () => {
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

      const response = await instance.get(`/message/list?page=${page}&items=${pageSize}&order=DESC&orderBy=createdDate`);
      setData(response.data.results.messages);
      setTotalRows(response.data.results.maxResults);
    } catch (error) {
      showNotification('Error fetching messages list', 'error');
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const options = {
      weekday: 'long',
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    };
  
    const formattedDate = date.toLocaleString('en-US', options);
  
    return `${formattedDate}`;
  }

  const handleAddUserClick = () => {
    navigate('/messages/create');
  }

  const getMessageTypeTextAndColor = (type) => {
    var typeObj = {text: '', color: ''};

    switch (type) {
      case 0:
        typeObj.text = 'Notification';
        typeObj.color = '#69c0ff';
        break;
      case 1:
        typeObj.text = 'Reminder';
        typeObj.color = '#ffd666';
        break;
      case 2:
        typeObj.text = 'Message';
        typeObj.color = '#e6f7ff';
        break;
      default:
        typeObj.text = 'Uknown type'
        typeObj.color = '#f5222d'
        break;
    }

    return typeObj;
  }

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  const columns = [
    { field: 'id', headerName: 'ID', width: 50 },
    { field: 'email', headerName: 'Email', width: 300 },
    { field: 'phone', headerName: 'Phone Number', width: 150 },
    { field: 'subject', headerName: 'Subject', width: 300 },
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
      field: 'type',
      headerName: 'Type',
      width: 100,
      renderCell: (params) => (
        <div className="badge" style={{ backgroundColor: getMessageTypeTextAndColor(params.row.type).color}}>
          {getMessageTypeTextAndColor(params.row.type).text}
        </div>
      ),
    },
    { 
      field: 'createdAt', 
      headerName: 'Created At', 
      width: 250,
      renderCell: (params) => (
        <div>
          {formatDate(params.row.createdAt.date)}
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
              <Link to={`/messages/detail/${currentId}`} className="text-decoration-none">
                <EyeOutlined /> View Details
              </Link>
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
            <Button type="primary" onClick={handleAddUserClick}><MailOutlined /> Create message</Button>
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
}

export default MessagesList;
