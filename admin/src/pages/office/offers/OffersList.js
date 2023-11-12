import { MoreOutlined, EyeOutlined, FileAddOutlined, EditOutlined, DeleteOutlined, CloseOutlined, CheckOutlined } from '@ant-design/icons';
import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import { Button, Col, Row } from 'antd';
import { DataGrid } from '@mui/x-data-grid';
import { Menu, MenuItem } from '@mui/material';
import { Link } from 'react-router-dom';
import { useNavigate } from 'react-router-dom';
import FormatUtils from 'utils/format-utils';
import './Offer.css';
import { notification } from '../../../../node_modules/antd/es/index';

const OffersList = () => {
  const navigate = useNavigate();
  const [data, setData] = useState([]);
  const [totalRows, setTotalRows] = useState(0);
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
      marginBottom: '15px',
    },
  };

  const showNotification = (message, type) => {
    if (type === 'error') {
      notification.error({
        message: message,
        type: type,
        placement: 'bottomRight',
      });
      return;
    }

    notification.success({
      message: message,
      type: type,
      placement: 'bottomRight',
    });
  };

  const handlePageChange = (newModel) => {
    setPaginationModel(newModel);
  };

  const handleClick = (event, id, params) => {
    event.stopPropagation();
    setCurrentStateRowParams(params);
    setAnchorEl(event.currentTarget);
    setCurrentId(id);
  };

  const handleClose = () => {
    setAnchorEl(null);
    setCurrentId(null);
    setCurrentStateRowParams(null);
  };

  const fetchData = async () => {
    try {
      var { page, pageSize } = paginationModel;

      page += 1;

      const response = await instance.get(`/offer/list?page=${page}&items=${pageSize}`);
      setData(response.data.results.offers);
      setTotalRows(response.data.results.maxResults);
    } catch (error) {
      showNotification('Error fetching offers list', 'error');
    }
  };

  const handleAddOffer = () => {
    navigate('/office/offers/add');
  };

  const handleEditOffer = () => {
    navigate(`/office/offers/edit/${currentId}`);
  };

  const handleDeleteOffer = async () => {
    try {
      const response = await instance.delete(`/offer/${currentId}/delete`);

      if (response.status !== 200) {
        notification.error({
          type: 'error',
          message: "An error occurred. Can't delete offer.",
          placement: 'bottomRight',
        });
      }

      notification.success({
        message: 'Successfully deleted Offer',
        type: 'success',
        placement: 'bottomRight',
      });

      fetchData();
    } catch (e) {
      notification.error({
        type: 'error',
        message: "An error occurred. Can't delete offer.",
        description: e.message,
        placement: 'bottomRight',
      });
    }
  };

  useEffect(() => {
    fetchData();
  }, [paginationModel]);

  const columns = [
    { field: 'id', headerName: 'ID', width: 50 },
    { field: 'title', headerName: 'Title', width: 200 },
    {
      field: 'type',
      headerName: 'Type',
      width: 150,
      renderCell: (params) => (
        <div>{FormatUtils.getOfferType(params.row.type)}</div>
      ),
    },
    {
      field: 'duration',
      headerName: 'Duration',
      width: 100,
      renderCell: (params) => (
        <div>{FormatUtils.getOfferDuration(params.row.duration)}</div>
      ),
    },
    {
      field: 'price',
      headerName: 'Price',
      width: 150,
    },
    {
      field: 'discount',
      headerName: 'Discount',
      width: 100,
      renderCell: (params) => (
        <div>
          {FormatUtils.getOfferDiscount(params.row.discount, params.row.discountType, params.row.price)}
        </div>
      ),
    },
    {
      field: 'validDue',
      headerName: 'Valid Due',
      width: 250,
      renderCell: (params) => (
        <div>
          {params.row.validDue ? FormatUtils.formatDateWithTime(params.row.validDue.date) : '-'}
        </div>
      ),
    },
    {
      field: 'forStudents',
      headerName: 'Is for students',
      width: 100,
      renderCell: (params) => (
        <div className="badge" style={{ color: params.row.forStudents === true ? '#95de64' : '#ff7875', textAlign: 'center' }}>
          {params.row.forStudents === true ? <CheckOutlined /> : <CloseOutlined />}
        </div>
      ),
    },
    {
      field: 'forNewUsers',
      headerName: 'Is For New Users',
      width: 150,
      renderCell: (params) => (
        <div className="badge" style={{ color: params.row.forNewUsers === true ? '#95de64' : '#ff7875', textAlign: 'center' }}>
          {params.row.forNewUsers === true ? <CheckOutlined /> : <CloseOutlined />}
        </div>
      ),
    },
    {
      field: 'isDeleted',
      headerName: 'Deleted',
      width: 150,
      renderCell: (params) => (
        <div className="badge" style={{ color: params.row.isDeleted === true ? '#95de64' : '#ff7875', textAlign: 'center' }}>
          {params.row.isDeleted === true ? <CheckOutlined /> : <CloseOutlined />}
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
              <Link to={`/office/offers/detail/${currentId}`} className="text-decoration-none">
                <EyeOutlined /> View Details
              </Link>
            </MenuItem>
            {currentStateRowParams && currentStateRowParams.row.isDeleted !== true ?
              <div>
              <MenuItem onClick={handleEditOffer}>
                <EditOutlined /> Edit Offer
              </MenuItem>
              <MenuItem onClick={handleDeleteOffer}>
                <DeleteOutlined /> Delete Offer
              </MenuItem>
              </div> : null
            }
          </Menu>
        </div>
      ),
    },
  ];

  return (
    <>
      <MainCard title="Offers">
        <Row>
          <Col span={4} offset={20} style={styles.addUserButton}>
            <Button type="primary" onClick={handleAddOffer}>
              <FileAddOutlined /> Add Offer
            </Button>
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

export default OffersList;