import MainCard from 'components/MainCard';
import { Button, Col, Form, Input, Row, Select, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';
import DebounceSelect from 'utils/DebounceSelect';
import  { SendOutlined } from '@ant-design/icons';
import { useState, useEffect } from 'react';
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';


const MessageForm = () => {
  const { TextArea } = Input;
  const [selectedCustomerId, setSelectedCustomerId] = useState(null);
  const [serviceRequests, setServiceRequests] = useState([]);
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    subject: '',
    message: '',
    type: 0,
    serviceRequest: '',
    phoneNumber: '',
    email: ''
  });
  
  async function searchCustomers(inputValue) {
    try {
      const response = await instance.get(`/customers/list?searchTerm=${inputValue}`);
      
      if (response.data.results.customers) {
        return response.data.results.customers.map((customer) => ({
          label: `#${customer.id} ${customer.firstName} ${customer.lastName} (${customer.socialSecurityNumber ? customer.socialSecurityNumber : 'empty'})`,
          value: customer.id,
        }));
      }

      return [];
    } catch (error) {
      return [];
    }
  }

  async function fetchServiceRequests(customerId) {
    try {
      const response = await instance.get(`/service-requests/list?customerId=${customerId.value}&status=0`);
      if (response.data.results.serviceRequests) {
        setServiceRequests(response.data.results.serviceRequests);
      }
    } catch (error) {
      setServiceRequests([]);
    }
  }

  const sendMessage = async () => {
    try {
      const request = {
        customer: parseInt(selectedCustomerId.value),
        message: formData.message,
        subject: formData.subject,
        type: parseInt(formData.type),
        email: formData.email,
        phoneNumber: formData.phoneNumber
      };

      if (formData.serviceRequest != null && formData.serviceRequest != '') {
        request.serviceRequest = formData.serviceRequest
      }

      const response = await instance.post(`/messages/create`, 
        request
      )

      if (response.status != 200) {
        notification.error({
          message: 'An error ocured during sending message.',
          type: 'error',
          placement: 'bottomRight'
        });
      }

      notification.success({
        message: 'Successfully send notification.',
        type: 'success',
        placement: 'bottomRight'
      });

      navigate(`/messages/detail/${response.data.results.id}`)
    } catch (e) {
      notification.error({
        message: 'An error ocured during sending message.',
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      });
    }
  }

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));  
  };

  useEffect(() => {
    if (selectedCustomerId) {
      fetchServiceRequests(selectedCustomerId);
    }
  }, [selectedCustomerId]);

  return (
  <>
    <MainCard title="Create Message">
      <Form layout="vertical" style={{ width: 100 + '%' }}>
        <Row>
          <Col span={22} offset={1} style={{textAlign: 'right'}}>
            <Button type="primary" onClick={sendMessage}><SendOutlined /> Send Message</Button>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <Form.Item label="Subject" required tooltip="This is a required field">
              <Input name="subject" onChange={handleInputChange} />
            </Form.Item>
          </Col>
        </Row>
        <Row>
          <Col span={10} offset={1}>
            <Form.Item label="Message Type" required tooltip="This is a required field">
              <Select
                name="type"
                defaultValue={0}
                options={[
                  { value: 0, label: 'Notification' },
                  { value: 1, label: 'Reminder' },
                  { value: 2, label: 'Message' },
                ]}
                onChange={(value) => {
                  setFormData((prevFormData) => ({ ...prevFormData, type: value }));
                }}
              />
            </Form.Item>
          </Col>
          <Col span={10} offset={2} >
            <Form.Item label="Customer" required tooltip="This is a required field">
                <DebounceSelect
                  mode="single"
                  placeholder="Search customer..."
                  fetchOptions={searchCustomers}
                  onChange={(value) => {
                    setSelectedCustomerId(value);
                  }}
                  style={{
                    width: '100%',
                  }}
                />
            </Form.Item>
          </Col>
        </Row>
        <Row>
          <Col span={10} offset={1}>
            <Form.Item label="Email" required tooltip="This is a required field">
              <Input name="email" onChange={handleInputChange} />
            </Form.Item>
          </Col>
          <Col span={10} offset={2} >
            <Form.Item label="Phone Number" required tooltip="This is a required field">
                <Input name="phoneNumber" onChange={handleInputChange} />
            </Form.Item>
          </Col>
        </Row>
        <Row>
          <Col span={10} offset={1}>
            <Form.Item label="Service Request">
              <Select
                name="serviceRequest"
                placeholder="Select a service request"
                onChange={(value) => {
                  setFormData((prevFormData) => ({ ...prevFormData, serviceRequest: value }));
                }}
              >
                {serviceRequests.map((request) => (
                  <Select.Option key={request.id} value={request.id}>
                    Service request #{request.id}
                  </Select.Option>
                ))}
              </Select>
            </Form.Item>
          </Col>
        </Row>
        <Row>
          <Col span={22} offset={1}>
            <Form.Item label="Message" required tooltip="This is a required field">
              <TextArea
                showCount
                maxLength={1500}
                style={{ height: 250, resize: 'none' }}
                placeholder="Message..."
                name="message"
                onChange={handleInputChange}
              />
            </Form.Item>
          </Col>
        </Row>
      </Form>
    </MainCard>
  </>
)};

export default MessageForm;
