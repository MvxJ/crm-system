import MainCard from "components/MainCard";
import "./Invoice.css";
import {
  useNavigate,
  useParams,
} from "../../../../node_modules/react-router-dom/dist/index";
import { useEffect, useState } from "react";
import {
  Button,
  Col,
  DatePicker,
  Form,
  Row,
  Select,
  Spin,
  notification,
} from "../../../../node_modules/antd/es/index";
import instance from "utils/api";
import DebounceSelect from "utils/DebounceSelect";
import FormatUtils from "utils/format-utils";
import dayjs from "dayjs";
import utc from "dayjs/plugin/utc";
import PositionForm from "components/PositionForm";

const InvoiceForm = () => {
  const { id } = useParams();
  const [formData, setFormData] = useState({
    contract: null,
    customer: null,
    status: 0,
    payDue: null,
  });
  const [contracts, setContracts] = useState([]);
  const [positions, setPositions] = useState([]);
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [loading, setLoading] = useState(false);
  const [addPosition, setAddPosition] = useState(false);
  const [selectedContract, setSelectedContract] = useState(null);
  const navigate = useNavigate();

  const fetchData = async () => {
    dayjs.extend(utc);
    if (id) {
      try {
        setLoading(true);

        const response = await instance.get(`/bill/${id}/detail`);

        if (response.status != 200) {
          setLoading(false);

          notification.error({
            message: "Can't fetch invoice detail.",
            type: "error",
            placement: "bottomRight",
          });

          return;
        }

        setFormData({
          payDue: dayjs.utc(response.data.bill.payDue.date).local(),
          status: response.data.bill.status,
        });

        setSelectedCustomer({
          value: response.data.bill.customer.id,
          label: `${response.data.bill.customer.name} ${response.data.bill.customer.surname} (${response.data.bill.customer.email})`,
        });

        setContracts([response.data.bill.contract]);

        setSelectedContract(response.data.bill.contract.id);

        setPositions(response.data.bill.positions);

        setLoading(false);
      } catch (e) {
        setLoading(false);
        notification.error({
          message: "Can't fetch invoice detail.",
          description: e.message,
          type: "error",
          placement: "bottomRight",
        });
      } finally {
        setLoading(false);
      }
    }
  };

  async function searchCustomers(inputValue) {
    try {
      const response = await instance.get(
        `/customers/list?searchTerm=${inputValue}`
      );

      if (response.data.results.customers) {
        return response.data.results.customers.map((customer) => ({
          label: `${customer.firstName} ${customer.lastName} (${
            customer.socialSecurityNumber
              ? customer.socialSecurityNumber
              : "empty"
          })`,
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
      } else {
        setContracts([]);
        return;
      }

      setContracts([]);
    } catch (error) {
      setContracts([]);
    }
  }

  const saveInvoice = async () => {
    try {
      setLoading(true);

      const response = await instance.patch(`/bill/${id}/edit`, {
        status: formData.status,
        payDue: formData.payDue
      });

      if (response.status != 200) {
        setLoading(false);

        notification.error({
          message: "An error occured during updating invoice.",
          type: "error",
          placement: "bottomRight"
        });

        return;
      }

      setLoading(false);

      notification.success({
        message: "Successfully updated invoice.",
        type: "success",
        placement: "bottomRight"
      });

      navigate(`/office/invoices/detail/${id}`);
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "An error occured during updating invoice.",
        type: "error",
        placement: "bottomRight"
      });
    } finally {
      setLoading(false);
    }
  };

  const addInvoice = async () => {
    try {
      setLoading(true);

      const request = {
        customer: selectedCustomer.value,
        payDue: formData.payDue,
        status: formData.status,
      };

      console.log(formData);

      if (selectedContract) {
        request.contract = selectedContract;
      }

      const response = await instance.post(`/bill/add`, request);

      if (response.status != 200) {
        setLoading(false);

        notification.error({
          message: "Can't add invoice.",
          placement: "bottomRight",
          type: "error",
        });

        return;
      }

      setLoading(false);
      navigate(`/office/invoices/detail/${response.data.bill.id}`);
    } catch (e) {
      setLoading(false);

      notification.error({
        message: "Can't add invoice.",
        description: e.message,
        type: "error",
        placement: "bottomRight",
      });
    } finally {
      setLoading(false);
    }
  };

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
      <Spin spinning={loading}>
        <MainCard title={id ? `Edit invoice NO #${id}` : "Add Invoice"}>
          <Row style={{ textAlign: "right" }}>
            <Col span={22} offset={1}>
              {id ? (
                <Button type="primary" onClick={saveInvoice}>Save Invoice</Button>
              ) : (
                <Button type="primary" onClick={addInvoice}>
                  Add Invoice
                </Button>
              )}
            </Col>
          </Row>
          <Form layout="vertical" style={{ width: 100 + "%" }}>
            <Row>
              <Col span={10} offset={1}>
                <Form.Item label="Customer">
                  <DebounceSelect
                    value={selectedCustomer}
                    disabled={id ? true : false}
                    mode="single"
                    placeholder="Search customer..."
                    fetchOptions={searchCustomers}
                    onChange={(value) => {
                      setSelectedCustomer(value);
                    }}
                    style={{
                      width: "100%",
                    }}
                  />
                </Form.Item>
              </Col>
              {selectedCustomer ? (
                <Col span={10} offset={2}>
                  <Form.Item label="Contract">
                    <Select
                      defaultValue={selectedContract}
                      disabled={id ? true : false}
                      mode="single"
                      placeholder="Choose contract..."
                      onChange={(value) => {
                        setSelectedContract(value);
                      }}
                      style={{
                        width: "100%",
                      }}
                    >
                      {contracts.map((contract) => (
                        <Select.Option key={contract.id} value={contract.id}>
                          #{contract.contractNumber} - {contract?.city} (
                          {contract.zipCode}), {contract?.address} [
                          {
                            FormatUtils.getContractBadgeDetails(
                              contract?.status
                            ).text
                          }
                          ]
                        </Select.Option>
                      ))}
                    </Select>
                  </Form.Item>
                </Col>
              ) : null}
            </Row>
            <Row>
              <Col span={10} offset={1}>
                <Form.Item label="Pay Due">
                  <DatePicker
                    value={formData.payDue}
                    style={{ width: "100%" }}
                    onChange={(value) =>
                      handleInputChange({ target: { name: "payDue", value } })
                    }
                  />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item label="Status">
                  <Select
                    defaultValue={0}
                    value={formData.status}
                    onChange={(value) =>
                      handleInputChange({ target: { name: "status", value } })
                    }
                    options={[
                      { value: 0, label: "AWAITING PAYMENT" },
                      { value: 1, label: "PAID" },
                      { value: 2, label: "PAID PARTIALLY" },
                      { value: 3, label: "PAIMENT DELAYED" },
                      { value: 4, label: "NOT PAID" },
                      { value: 5, label: "REFUNDED" },
                    ]}
                  />
                </Form.Item>
              </Col>
            </Row>
          </Form>
          {id ? (
            <>
              <Row>
                <Col span={10} offset={1}>
                  <h4>Positions: </h4>
                </Col>
                <Col span={10} offset={2} style={{textAlign: 'right', marginTop: '15px'}}>
                  { !addPosition ?
                  <Button type="primary" onClick={() => {setAddPosition(true)}}>
                    Add position
                  </Button> : <Button danger onClick={() => {setAddPosition(false)}}>Cancell</Button> }
                </Col>
              </Row>
              { addPosition ?
              <Row>
                <Col span={22} offset={1}>
                  <PositionForm position={null} billId={id}/>
                </Col>
              </Row> : null }
              {positions.map((position) => (
                <Row>
                  <Col span={22} offset={1}>
                    <PositionForm position={position}/>
                  </Col>
                </Row>
              ))}
              </>
          ) : null}
        </MainCard>
      </Spin>
    </>
  );
};

export default InvoiceForm;
