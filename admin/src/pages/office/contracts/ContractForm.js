import MainCard from 'components/MainCard';
import './Contract.css'
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import { Button, Col, DatePicker, Form, Input, Row, Select, Spin, notification } from '../../../../node_modules/antd/es/index';
import DebounceSelect from 'utils/DebounceSelect';
import instance from 'utils/api';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'

const ContractForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [selectedOffer, setSelectedOffer] = useState(null);
  const [loading, setLoading] = useState(false);
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [formData, setFormData] = useState({
    userId: null,
    offerId: null,
    status: null,
    startDate: '',
    discount: 0,
    discountType: null,
    description: '',
    city: '',
    zipCode: '',
    address: ''
  });
  const { TextArea } = Input;

  const fetchData = async () => {
    try {
      if (id) {
        setLoading(true);
        dayjs.extend(utc);
        const response = await instance.get(`/contracts/${id}/details`);

        if (response.status != 200) {
          setLoading(false);
          notification.error({
            type: 'error',
            messsage: "Can't fetch contract detail.",
            placement: 'bottomRight'
          });

          return;
        }

        setFormData({
          userId: response.data.contract.id,
          offerId: response.data.contract.offer.id,
          status: response.data.contract.status,
          startDate: response.data.contract.startDate ? dayjs.utc(response.data.contract.startDate.date).local() : null,
          discount: response.data.contract.discount,
          discountType: response.data.contract.discountType,
          description: response.data.contract.description,
          city: response.data.contract.city,
          zipCode: response.data.contract.zipCode,
          address: response.data.contract.address
        });
      
        setLoading(false);
      }
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't fetch contract data.",
        description: error.message,
        type: "error",
        placement: "bottomRight",
      });
    } finally {
      setLoading(false);
    }
  };

  const saveContract = async () => {
    try {
      setLoading(true);
      const request = createRequestObj();
      const response = await instance.patch(`/contracts/${id}/edit`, request);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Can't update contract.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      setLoading(false);
      notification.success({
        message: "Successfully updated contract.",
        type: 'success',
        placement: 'bottomRight'
      });

      navigate(`/office/contracts/detail/${id}`);
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't update contract.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

  async function serchOffer(inputValue) {
    try {
      const response = await instance.get(`/offer/list?searchTerm=${inputValue}`);

      if (response.data.results.offers) {
        return response.data.results.offers.map((offer) => ({
          label: `${offer.title}`,
          value: offer.id,
        }));
      }

      return [];
    } catch (error) {
      return [];
    }
  }

  const createRequestObj = () => {
    var obj = {
      startDate: formData.startDate,
      description: formData.description,
      city: formData.city,
      zipCode: formData.zipCode,
      address: formData.address,
    };

    if (formData.status) {
      obj.status = parseInt(formData.status);
    }

    if (!id) {
      obj.user = selectedCustomer.value;
      obj.offer = selectedOffer.value;
    }

    if (formData.discountType != null) {
      obj.discount = parseFloat(formData.discount);
      obj.discountType = parseInt(formData.discountType);
    }

    return obj;
  }

  async function searchCustomers(inputValue) {
    try {
      const response = await instance.get(`/customers/list?searchTerm=${inputValue}`);

      if (response.data.results.customers) {
        return response.data.results.customers.map((customer) => ({
          label: `${customer.firstName} ${customer.lastName} (${customer.socialSecurityNumber ? customer.socialSecurityNumber : 'empty'})`,
          value: customer.id,
        }));
      }

      return [];
    } catch (error) {
      return [];
    }
  }

  const addContract = async () => {
    try {
      setLoading(true);
      const request = createRequestObj();
      const response = await instance.post(`/contracts/add`, request);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Can 't add contract.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      setLoading(false);
      notification.success({
        message: "Successfully created contract.",
        type: 'success',
        placement: 'bottomRight'
      });

      navigate(`/office/contracts/detail/${response.data.contract.id}`);
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't add contract.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

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
      <MainCard title={id ? `Edit Contract #${id}` : 'Add Contract'}>
        <Row style={{ textAlign: 'right' }}>
          <Col span={22} offset={1}>
            {id ?
              <Button type="primary" onClick={saveContract}>
                Save Contract
              </Button>
              :
              <Button type="primary" onClick={addContract}>
                Add Contract
              </Button>
            }
          </Col>
        </Row>
        <Form layout="vertical" style={{ width: 100 + "%" }}>
          <Row>
            <Col span={22} offset={1}>
              <h4>Contract Detail: </h4>
            </Col>
          </Row>
          { !id ? 
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Offer"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the mac Start date!",
                  },
                ]}>
                <DebounceSelect
                  mode="single"
                  placeholder="Search offer..."
                  fetchOptions={serchOffer}
                  onChange={(value) => {
                    setSelectedOffer(value);
                  }}
                  style={{
                    width: '100%',
                  }}
                />
              </Form.Item>
            </Col>
            
            <Col span={10} offset={2}>
              <Form.Item
                label="Customer"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the mac Start date!",
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
            </Col>
          </Row> : null }
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Start Date"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please select the contract start date!",
                  },
                ]}
              >
                <DatePicker
                  style={{ width: '100%' }}
                  name="startDate"
                  value={formData.startDate}
                  onChange={(value) => handleInputChange({ target: { name: "startDate", value } })}
                />
              </Form.Item>
            </Col>
            { id ?
            <Col span={10} offset={2}>
              <Form.Item
                label="Status"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please select the contract status!",
                  },
                ]}
              >
                <Select
                  value={formData.status}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "status", value } })
                  }
                  options={[
                    { value: 0, label: "AWAITING_ACTIVATION" },
                    { value: 1, label: "CLOSED" },
                    { value: 2, label: "ACTIVE" },
                    { value: 3, label: "INACTIVE" }
                  ]}
                />
              </Form.Item>
            </Col> : null }
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Discount Type"
              >
                <Select
                  value={formData.discountType}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "discountType", value } })
                  }
                  options={[
                    { value: null, label: "NONE" },
                    { value: 0, label: "ABSOLUTE" },
                    { value: 1, label: "PERCENTAGE" },
                  ]}
                />
              </Form.Item>
            </Col>
            {formData.discountType != null ?
              <Col span={10} offset={2}>
                <Form.Item
                  label="Discount Amount"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: "Please insert discount amount!",
                    },
                  ]}
                >
                  <Input type="number" name="discount" value={formData.discount} onChange={handleInputChange} />
                </Form.Item>
              </Col>
              : null}
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <h4>Service Detail: </h4>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="City"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the city!",
                  },
                ]}
              >
                <Input
                  name="city"
                  value={formData.city}
                  onChange={handleInputChange}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Zip-Code"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the mac zipCode!",
                  },
                ]}
              >
                <Input
                  name="zipCode"
                  value={formData.zipCode}
                  onChange={handleInputChange}
                />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Address"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the address!",
                  },
                ]}
              >
                <Input
                  name="address"
                  value={formData.address}
                  onChange={handleInputChange}
                />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <h4>Additional info: </h4>
            </Col>
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

export default ContractForm;
