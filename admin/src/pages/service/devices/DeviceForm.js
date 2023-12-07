import MainCard from 'components/MainCard';
import { Button, Col, DatePicker, Form, Input, Row, Select, Spin, notification } from '../../../../node_modules/antd/es/index';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import DebounceSelect from 'utils/DebounceSelect';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'

const DeviceForm = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [selectedModelId, setSelectedModelId] = useState(null);
  const [selectedUserId, setSelectedUserId] = useState(null);
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    serialNumber: '',
    macAddress: '',
    boughtDate: '',
    status: 0,
    soldDate: '',
    model: null,
    user: null
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

  async function searchModels(inputValue) {
    try {
      const response = await instance.get(`/models/list?searchTerm=${inputValue}`);

      if (response.data.results.models) {
        return response.data.results.models.map((model) => ({
          label: `${model.manufacturer} - ${model.name} (${model.price})`,
          value: model.id,
        }));
      }

      return [];
    } catch (error) {
      return [];
    }
  }

  const fetchData = async () => {
    try {
      if (id) {
        setLoading(true);
        const response = await instance.get(`/devices/${id}`);
        dayjs.extend(utc);

        setFormData({
          serialNumber: response.data.device.serialNo,
          macAddress: response.data.device.macAddress,
          boughtDate: response.data.device.boughtDate?.date,
          model: response.data.device.model,
          status: response.data.device.status,
          user: response.data.device.user,
          soldDate: response.data.device.soldDate ? dayjs.utc(response.data.device.soldDate.date).local() : null,
        });

        if (response.data.device.model) {
          setSelectedModelId({
            label: `${response.data.device.model.manufacturer} - ${response.data.device.model.name} (${response.data.device.model.price})`,
            value: response.data.device.model.id,
          });
        }

        if (response.data.device.user) {
          setSelectedUserId({
            label: `#${response.data.device.user.id} ${response.data.device.user.firstName} ${response.data.device.user.lastName} (${response.data.device.user.socialSecurityNumber ? response.data.device.user.socialSecurityNumber : 'empty'})`,
            value: response.data.device.user.id,
          });
          setLoading(false);
        }
      }
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't fetch device data.",
        description: error.message,
        type: "error",
        placement: "bottomRight",
      });
    } finally {
      setLoading(false);
    }
  };

  const saveDevice = async () => {
    try {
      setLoading(true);
      const device = {
        serialNumber: formData.serialNumber,
        macAddress: formData.macAddress,
        model: selectedModelId.value,
        status: parseInt(formData.status),
      }

      if (selectedUserId != null) {
        device.user = selectedUserId.value;
      }

      if (formData.soldDate != null) {
        device.soldDate = formData.soldDate;
      }

      const response = await instance.patch(`/devices/${id}/edit`, device)

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Can't save device.",
          type: "error",
          placement: "bottomRight"
        })
        return;
      }

      setLoading(false);
      notification.success({
        type: "success",
        placement: "bottomRight",
        message: "Successfully updated device."
      });
      navigate(`/service/devices/details/${id}`);
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't save device.",
        description: error.message,
        type: "error",
        placement: "bottomRight"
      })
    } finally {
      setLoading(false);
    }
  }

  const createDevice = async () => {
    try {
      setLoading(true);
      const response = await instance.post(`/devices/add`, {
        serialNumber: formData.serialNumber,
        macAddress: formData.macAddress,
        boughtDate: formData.boughtDate,
        model: selectedModelId.value,
        status: parseInt(formData.status)
      });

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Can't add device.",
          type: "error",
          placement: "bottomRight"
        });

        return;
      }

      navigate(`/service/devices/details/${response.data.device.id}`);
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't add device.",
        description: error.message,
        type: "error",
        placement: "bottomRight"
      })
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
      <MainCard title={id ? `Edit Device #${id}` : 'Add Device'}>
        <Row>
          <Col span={22} offset={1} style={{ textAlign: "right" }}>
            {id ? (
              <Button type="primary" onClick={saveDevice}>
                Save Device
              </Button>
            ) : (
              <Button type="primary" onClick={createDevice}>
                Add Device
              </Button>
            )}
          </Col>
        </Row>
        <Form layout="vertical" style={{ width: 100 + "%" }}>
          <Row>
            <Col span={22} offset={1}>
              <h4>Device:</h4>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Serial Number"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the serial number!",
                  },
                ]}
              >
                <Input
                  name="serialNumber"
                  value={formData.serialNumber}
                  onChange={handleInputChange}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Mac Address"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the mac address!",
                  },
                ]}
              >
                <Input
                  name="macAddress"
                  value={formData.macAddress}
                  onChange={handleInputChange}
                />
              </Form.Item>
            </Col>
          </Row>
          {id ? null :
            <Row>
              <Col span={10} offset={1} >
                <Form.Item
                  label="Bought Date"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: "Please enter the mac Bought date!",
                    },
                  ]}>
                  <DatePicker
                    style={{ width: '100%' }}
                    name="boughtDate"
                    value={formData.boughtDate}
                    onChange={(value) => handleInputChange({ target: { name: "boughtDate", value } })}
                  />
                </Form.Item>
              </Col>
            </Row>
          }
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Model"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please select model!",
                  },
                ]}
              >
                <DebounceSelect
                  mode="single"
                  value={selectedModelId}
                  placeholder="Search model..."
                  fetchOptions={searchModels}
                  onChange={(value) => {
                    setSelectedModelId(value);
                  }}
                  style={{
                    width: '100%',
                  }}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Status"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please select the model!",
                  },
                ]}
              >
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
          {id ?
            <Row>
              <Col span={10} offset={1}>
                <Form.Item label="User">
                  <DebounceSelect
                    value={selectedUserId}
                    mode="single"
                    placeholder="Search customer..."
                    fetchOptions={searchCustomers}
                    onChange={(value) => {
                      setSelectedUserId(value);
                    }}
                    style={{
                      width: '100%',
                    }}
                  />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item label="Sold Date">
                  <DatePicker
                    style={{ width: '100%' }}
                    name="soldDate"
                    value={formData.soldDate}
                    onChange={(value) => handleInputChange({ target: { name: "soldDate", value: value} })}
                  />
                </Form.Item>
              </Col>
            </Row> : null}
        </Form>
      </MainCard>
      </Spin>
    </>
  )
};

export default DeviceForm;
