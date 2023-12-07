import MainCard from 'components/MainCard';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { Button, Col, Form, Input, Row, Select, Spin, notification } from '../../../../node_modules/antd/es/index';
import { useEffect, useState } from 'react';
import DebounceSelect from 'utils/DebounceSelect';
import instance from 'utils/api';

const PaymentForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [loading, setLoading] = useState(false);
  const [invoices, setInvoices] = useState([]);
  const [isIdExist, setIsIdExist] = useState(false);
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
        setLoading(true);
        const response = await instance.get(`/payments/${id}/detail`);

        if (response.status != 200) {
          setLoading(false);
          notification.error({
            type: "error",
            message: "Can't fetch payment detail",
            placement: "bottomRight"
          });

          return;
        }

        setFormData({
          customerId: response.data.payment.customer.id,
          billId: response.data.payment.bill.id,
          paidBy: response.data.payment.paidBy,
          amount: response.data.payment.amount,
          note: response.data.payment.note,
          status: response.data.payment.status
        });

        setIsIdExist(true);
        setLoading(false);
      }
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't fetch payment data.",
        description: error.message,
        type: "error",
        placement: "bottomRight",
      });
    } finally {
      setLoading(false);
    }
  };

  const createRequestObj = () => {
    const obj = {
      note: formData.note,
      status: parseInt(formData.status)
    };

    if (!id) {
      obj.customer = selectedCustomer.value;
      obj.bill = formData.billId;
      obj.paidBy = parseInt(formData.paidBy);
      obj.amount = parseFloat(formData.amount);
    }

    return obj;
  }

  const savePayment = async () => {
    try {
      setLoading(true);
      const request = createRequestObj();
      const response = await instance.patch(`/payments/${id}/edit`, request);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Can't update payment.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      notification.success({
        message: "Successfully updated payment.",
        type: 'success',
        placement: 'bottomRight'
      });

      setLoading(false);
      navigate(`/office/payments/detail/${id}`);
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't update payment.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

  const addPayment = async () => {
    try {
      setLoading(true);
      const request = createRequestObj();
      const response = await instance.post(`/payments/add`, request);

      if (response.status != 200) {
        setLoading(false);
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

      setLoading(false);
      navigate(`/office/payments/detail/${response.data.payment.id}`);
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't add payment.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
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
      <Spin spinning={loading}>
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
          {!id ?
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
              {selectedCustomer ?
                <Col span={10} offset={2}>
                  <Form.Item
                    label="Invoice"
                  >
                    <Select
                      onChange={(value) =>
                        handleInputChange({ target: { name: "billId", value } })
                      }
                      value={formData.billId}
                    >
                      {invoices.map((invoice) => (
                        <Select.Option key={invoice.id} value={invoice.id}>
                          Invoice #{invoice.number}
                        </Select.Option>
                      ))}
                    </Select>
                  </Form.Item>
                </Col> : null}
            </Row> : null}
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
                  disabled={isIdExist}
                  value={formData.paidBy}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "paidBy", value } })
                  }
                  options={[
                    { value: 0, label: "Card" },
                    { value: 1, label: "BLIK" },
                    { value: 2, label: "Online Payments" },
                    { value: 3, label: "Cash" },
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
                <Input type="number" name="amount" value={formData.amount} onChange={handleInputChange} disabled={isIdExist} />
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
      </Spin>
    </>
  )
};

export default PaymentForm