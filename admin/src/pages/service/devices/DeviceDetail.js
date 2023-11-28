import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom'; // Removed unnecessary path
import { Button, Col, Row, notification } from 'antd'; // Removed unnecessary path
import instance from 'utils/api';
import { DeleteOutlined, EditOutlined, UserOutlined } from '@ant-design/icons'; // Replaced deprecated import path
import MainCard from 'components/MainCard';
import FormatUtils from 'utils/format-utils';
import { Link } from '../../../../node_modules/react-router-dom/dist/index';
import './Device.css';
import { Spin } from '../../../../node_modules/antd/es/index';

const DeviceDetail = () => {
  const { id } = useParams();
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();
  const [device, setDevice] = useState({
    id: id,
    model: {},
    serialNo: null,
    macAddress: null,
    status: null,
    boughtDate: null,
    soldDate: null,
    customer: null
  });

  const handleDelete = async () => {
    try {
      setLoading(true);
      const response = await instance.delete(`/devices/${id}/delete`);

      if (response.status != 200) {
        notification.error({
          message: "An error occured during deleting customer.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      navigate(`/service/devices`);
      setLoading(false);
      notification.success({
        message: "Successfully deleted device.",
        type: "success",
        placement: "bottomRight"
      });
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "An error occured during deleting customer.",
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      })
    } finally {
      setLoading(false);
    }
  }

  const handleEdit = () => {
    navigate(`/service/devices/edit/${id}`);
  }

  const handleCustomerAction = () => {
    navigate(`/customers/detail/${device.customer.id}`);
  }

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const response = await instance.get(`/devices/${id}`);

        setDevice({
          id: response.data.device.id,
          model: response.data.device.model,
          serialNo: response.data.device.serialNo,
          macAddress: response.data.device.macAddress,
          status: response.data.device.status,
          boughtDate: response.data.device.boughtDate,
          soldDate: response.data.device.soldDate,
          customer: response.data.device.user,
        });
        setLoading(false);
      } catch (error) {
        setLoading(false);
        notification.error({
          message: "Can't fetch device detail.",
          description: error.message,
          type: 'error',
          placement: 'bottomRight'
        });
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [id]);

  return (
    <>
    <Spin spinning={loading}>
      <MainCard title={        <div className='title-container'>
          <span>{'Device Detail #' + id}</span>
          <div className='badge' style={{backgroundColor: FormatUtils.getDeviceStatus(device.status).color}}>
            {FormatUtils.getDeviceStatus(device.status).text}
          </div>
        </div>}>
        <Row>
          <Col span={24} style={{ textAlign: 'right' }}>
            {device.customer != null ? (
              <Button style={{ marginLeft: '5px' }} type="primary" onClick={handleCustomerAction}><UserOutlined /> {device.customer.name + ' ' + device.customer.lastName}</Button>
            ) : null}
            <Button style={{ marginLeft: '5px' }} type="primary" onClick={handleEdit}><EditOutlined /> Edit</Button>
            <Button style={{ marginLeft: '5px' }} type="primary" danger onClick={handleDelete}><DeleteOutlined /> Delete</Button>
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <b>Bought Date: </b> {FormatUtils.formatDateWithTime(device.boughtDate?.date)}
          </Col>
        </Row>
        {device.soldDate != null ? (
          <Row>
            <Col span={24}>
              <b>Sold Date: </b> {FormatUtils.formatDateWithTime(device.soldDate?.date)}
            </Col>
          </Row>
        ) : null}
        <Row>
          <Col span={24}>
            <b>Mac Address: </b> {device.macAddress}
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <b>Serial NO: </b> {device.serialNo}
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <b>Model: </b><Link to={`/service/models/detail/${device.model.id}`}>{device.model.manufacturer} - {device.model.name}
            </Link> 
          </Col>
        </Row>
      </MainCard>
      </Spin>
    </>
  );
};

export default DeviceDetail;