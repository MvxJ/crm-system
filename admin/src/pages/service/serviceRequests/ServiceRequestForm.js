import MainCard from 'components/MainCard';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import { Button, Col, Form, Input, Row, Select, Spin, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';
import DebounceSelect from 'utils/DebounceSelect';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'
import FormatUtils from 'utils/format-utils';

const ServiceRequestForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [contracts, setContracts] = useState([]);
  const [loading, setLoading] = useState(false);
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

  const createRequestObj = () => {
    const obj = {
      description: formData.description,
    };

    if (formData.status) {
      obj.status = formData.status;
    }

    if (selectedUser) {
      obj.user = selectedUser.value;
    }

    if (!id) {
      obj.customer = selectedCustomer.value;
      obj.contract = selectedContract;
    }

    return obj;
  }

  const saveReuqest = async () => {
    try {
      setLoading(true);
      const request = createRequestObj();
      const response = await instance.patch(`/service-requests/${id}/edit`, request);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Can't update service request.",
          type: "error",
          placement: "bottomRight"
        });

        return;
      }

      setLoading(false);
      notification.success({
        message: "Updated service request.",
        type: "success",
        placement: "bottomRight"
      });
      navigate(`/service/requests/detail/${id}`);
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Can't update service request.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      })
    } finally {
      setLoading(false);
    }
  }

  const addRequest = async () => {
    try {
      setLoading(true);
      const request = createRequestObj();
      const response = await instance.post(`/service-requests/add`, request);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Can't add service request.",
          type: "error",
          placement: "bottomRight"
        });

        return;
      }

      setLoading(false);
      notification.success({
        message: "Addedd service request.",
        type: "success",
        placement: "bottomRight"
      });
      navigate(`/service/requests/detail/${response.data.serviceRequest.id}`);
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Can't add service request.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      })
    } finally {
      setLoading(false);
    }
  }

  const fetchData = async () => {
    try {
      if (id) {
        setLoading(true);
        dayjs.extend(utc);

        const response = await instance.get(`/service-requests/${id}/detail`);

        if (response.status != 200) {
          setLoading(false);
          notification.error({
            type: "error",
            message: "Can't fetch service request edtail.",
            placement: "bottomRight"
          });
          return;
        }

        setFormData({
          id: id,
          customerId: response.data.serviceRequest?.customer?.id,
          userId: response.data.serviceRequest?.user?.id,
          createdDate: response.data.serviceRequest.createdDate,
          closeDate: response.data.serviceRequest.closeDate,
          isClosed: response.data.serviceRequest.isClosed,
          description: response.data.serviceRequest.description,
          contractId: response.data.serviceRequest.contract.id,
          status: response.data.serviceRequest.status
        });

        if (response.data.serviceRequest.user) {
          setSelectedUser({
            label: `#${response.data.serviceRequest.user.id} ${response.data.serviceRequest.user.name} ${response.data.serviceRequest.user.surname} (${response.data.serviceRequest.user.username})`,
            value: response.data.serviceRequest.user.id,
          });
        }
        setLoading(false);
      }
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't fetch service request data.",
        description: error.message,
        type: "error",
        placement: "bottomRight",
      });
    } finally {
      setLoading(false);
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
      <Spin spinning={loading}>
      <MainCard title="Service Request Form">
        <Row style={{ textAlign: 'right' }}>
          <Col span={22} offset={1}>
            {id ?
              <Button type="primary" onClick={saveReuqest}>
                Save Service Request
              </Button> :
              <Button type="primary" onClick={addRequest}>
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
                  value={selectedUser}
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
                    { value: 2, label: "CLOSED" },
                    { value: 3, label: "CANCELLED" },
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
                      #{contract.contractNumber} - {contract.city} ({contract.zipCode}), {contract.address} [{FormatUtils.getContractBadgeDetails(contract.status).text}]
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
      </Spin>
    </>
  )
};

export default ServiceRequestForm;
