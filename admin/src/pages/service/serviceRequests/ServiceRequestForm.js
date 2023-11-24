import MainCard from 'components/MainCard';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import { Button, Col, Form, Input, Row, Select, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';
import DebounceSelect from 'utils/DebounceSelect';

const ServiceRequestForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [contracts, setContracts] = useState([]);
  const [selectedUser, setSelectedUser] = useState(null);
  const [selectedContract, setSelectedContract] = useState(null);
  const [formData, setFormData] = useState({
    id: id,
    customerId: null,
    userId: null,
    createdDate: '',
    closeDate: '',
    isClosed: false,
    description: '',
    contractId: null,
    status: null
  });
  const { TextArea } = Input;

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

  async function searchUser(inputValue) {
    try {
      const response = await instance.get(`/users/list?searchTerm=${inputValue}`);

      if (response.data.results.users) {
        return response.data.results.users.map((user) => ({
          label: `#${user.id} ${user.name} ${user.surname} (${user.username})`,
          value: user.id,
        }));
      }

      return [];
    } catch (error) {
      return [];
    }
  }

  function fetchCustomerContracts(customerId) {
    serachContract(customerId);
  }

  async function serachContract(customerId) {
    try {
      var url = `/contracts/list`;

      if (customerId) {
        url += `?customerId=${customerId}`;
      }

      const response = await instance.get(url);

      if (response.data.results.contracts) {
        setContracts(response.data.results.contracts);
        return;
      }

      setContracts([]);
    } catch (error) {
      setContracts([]);
    }
  }

  const fetchData = async () => {
    try {
      if (id) {
        dayjs.extend(utc);

        const response = await instance.get(``);

        setFormData({
        });
      }
    } catch (error) {
      notification.error({
        message: "Can't fetch service request data.",
        description: error.message,
        type: "error",
        placement: "bottomRight",
      });
    }
  };

  useEffect(() => {
    fetchData();
  }, [id]);

  useEffect(() => {
    if (selectedCustomer) {
      fetchCustomerContracts(selectedCustomer.value);
    }
  }, [selectedCustomer]);

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  return (
    <>
      <MainCard title="Service Request Form">
        <Row style={{ textAlign: 'right' }}>
          <Col span={22} offset={1}>
            {id ?
              <Button type="primary">
                Save Service Request
              </Button> :
              <Button type="primary">
                Add Service Request
              </Button>
            }
          </Col>
        </Row>
        <Form layout="vertical" style={{ width: 100 + "%" }}>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="User"
              >
                <DebounceSelect
                  mode="single"
                  placeholder="Search user..."
                  onChange={(value) => {
                    setSelectedUser(value);
                  }}
                  fetchOptions={searchUser}
                  style={{
                    width: '100%',
                  }}
                />
              </Form.Item>
            </Col>
            {!id ?
              <Col span={10} offset={2}>
                <Form.Item
                  label="Customer"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: "Please select customer!",
                    },
                  ]}>
                  <DebounceSelect
                    mode="single"
                    placeholder="Search customer..."
                    fetchOptions={searchCustomers}
                    onChange={(value) => {
                      setSelectedCustomer(value);
                    }}
                    style={{
                      width: '100%',
                    }}
                  />
                </Form.Item>
              </Col> : null}
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Status"
              >
                <Select
                  value={formData.status}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "status", value } })
                  }
                  options={[
                    { value: null },
                    { value: 0, label: "OPENED" },
                    { value: 1, label: "REALIZATION" },
                    { value: 1, label: "CLOSED" },
                    { value: 1, label: "CANCELLED" },
                  ]}
                />
              </Form.Item>
            </Col>
            {(!id && selectedCustomer != null) ?
            <Col span={10} offset={2}>
              <Form.Item
                label="Contract"
                required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: "Please select contract!",
                    },
                  ]}
              >
                <Select
                  mode="single"
                  placeholder="Choose contract..."
                  onChange={(value) => {
                    setSelectedContract(value);
                  }}
                  style={{
                    width: '100%',
                  }}
                >
                  {contracts.map((contract) => (
                    <Select.Option key={contract.id} value={contract.id}>
                      Contract #{contract.id}
                    </Select.Option>
                  ))}
                </Select>
              </Form.Item>
            </Col> : null 
            }
          </Row>
          <Row>
            <Col span={22} offset={1}>
            <Form.Item
                label="Description"
              >
            <TextArea
                  showCount
                  maxLength={1500}
                  style={{ height: 120, resize: "none" }}
                  placeholder="Description..."
                  value={formData.description}
                  onChange={handleInputChange}
                  name="description"
                />
                </Form.Item>
            </Col>
          </Row>
        </Form>
      </MainCard>
    </>
  )
};

export default ServiceRequestForm;
