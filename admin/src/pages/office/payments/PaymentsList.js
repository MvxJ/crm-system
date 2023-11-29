import { MoreOutlined, EyeOutlined, FileAddOutlined, EditOutlined } from '@ant-design/icons';
import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row, Spin, notification } from '../../../../node_modules/antd/es/index';
import { DataGrid } from '@mui/x-data-grid';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import FormatUtils from 'utils/format-utils';
import './Payment.css';

const PaymentsList = () => {
  const navigate = useNavigate();
  const [data, setData] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
  const [loading, setLoading] = useState(false);
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
    setLoading(true);
    try {
      var { page, pageSize } = paginationModel;

      page += 1;

      const response = await instance.get(`/payments/list?page=${page}&items=${pageSize}&order=DESC&orderBy=createdAt`);
      setData(response.data.results.payments);
      setLoading(false);
    } catch (error) {
      setLoading(false);
      showNotification('Error fetching payments list', 'error');
    } finally {
      setLoading(false);
    }
  };

  const handleAddPayment = () => {
    navigate('/office/payments/add');
  }

  const handleEditPayment = () => {
    navigate(`/office/payments/edit/${currentId}`);
  }

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  const columns = [
    { field: 'id', headerName: 'ID', width: 50 },
    { field: 'amount', headerName: 'Amount', width: 100 },
    {
      field: 'paidBy',
      headerName: 'Paid By',
      width: 100,
      renderCell: (params) => (
        <div>
          {FormatUtils.getPaidBy(params.row.paidBy)}
        </div>
      ),
    },
    {
      field: 'bill',
      headerName: 'Bill',
      width: 250,
      renderCell: (params) => (
        <div>
          {params.row.bill.number}
        </div>
      ),
    },
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
          <div className="badge" style={{ backgroundColor: FormatUtils.getPaymentStatusBadge(params.row.status).color }}>
            {FormatUtils.getPaymentStatusBadge(params.row.status).text}
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
              <Link to={`/office/payments/detail/${currentId}`} className="text-decoration-none">
                <EyeOutlined /> View Details
              </Link>
            </MenuItem>
            { params.row.status == 0 ?
            <MenuItem onClick={handleEditPayment}>
              <EditOutlined /> Edit Payment
            </MenuItem> : null }
          </Menu>
        </div>
      ),
    },
  ];

  return (
    <>
      <Spin spinning={loading}>
      <MainCard title="Payments">
        <Row>
          <Col span={4} offset={20} style={styles.addUserButton}>
            <Button type="primary" onClick={handleAddPayment}><FileAddOutlined /> Add Payment</Button>
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

export default PaymentsList;
