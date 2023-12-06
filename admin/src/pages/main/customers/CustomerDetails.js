import MainCard from 'components/MainCard';
import { Badge, Button, Col, Row, Spin, Tabs, notification } from '../../../../node_modules/antd/es/index';
import {
  MessageOutlined,
  WalletOutlined,
  SolutionOutlined,
  FilePdfOutlined,
  IssuesCloseOutlined,
  EditOutlined,
  ClusterOutlined,
  DeleteOutlined,
  MailOutlined,
  PhoneOutlined,
  IdcardOutlined,
  CalendarOutlined,
  NotificationOutlined,
  SafetyOutlined,
  CaretDownOutlined,
  CaretUpOutlined
} from '@ant-design/icons';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import './Customers.css';
import instance from 'utils/api';
import CustomerIncoicesTab from 'components/CustomerInvoicesTab';
import CustomerContractsTab from 'components/CustomerContractsTab';
import CustomerPaymentsTab from 'components/CustomerPaymentsTab';
import CustomerMessagesTab from 'components/CustomerMessagesTab';
import CustomerServiceRequestsTab from 'components/CustomerServiceRequestsTab';
import CustomerDevicesTab from 'components/CustomerDevicesTab';
import AddressInfo from 'components/AddressInfo';

const CustomerDetails = () => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const { id } = useParams();
  const [showAddresses, setShowAddresses] = useState(false);
  const [customer, setCustomer] = useState({
    id: id,
    firstName: '',
    secondName: '',
    lastName: '',
    email: '',
    phoneNumber: '',
    birthDate: null,
    isVerified: false,
    isActive: false,
    socialSecurityNumber: '',
    numberOfContracts: 0,
    numberOfDevices: 0,
    numberOfServiceRequests: 0,
    numberOfBills: 0,
    numberOfPayments: 0,
    numberOfMessages: 0,
    twoFactorAuth: false,
    smsNotification: false,
    emailNotification: false,
    addresses: []
  });
  const tabs = [
    {
      key: 'contracts',
      label: (
        <span className='tabLabel'>
          <SolutionOutlined />
          Contracts
          <Badge count={customer.numberOfContracts} showZero color="rgb(24, 144, 255)" style={{ marginLeft: '5px' }} />
        </span>
      ),
      children: (<CustomerContractsTab customerId={id}/>),
    },
    {
      key: 'invoices',
      label: (
        <span className='tabLabel'>
          <FilePdfOutlined />
          Invoices
          <Badge count={customer.numberOfBills} showZero color="rgb(24, 144, 255)" style={{ marginLeft: '5px' }} />
        </span>
      ),
      children: (<CustomerIncoicesTab customerId={id}/>),
    },
    {
      key: 'payments',
      label: (
        <span className='tabLabel'>
          <WalletOutlined />
          Payments
          <Badge count={customer.numberOfPayments} showZero color="rgb(24, 144, 255)" style={{ marginLeft: '5px' }} />
        </span>
      ),
      children: (<CustomerPaymentsTab customerId={id}/>),
    },
    {
      key: 'service-requests',
      label: (
        <span className='tabLabel'>
          <IssuesCloseOutlined/>
          Service Requests
          <Badge count={customer.numberOfServiceRequests} showZero color="rgb(24, 144, 255)" style={{ marginLeft: '5px' }} />
        </span>
      ),
      children: (<CustomerServiceRequestsTab customerId={id}/>),
    },
    {
      key: 'messages',
      label: (
        <span className='tabLabel'>
          <MessageOutlined />
          Messages
          <Badge count={customer.numberOfMessages} showZero color="rgb(24, 144, 255)" style={{ marginLeft: '5px' }} />
        </span>
      ),
      children: (<CustomerMessagesTab customerId={id}/>),
    },
    {
      key: 'devices',
      label: (
        <span className='tabLabel'>
          <ClusterOutlined />
          Devices
          <Badge count={customer.numberOfDevices} showZero color="rgb(24, 144, 255)" style={{ marginLeft: '5px' }} />
        </span>
      ),
      children: (<CustomerDevicesTab customerId={id}/>)
    }
  ];

  const handleCustomerDelete = async () => {
    try {
      setLoading(true);
      const response = await instance.delete(`/customers/${id}/delete`);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Can't delete customer",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      navigate(`/customers`);

      setLoading(false);
      notification.success({
        message: 'Successfully deleted customer',
        type: 'success',
        placement: 'bottomRight'
      });
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Can't delete customer",
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

  const getCustomerNameAndSurname = () => {
    var string = customer.firstName + ' ';

    if (customer.secondName != null && customer.secondName.length > 0) {
      string += customer.secondName + ' ';
    }

    return string += customer.lastName;
  }

  const getCustomerInicials = () => {
    if (customer.firstName == null || customer.lastName == null) {
      return '';
    }

    return customer.firstName?.slice(0, 1).toUpperCase() + customer.lastName?.slice(0, 1).toUpperCase();
  }

  const getBadgeDetails = () => {
    if (!customer.isActive) {
      return { text: 'Deleted', color: '#f50' };
    }

    return customer.isVerified ? { text: 'Verified', color: 'hsl(102, 53%, 61%)' } : { text: 'No-Verified', color: '#f50' };
  }

  const formatBirthDate = (dateString) => {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
  }

  const handleCustomerEdit = () => {
    navigate(`/customers/edit/${id}`);
  }

  const fetchCustomerData = async () => {
    try {
      setLoading(true);
      const response = await instance.get(`/customers/${id}/detail`);

      if (response.status != 200) {
        setLoading(false);

        notification.error({
          message: "Can't fetch customer details.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      setCustomer({
        firstName: response.data.customer.firstName,
        secondName: response.data.customer.secondName,
        lastName: response.data.customer.lastName,
        email: response.data.customer.email,
        phoneNumber: response.data.customer.phoneNumber,
        birthDate: response.data.customer.birthDate,
        isVerified: response.data.customer.isVerified,
        isActive: response.data.customer.isActive,
        socialSecurityNumber: response.data.customer.socialSecurityNumber,
        numberOfContracts: response.data.customer.numberOfContracts,
        numberOfDevices: response.data.customer.numberOfDevices,
        numberOfServiceRequests: response.data.customer.numberOfServiceRequests,
        numberOfBills: response.data.customer.numberOfBills,
        numberOfPayments: response.data.customer.numberOfPayments,
        numberOfMessages: response.data.customer.numberOfMessages,
        twoFactorAuth: response.data.customer.twoFactorAuth,
        smsNotification: response.data.customer.smsNotification,
        emailNotification: response.data.customer.emailNotification,
        addresses: response.data.customer.addresses
      });
      setLoading(false);
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Can't fetch customer details.",
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

  const toggleAddresses = () => {
    setShowAddresses(!showAddresses);
  };

  useEffect(() => {
    fetchCustomerData();
  }, [id]
  );

  return (
    <>
    <Spin spinning={loading}>
      <MainCard title={`Customer Details #${id}`}>
        <Row>
          <Col span={22} offset={1} style={{ textAlign: 'right' }}>
            <Button type="primary" onClick={handleCustomerEdit}>
              <EditOutlined /> Edit
            </Button>
            {customer.isActive ?
              <Button type="primary" danger style={{ marginLeft: '5px' }} onClick={handleCustomerDelete}>
                <DeleteOutlined /> Delete
              </Button>
              :
              null
            }
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <h4>Customer Profile</h4>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <Row>
              <Col>
                <div className='headerContainer'>
                  <div className='customerProfileIcon'>
                    {getCustomerInicials()}
                  </div>
                  <div className='userName' style={{ marginLeft: '15px' }}>
                    {getCustomerNameAndSurname()}
                  </div>
                  <div className='badge' style={{ backgroundColor: getBadgeDetails().color, marginLeft: '15px' }}>
                    {getBadgeDetails().text}
                  </div>
                </div>
              </Col>
            </Row>
            <Row style={{ marginTop: '15px' }}>
              <MailOutlined />&nbsp;Email:&nbsp; {customer.email ? customer.email : 'empty'}
            </Row>
            <Row>
              <PhoneOutlined />&nbsp;Phone number:&nbsp; {customer.phoneNumber ? customer.phoneNumber : 'empty'}
            </Row>
            <Row>
              <IdcardOutlined />&nbsp;Social Security number:&nbsp; {customer.socialSecurityNumber ? customer.socialSecurityNumber : 'empty'}
            </Row>
            <Row>
              <CalendarOutlined />&nbsp;Birth date:&nbsp; {customer.birthDate ? formatBirthDate(customer.birthDate.date) : 'empty'}
            </Row>
            <Row>
              <SafetyOutlined />&nbsp;2fa enabled:&nbsp; {customer.twoFactorAuth == true ? <span style={{ color: 'hsl(102, 53%, 61%)' }}> true</span> : <span style={{ color: '#f50' }}> false</span>}
            </Row>
            <Row>
              <NotificationOutlined />&nbsp;Email Norifications:&nbsp; {customer.emailNotification == true ? <span style={{ color: 'hsl(102, 53%, 61%)' }}> enabled</span> : <span style={{ color: '#f50' }}> disabled</span>}
            </Row>
            <Row>
              <NotificationOutlined />&nbsp;Sms Notifications:&nbsp; {customer.smsNotification == true ? <span style={{ color: 'hsl(102, 53%, 61%)' }}> enabled</span> : <span style={{ color: '#f50' }}> disabled</span>}
            </Row>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1} onClick={toggleAddresses} style={{cursor: 'pointer'}}>
            <h4>Addresses { showAddresses == false ? <CaretDownOutlined /> : <CaretUpOutlined /> }</h4>
          </Col>
        </Row>
        {showAddresses && (
          <Row id="addressesRow">
            <Col span={22} offset={1}>
              <Row>
                <Col span={12}>
                  <Row>
                    <AddressInfo addresses={customer.addresses} type="contact" />
                  </Row>
                </Col>
                <Col span={12}>
                  <Row>
                    <AddressInfo addresses={customer.addresses} type="billing" />
                  </Row>
                </Col>
              </Row>
            </Col>
          </Row>
        )}
        <Row>
          <Col span={22} offset={1}>
            <h4>Customer data</h4>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <Tabs defaultActiveKey="contracts" items={tabs} />
          </Col>
        </Row>
      </MainCard>
      </Spin>
    </>
  )
};

export default CustomerDetails;
