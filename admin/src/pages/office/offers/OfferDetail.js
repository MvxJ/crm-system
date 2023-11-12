import MainCard from 'components/MainCard';
import './Offer.css'
import { Button, Col, Row, Table, Tag, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import FormatUtils from 'utils/format-utils';
import { Menu, MenuItem } from '@mui/material';
import { Link } from 'react-router-dom';
import {
  MoreOutlined,
  EyeOutlined,
  DeleteOutlined,
  EditOutlined,
} from '@ant-design/icons';
import './Offer.css';

const OfferDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [anchorEl, setAnchorEl] = useState(null);
  const [offer, setOffer] = useState({
    id: id,
    title: "",
    description: "",
    duration: null,
    downloadSpeed: 0,
    uploadSpeed: 0,
    numberOfCanals: null,
    type: null,
    price: 0,
    discount: 0,
    discountType: null,
    forNewUsers: false,
    forStudents: false,
    validDue: null,
    isDeleted: false,
    devices: []
  });

  const deviceColumns = [
    {
      title: 'Name',
      dataIndex: 'name',
      key: 'name'
    },
    {
      title: 'Type',
      dataIndex: 'type',
      key: 'type',
      render: (type) => {
        const deviceType = FormatUtils.getDeviceType(type)

        return (
          <>
            <Tag color={'#bfbfbf'}>
              {deviceType}
            </Tag>
          </>
        )
      }
    },
    {
      title: 'Actions',
      dataIndex: 'id',
      key: 'id',
      render: (id) => {
        const handleClick = (event, id) => {
          event.stopPropagation();
          setAnchorEl(event.currentTarget);
        }

        const handleClose = () => {
          setAnchorEl(null);
        }

        return (
          <div className="actionColumn">
            <MoreOutlined onClick={(event) => handleClick(event, id)} />
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
                <Link to={`/service/models/detail/${id}`} className="text-decoration-none">
                  <EyeOutlined /> View Device Details
                </Link>
              </MenuItem>
            </Menu>
          </div>
        );
      }
    }
  ]

  const fetchData = async () => {
    try {
      const response = await instance.get(`/offer/${id}/detail`);

      if (response.status != 200) {
        notification.error({
          type: 'error',
          messsage: "Can't fetch offer detail.",
          placement: 'bottomRight'
        });
      }

      setOffer({
        id: id,
        title: response.data.offer.title,
        description: response.data.offer.description,
        duration: response.data.offer.duration,
        downloadSpeed: response.data.offer.downloadSpeed,
        uploadSpeed: response.data.offer.uploadSpeed,
        numberOfCanals: response.data.offer.numberOfCanals,
        type: response.data.offer.type,
        price: response.data.offer.price,
        discount: response.data.offer.discount,
        discountType: response.data.offer.discountType,
        forNewUsers: response.data.offer.forNewUsers,
        forStudents: response.data.offer.forStudents,
        validDue: response.data.offer.validDue,
        isDeleted: response.data.offer.isDeleted,
        devices: response.data.offer.devices
      });
    } catch (e) {
      notification.error({
        type: 'error',
        messsage: "Can't fetch offer detail.",
        description: e.message,
        placement: 'bottomRight'
      });
    }
  }

  const handleEditClick = () => {
    navigate(`/office/offers/edit/${id}`);
  }

  const handleDeleteClick = async () => {
    try {
      const response = await instance.delete(`/offer/${id}/delete`);

      if (response.status != 200) {
        notification.error({
          type: 'error',
          message: "An error occured. Can't deleted offer.",
          placement: 'bottomRight'
        });
      }

      notification.success({
        message: "Successfully deleted Offer",
        type: "success",
        placement: "bottomRight"
      });

      navigate(`/office/offers`);
    } catch (e) {
      notification.error({
        type: 'error',
        message: "An error occured. Can't deleted offer.",
        description: e.message,
        placement: 'bottomRight'
      });
    }
  }

  useEffect(() => {
    fetchData();
  }, [id]);

  return (
    <>
      <MainCard title={
        <div className='title-container'>
          <span>{'Offer Detail #' + id}</span>
          <div style={{ display: 'flex' }}>
            <div className='badge' style={{ backgroundColor: '#BA55D3' }}>
              {FormatUtils.getOfferType(offer.type)}
            </div>
            {offer.isDeleted == true ?
              <div className='badge' style={{ backgroundColor: '#ff7875' }}>
                Deleted
              </div>
              : null}
          </div>
          {offer.isDeleted == false ?
            <div style={{ display: 'flex' }}>
              {offer.forNewUsers == true ?
                <div className='badge' style={{ backgroundColor: '#95de64' }}>
                  For New Users
                </div>
                : null}
              {offer.forStudents == true ?
                <div className='badge' style={{ backgroundColor: '#95de64' }}>
                  For Students
                </div>
                : null}
            </div>
            : null}
        </div>
      }>
        <Row>
          <Col span={24} style={{ textAlign: 'right' }}>
            {offer.isDeleted != true ?
              <div>
                <Button type="primary" onClick={handleEditClick}>
                  <EditOutlined /> Edit Offer
                </Button>
                <Button type="primary" danger style={{ marginLeft: '5px' }} onClick={handleDeleteClick}>
                  <DeleteOutlined /> Delete Offer
                </Button>
              </div>
              : null}
          </Col>
        </Row>
        <Row>
          <Col span={11}>
            <Row>
              <h4>Offer Details:</h4>
            </Row>
            <Row>
              <Col span={24}>
                <table style={{
                  width: '100%',
                  border: '1px solid lightgray',
                  borderCollapse: 'collapse',
                }}>
                  <thead>
                    <tr>
                      <th style={{
                        backgroundColor: '#f5f5f5',
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Name:</th>
                      <th style={{
                        backgroundColor: '#f5f5f5',
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Value:</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Title</td>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>{offer.title}</td>
                    </tr>
                    <tr>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Price</td>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>{offer.price}</td>
                    </tr>
                    <tr>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Discount</td>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>{FormatUtils.getOfferDiscount(offer.discount, offer.discountType, offer.price)}</td>
                    </tr>
                    <tr>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Valid Due</td>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>{offer.validDue != null ? FormatUtils.formatDateWithTime(offer.validDue?.date) : '-'}</td>
                    </tr>
                  </tbody>
                </table>
              </Col>

            </Row>
            <Row>
              <Col span={24}>
                <h4>Description</h4>
              </Col>
              <Col span={24}>
                {offer.description}
              </Col>
            </Row>
          </Col>
          <Col span={11} offset={2}>
            <Row>
              <Col span={24}>
                <h4>Service details: </h4>
              </Col>
              <Col span={24}>
                <table style={{
                  width: '100%',
                  border: '1px solid lightgray',
                  borderCollapse: 'collapse',
                }}>
                  <thead>
                    <tr>
                      <th style={{
                        backgroundColor: '#f5f5f5',
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Name:</th>
                      <th style={{
                        backgroundColor: '#f5f5f5',
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Params:</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Upload Speed</td>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>{offer.uploadSpeed != null ? offer.uploadSpeed : 'none'}</td>
                    </tr>
                    <tr>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Download Speed</td>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>{offer.downloadSpeed != null ? offer.downloadSpeed : 'none'}</td>
                    </tr>
                    <tr>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Number of Channels</td>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>{offer.numberOfCanals != null ? offer.numberOfCanals : 'none'}</td>
                    </tr>
                    <tr>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        borderRight: '1px solid #ddd',
                        textAlign: 'left',
                      }}>Duration</td>
                      <td style={{
                        padding: '10px',
                        borderBottom: '1px solid #ddd',
                        textAlign: 'left',
                      }}>{FormatUtils.getOfferDuration(offer.duration)}</td>
                    </tr>
                  </tbody>
                </table>
              </Col>
            </Row>
          </Col>
        </Row>

        {offer.devices != null && offer.devices.length > 0 ?
          <div>
            <Row>
              <h4>Devices: </h4>
            </Row>
            <Row>
              <Col span={24}>
                <Table columns={deviceColumns} dataSource={offer.devices} bordered size="small" pagination={{
                  position: ['none', 'none'],
                }} />
              </Col>
            </Row>
          </div>
          : null}
      </MainCard>
    </>
  )
};

export default OfferDetail;
