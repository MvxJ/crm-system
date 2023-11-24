import MainCard from 'components/MainCard';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { Button, Col, Form, Input, Row, Select, notification } from '../../../../node_modules/antd/es/index';
import { useEffect, useState } from 'react';
import DebounceSelect from 'utils/DebounceSelect';
import instance from 'utils/api';

const PaymentForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [invoices, setInvoices] = useState([]);
  const [formData, setFormData] = useState({
    customerId: null,
    billId: null,
    paidBy: 0,
    amount: 0,
    note: '',
    status: 0
  });
  const { TextArea } = Input;

  const fetchData = async () => {
    try {
      if (id) {
        const response = await instance.get(``);

        setFormData({
        });
      }
    } catch (error) {
      notification.error({
        message: "Can't fetch payment data.",
        description: error.message,
        type: "error",
        placement: "bottomRight",
      });
    }
  };

  const savePayment = async () => {
    try {
      const response = await instance.patch(``, {});

      if (response.status != 200) {
        notification.error({
          message: "Can't update payment.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      notification.success({
        message: "Successfully updated contract.",
        type: 'success',
        placement: 'bottomRight'
      });

      navigate(`/office/payments/detail/${id}`);
    } catch (error) {
      notification.error({
        message: "Can't update payment.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    }
  }

  const addPayment = async () => {
    try {
      const response = await instance.post(``, {});

      if (response.status != 200) {
        notification.error({
          message: "Can 't add payment.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      notification.success({
        message: "Successfully created payment.",
        type: 'success',
        placement: 'bottomRight'
      });

      navigate(`/office/payments/detail/${response.data.payment.id}`);
    } catch (error) {
      notification.error({
        message: "Can't add payment.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
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

  async function fetchCustomerInvoices(customerId) {
    try {
      const response = await instance.get(`/bill/list?customerId=${customerId}`);

      if (response.status != 200) {
        setInvoices([]);
        return;
      }

      setInvoices(response.data.results.bills);
    } catch (e) {
      setInvoices([]);
    }
  }

  useEffect(() => {
    if (selectedCustomer) {
      fetchCustomerInvoices(selectedCustomer.value);
    }
  }, [selectedCustomer]);

  useEffect(() => {
    fetchData();
  }, [id]);

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  return (
    <>
      <MainCard title="Payment Form">
        <Row style={{ textAlign: 'right' }}>
          <Col span={22} offset={1}>
            {id ?
              <Button type="primary" onClick={savePayment}>
                Save Payment
              </Button> :
              <Button type="primary" onClick={addPayment}>
                Add Payment
              </Button>}
          </Col>
        </Row>
        <Form layout="vertical" style={{ width: 100 + "%" }}>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Customer"
                required
                tooltip="This is a required field"
              >
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
            { !id && selectedCustomer ?
            <Col span={10} offset={2}>
              <Form.Item
                label="Invoice"
              >
                <Select>
                    {invoices.map((invoice) => (
                        <Select.Option key={invoice.id} value={invoice.id}>
                          Invoice #{invoice.id}
                        </Select.Option>
                      ))}
                  </Select>
              </Form.Item>
            </Col> : null }
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Paid By"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please select the paid method!",
                  },
                ]}
              >
                <Select
                  value={formData.paidBy}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "paidBy", value } })
                  }
                  options={[
                    { value: 0, label: "Card" },
                    { value: 1, label: "BLIK" },
                    { value: 2, label: "Online Payments" },
                    { value: 2, label: "Cash" },
                  ]}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Amount"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please insert amount!",
                  },
                ]}
              >
                <Input type="number" name="amount" value={formData.amount} onChange={handleInputChange} />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Payment Status"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please select the status!",
                  },
                ]}
              >
                <Select
                  value={formData.status}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "status", value } })
                  }
                  options={[
                    { value: 0, label: "Pending" },
                    { value: 1, label: "Posted" }
                  ]}
                />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <Form.Item
                label="Note"
              >
                <TextArea
                  showCount
                  maxLength={1500}
                  style={{ height: 120, resize: "none" }}
                  placeholder="Note..."
                  value={formData.note}
                  onChange={handleInputChange}
                  name="note"
                />
              </Form.Item>
            </Col>
          </Row>
        </Form>
      </MainCard>
    </>
  )
};

export default PaymentForm