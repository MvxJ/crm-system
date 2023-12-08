import { MoreOutlined, EyeOutlined, FileAddOutlined, EditOutlined, DownloadOutlined } from '@ant-design/icons';
import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row, Spin, notification } from '../../../../node_modules/antd/es/index';
import { DataGrid } from '@mui/x-data-grid';
import { Menu, MenuItem } from "@mui/material";
import { Link } from "react-router-dom";
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import FormatUtils from 'utils/format-utils';
import './Invoice.css';
import PdfUtils from 'utils/pdf-utils';

const InvoicesList = () => {
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

      const response = await instance.get(`/bill/list?page=${page}&items=${pageSize}&order=DESC&orderBy=dateOfIssue`);

      if (response.data.results != null) {
        setData(response.data.results.bills);
        setTotalRows(response.data.results.maxResults);
      }

      setLoading(false);
    } catch (error) {
      setLoading(false);
      showNotification('Error fetching invoices list', 'error');
    } finally {
      setLoading(false);
    }
  };

  const handleAdd = () => {
    navigate('/office/invoices/add');
  }

  const handleEdit = () => {
    navigate(`/office/invoices/edit/${currentId}`);
  }

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  const columns = [
    { field: 'id', headerName: 'ID', width: 50 },
    { field: 'number', headerName: 'Number', width: 150 },
    { field: 'amount', headerName: 'amount', width: 150 },
    {
      field: 'status',
      headerName: 'Status',
      width: 150,
      renderCell: (params) => (
        <div style={{ textAlign: 'center' }}>
          <div className="badge" style={{ backgroundColor: FormatUtils.getBillBadgeDetails(params.row.status).color }}>
            {FormatUtils.getBillBadgeDetails(params.row.status).text}
          </div>
        </div>
      ),
    },
    {
      field: 'dateOfIssue',
      headerName: 'Date of Issue',
      width: 250,
      renderCell: (params) => (
        <div>
          {FormatUtils.formatDateWithTime(params.row.dateOfIssue.date)}
        </div>
      ),
    },
    {
      field: 'payDue',
      headerName: 'Pay Due',
      width: 250,
      renderCell: (params) => (
        <div>
          {FormatUtils.formatDateWithTime(params.row.payDue.date)}
        </div>
      ),
    },
    {
      field: 'paymentDate',
      headerName: 'Payment Date',
      width: 250,
      renderCell: (params) => (
        <div>
          {params.row.paymentDate != null ? FormatUtils.formatDateWithTime(params.row.paymentDate?.date) : '-'}
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
              <Link to={`/office/invoices/detail/${currentId}`} className="text-decoration-none">
                <EyeOutlined /> View Details
              </Link>
            </MenuItem>
            {
              (params.row.status != 1 && params.row.status != 5) ?
              <MenuItem onClick={handleEdit}>
                <EditOutlined /> Edit Invoice
              </MenuItem> : null
            }
            <MenuItem>
              <Link onClick={() => PdfUtils.downloadPDF(params.row.fileName, 'open')} className="text-decoration-none">
                <EyeOutlined /> View File
              </Link>
            </MenuItem>
            <MenuItem>
              <Link onClick={() => PdfUtils.downloadPDF(params.row.fileName, 'download')} className="text-decoration-none">
                <DownloadOutlined /> Download File
              </Link>
            </MenuItem>
          </Menu>
        </div>
      ),
    },
  ];

  return (
    <>
      <Spin spinning={loading}>
      <MainCard title="Invoices">
        <Row>
          <Col span={4} offset={20} style={styles.addUserButton}>
            <Button type="primary" onClick={handleAdd}><FileAddOutlined /> Add Invoice</Button>
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

export default InvoicesList;
