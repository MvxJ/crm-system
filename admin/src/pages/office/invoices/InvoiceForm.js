import MainCard from 'components/MainCard';
import './Invoice.css';
import { useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import { Button, Col, DatePicker, Form, Row, Select, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';
import DebounceSelect from 'utils/DebounceSelect';

const InvoiceForm = () => {
  const { id } = useParams();
  const [formData, setFormData] = useState({});
  const [positions, setPositions] = useState([]);
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [loading, setLoading] = useState(false);
  const [selectedContract, setSelectedContract] = useState(null);

  const fetchData = async () => {
    if (id) {
      try {
        setLoading(true);

        const response = await instance.get(`/bill/${id}/detail`);

        if (response.status != 200) {
          setLoading(false);

          notification.error({
            message: "Can't fetch invoice detail.",
            type: "error",
            placement: "bottomRight"
          });

          return;
        }

        setFormData({

        });

        setLoading(false);
      } catch (e) {
        setLoading(false);
        notification.error({
          message: "Can't fetch invoice detail.",
          description: e.message,
          type: "error",
          placement: "bottomRight"
        });
      } finally {
        setLoading(false);
      }
    }
  }

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

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

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

  useEffect(() => {
    if (selectedCustomer) {
      fetchCustomerContracts(selectedCustomer.value);
    }
  }, [selectedCustomer]);

  useEffect(() => {
    fetchData();
  }, [id]);

  return (
    <>
      <MainCard title={id ? `Edit invoice NO #${id}` : "Add Invoice"}>
        <Row style={{ textAlign: 'right' }}>
          <Col span={22} offset={1}>
            {id ? <Button type="primary">Save Invoice</Button> : <Button type="primary">Add Invoice</Button>}
          </Col>
        </Row>
        <Form layout="vertical" style={{ width: 100 + "%" }}>
          { !id ?
          <Row>
            <Col span={10} offset={1}>
              <Form.Item label="Customer">
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
            </Col>
            {selectedCustomer ?
              <Col span={10} offset={2}>
                <Form.Item label="Contract">
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
              </Col>
              : null}
          </Row> : null }
          <Row>
            <Col span={10} offset={1}>
              <Form.Item label="Pay Due">
                <DatePicker
                  style={{ width: '100%' }}
                  onChange={(value) => handleInputChange({ target: { name: "", value } })}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item label="Status">
                <Select
                  value={formData.status}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "status", value } })
                  }
                  options={[
                    { value: 0, label: "AVAILABLE" },
                    { value: 1, label: "RESERVED" },
                    { value: 2, label: "DESTROYED" },
                    { value: 3, label: "SOLD" },
                    { value: 4, label: "RENTED" },
                    { value: 5, label: "DEFEVTICE" }
                  ]}
                />
              </Form.Item>
            </Col>
          </Row>
        </Form>
        {id ?
          <Form layout="vertical" style={{ width: 100 + "%" }}>
            
          </Form>
          : null}
      </MainCard>
    </>
  )
};

export default InvoiceForm;
