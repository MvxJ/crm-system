import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { Button, Col, Row, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';
import { UserOutlined, IssuesCloseOutlined, MailOutlined, PhoneOutlined, CalendarOutlined } from '@ant-design/icons';
import FormatUtils from 'utils/format-utils';
import './Messages.css'


const MessageForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
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
        phoneNumber: response.data.message.phone,
        createdAt: response.data.message.createdAt
      });

      console.log(message.createdAt);
    } catch (e) {
      notification.error({
        type: 'error',
        messsage: "Can't fetch message detail.",
        description: e.message,
        placement: 'bottomRight'
      });
    }
  }

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
          <Col span={22} offset={1} style={{ textAlign: 'right' }}>
            <Button type="primary" onClick={navigateUserDetail}>
              <UserOutlined /> {message.customer?.name} {message.customer?.surname}
            </Button>
            {message.serviceRequest ?
              <Button type="primary" onClick={navigateServiceRequest} style={{ marginLeft: '5px' }}>
                <IssuesCloseOutlined /> Service Request #{message.serviceRequest?.id}
              </Button>
              : null
            }
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <h4>Type:</h4>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <div className='badge' style={{ backgroundColor: FormatUtils.getMessageBadgeObj(message.type).color }}>
              {FormatUtils.getMessageBadgeObj(message.type).text}
            </div>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <h4>Subject:</h4>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            {message.subject}
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <h4>Send Date:</h4>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <CalendarOutlined /> {formatDate(message.createdAt?.date)}
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <h4>Recipient data: </h4>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
          <MailOutlined /> Email Address: {message.email}
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
          <PhoneOutlined /> Phone Number: {message.phoneNumber}
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <h4>Message: </h4>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            {message.message}
          </Col>
        </Row>
      </MainCard>
    </>
  )
};

export default MessageForm;
