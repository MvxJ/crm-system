import { MoreOutlined, EyeOutlined, FileAddOutlined, EditOutlined } from '@ant-design/icons';
import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row, Spin, notification } from '../../../../node_modules/antd/es/index';
import { DataGrid } from '@mui/x-data-grid';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import './Contract.css'
import FormatUtils from 'utils/format-utils';

const ContractsList = () => {
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
      setLoading(true);
      var { page, pageSize } = paginationModel;

      page += 1;

      const response = await instance.get(`/contracts/list?page=${page}&items=${pageSize}&order=DESC&orderBy=createDate`);
      
      if (response.data.results == null) {
        setLoading(false);
        return;
      }
      
      setData(response.data.results.contracts);
      setTotalRows(response.data.results.maxResults);
      setLoading(false);
    } catch (error) {
      setLoading(false);
      showNotification('Error fetching contracts list', 'error');
    } finally {
      setLoading(false);
    }
  };

  const handleAddContract = () => {
    navigate('/office/contracts/add');
  }

  const handleEditContract = () => {
    navigate(`/office/contracts/edit/${currentId}`);
  }

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  const columns = [
    { field: 'id', headerName: 'ID', width: 50 },
    { field: 'contractNumber', headerName: 'Contract Number', width: 150 },
    { field: 'city', headerName: 'City', width: 100 },
    { field: 'zipCode', headerName: 'Zip Code', width: 100 },
    { field: 'address', headerName: 'Address', width: 250 },
    {
      field: 'customer',
      headerName: 'Customer',
      width: 250,
      renderCell: (params) => (
        <div>
          {params.row.customer.email}
        </div>
      ),
    },
    {
      field: 'status',
      headerName: 'Status',
      width: 150,
      renderCell: (params) => (
        <div style={{ textAlign: 'center' }}>
          <div className="badge" style={{ backgroundColor: FormatUtils.getContractBadgeDetails(params.row.status).color }}>
            {FormatUtils.getContractBadgeDetails(params.row.status).text}
          </div>
        </div>
      ),
    },
    {
      field: 'createdAt',
      headerName: 'Created At',
      width: 250,
      renderCell: (params) => (
        <div>
          {FormatUtils.formatDateWithTime(params.row.createdAt.date)}
        </div>
      ),
    },
    { field: 'totalSum', headerName: 'Total Sum', width: 100 },
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
              <Link to={`/office/contracts/detail/${currentId}`} className="text-decoration-none">
                <EyeOutlined /> View Details
              </Link>
            </MenuItem>
            <MenuItem onClick={handleEditContract}>
              <EditOutlined /> Edit Contract
            </MenuItem>
          </Menu>
        </div>
      ),
    },
  ];

  return (
    <>
    <Spin spinning={loading} >
      <MainCard title="Contracts">
        <Row>
          <Col span={4} offset={20} style={styles.addUserButton}>
            <Button type="primary" onClick={handleAddContract}><FileAddOutlined /> Create Contract</Button>
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

export default ContractsList;
