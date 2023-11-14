import MainCard from 'components/MainCard';
import { Badge, Col, Row, notification, Tabs, Button } from '../../../../node_modules/antd/es/index';
import { Link, useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import FormatUtils from 'utils/format-utils';
import instance from 'utils/api';
import {
  EditOutlined,
  CheckOutlined
} from '@ant-design/icons';
import './ServiceRequest.css';
import ServiceRequestCommentsTab from 'components/ServiceRequestCommentsTab';

const ServiceRequestDetail = () => {
  const { id } = useParams();  
  const navigate = useNavigate();
  const [request, setRequest] = useState({
    id: id,
    customer: null,
    closed: true,
    createdDate: null,
    status: null,
    contract: null,
    user: null,
    localization: null,
    description: "",
    serviceVisits: 0,
    comments: 0
  });
  const tabs = [
    {
      key: 'visits',
      label: (
        <span className='tabLabel'>
          Service Visits
          <Badge count={request.serviceVisits} showZero color="rgb(24, 144, 255)" style={{ marginLeft: '5px' }} />
        </span>
      ),
      children: 'test',
    },
    {
      key: 'comments',
      label: (
        <span className='tabLabel'>
          Comments
          <Badge count={request.comments} showZero color="rgb(24, 144, 255)" style={{ marginLeft: '5px' }} />
        </span>
      ),
      children: (<ServiceRequestCommentsTab serviceRequestId={id}/>),
    },
  ];
  const styles = {
    userInicials: {
      width: '35px',
      height: '35px',
      borderRadius: '50%',
      border: '1px solid #1890ff',
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
      backgroundColor: '#e6f7ff',
      color: '#1890ff',
      fontWeight: 'bold'
    }
  }
  const fetchData = async () => {
    try {
      const response = await instance.get(`/service-requests/${id}/detail`);

      if (response.status != 200) {
        notification.error({
          type: 'error',
          messsage: "Can't fetch request detail.",
          placement: 'bottomRight'
        });
      }

      setRequest({
        id: id,
        customer: response.data.serviceRequest.customer,
        closed: response.data.serviceRequest.closed,
        createdDate: response.data.serviceRequest.createdDate,
        status: response.data.serviceRequest.status,
        contract: response.data.serviceRequest.contract,
        user: response.data.serviceRequest.user,
        localization: response.data.serviceRequest.localization,
        description: response.data.serviceRequest.description,
        serviceVisits: response.data.serviceRequest.serviceVisits,
        comments: response.data.serviceRequest.comments
      });

    } catch (e) {
      notification.error({
        type: 'error',
        messsage: "Can't fetch request detail.",
        description: e.message,
        placement: 'bottomRight'
      });
    }
  }

  const getUserInicials = (user) => {
    if (user) {
      const name = user?.name;
      const surname = user?.surname;

      return name.slice(0, 1).toUpperCase() + surname.slice(0, 1).toUpperCase();
    }

    return '';
  }

  const handleEditRequest = () => {
    navigate(`/service/requests/edit/${id}`);
  }

  const handleCloseRequest = async () => {
    try {
      const response = await instance.delete(`/service-requests/${id}/delete`);

      if (response.status != 200) {
        notification.error({
          message: "Can't closed contract.",
          type: "error",
          placement: "bottomRight"
        });

        return;
      }

      notification.success({
        message: "Successfully closed contract.",
        type: "success",
        placement: "bottomRight"
      });

      fetchData();
    } catch (e) {
      notification.error({
        message: "Can't close contract.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      })
    }
  }

  useEffect(() => {
    fetchData();
  }, [id]);

  return (
    <>
      <MainCard title={
        <div className='title-container'>
          <span>{'Service request #' + id}</span>
          <div className='badge' style={{ backgroundColor: FormatUtils.getServiceRequestStatusBadge(request.status).color }}>
            {FormatUtils.getServiceRequestStatusBadge(request.status).text}
          </div>
        </div>
      }>
        <Row style={{ textAlign: 'right' }}>
          <Col span={4}>
            <Row style={{ textAlign: 'left' }}>
              <Col span={24}>
                <b>Technician:</b>
              </Col>
              <Col span={24}>
                <Link to={`/customers/detail/${request.customer?.id}`}>
                  <div style={{ display: 'flex', alignItems: 'center', marginTop: '5px' }}>
                    <div style={styles.userInicials}>
                      {getUserInicials(request.customer)}
                    </div>
                    <div style={{ marginLeft: '5px' }}>
                      {request.customer?.name + ' ' + request.customer?.surname}
                    </div>
                  </div>
                </Link>
              </Col>
            </Row>
          </Col>
          <Col span={20}>
            <Button type="primary" onClick={handleEditRequest}>
              <EditOutlined /> Edit Service Request
            </Button>
            {request.closed != true ?
              <Button type="primary" style={{ marginLeft: '5px' }} onClick={handleCloseRequest}>
                <CheckOutlined /> Close Service Request
              </Button>
              : null}
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <b>Created At: </b>
          </Col>
          <Col span={24}>
            {request.createdDate ? FormatUtils.formatDateWithTime(request.createdDate.date) : '-'}
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <b>Customer:</b>
          </Col>
          <Col span={24}>
            <Link to={`/administration/users/detail/${request.user?.id}`}>
              <div style={{ display: 'flex', alignItems: 'center', marginTop: '5px' }}>
                <div style={styles.userInicials}>
                  {getUserInicials(request.user)}
                </div>
                <div style={{ marginLeft: '5px' }}>
                  {request.user?.name + ' ' + request.user?.surname}
                </div>
              </div>
            </Link>
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <b>Localization:</b>
          </Col>
          <Col span={24}>
            {request.localization?.address}
          </Col>
          <Col span={24}>
            {request.localization?.zipCode}
          </Col>
          <Col span={24}>
            {request.localization?.city}
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <b>Contract: </b>
          </Col>
          <Col>
            <Link to={`/office/contracts/detail/${request.contract?.id}`}>
              #{request.contract?.number}
            </Link>
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <b>Issue Description: </b>
          </Col>
          <Col span={24}>
            {request.description}
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <Tabs defaultActiveKey="visits" items={tabs} />
          </Col>
        </Row>
      </MainCard>
    </>
  )
};

export default ServiceRequestDetail;
