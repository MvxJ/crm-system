import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import { useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { Button, Col, Row, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';

const MessageForm = () => {
  const { id } = useParams();
  const [message, setMessage] = useState({
    id: null,
    customer: null,
    serviceRequest: null,
    subject: null,
    message: null,
    type: null,
    email: null,
    phoneNumber: null,
    createdAt: null
  });

  const fetchData = async () => {
    try {
      const response = await instance.get(`/messages/detail/${id}`);

      if (response.status != 200) {
        notification.error({
          type: 'error',
          messsage: "Can't fetch message detail.",
          placement: 'bottomRight'
        });
      }

      setMessage({
        id: response.data.message.id,
        customer: response.data.message.customer,
        serviceRequest: response.data.message.serviceRequest,
        subject: response.data.message.subject,
        message: response.data.message.message,
        type: response.data.message.type,
        email: response.data.message.email,
        phoneNumber: response.data.message.phoneNumber,
        createdAt: response.data.message.createdAt
      });


    } catch (e) {
      notification.error({
        type: 'error',
        messsage: "Can't fetch message detail.",
        description: e.message,
        placement: 'bottomRight'
      });
    }
  }

  const getServiceRequestData = () => {

  }

  const getUserData = () => {

  }

  const navigateServiceRequest = () => {

  }

  const navigateUserDetail = () => {
    navigate(`/customers/detail/${message.customer.id}`);
  }

  useEffect(() => {
    fetchData();
  }, [id]);

  return (
  <>
    <MainCard title="Message Details">
      <Row>
        <Col span={22} offset={1} style={{textAlign: 'right'}}>
          <Button type="primary" onClick={navigateUserDetail}>
            Customer
          </Button>
          <Button type="primary" onClick={navigateServiceRequest}>
            Service Request
          </Button>
        </Col>
      </Row>
      <Row>
        <Col span={22} offset={1}>
          Subject - {message.subject}
        </Col>
      </Row>
      <Row>
        <Col span={22} offset={1}>
          Date
        </Col>
      </Row>
      <Row>
        <Col span={22} offset={1}>
           {message.message}
        </Col>
      </Row>
    </MainCard>
  </>
)};

export default MessageForm;
