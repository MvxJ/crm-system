import MainCard from 'components/MainCard';
import './Offer.css'
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import { Button, Col, DatePicker, Form, Input, Row, Select, Switch, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';
import DebounceSelect from 'utils/DebounceSelect';
import FormatUtils from 'utils/format-utils';
import moment from"moment";
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc'

const OfferForm = () => {
  
  const { id } = useParams();
  const navigate = useNavigate();
  const { TextArea } = Input;
  const [selectedModels, setSelectedModels] = useState([]);
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    downloadSpeed: 0,
    uploadSpeed: 0,
    newUsers: false,
    price: 0,
    discount: 0,
    type: 0,
    duration: 0,
    devices: [],
    numberOfCanals: 0,
    forStudents: false,
    discountType: null,
    validDue: null,
    deleted: false
  });

  async function searchModels(inputValue) {
    try {
      const response = await instance.get(`/models/list?searchTerm=${inputValue}`);
      
      if (response.data.results.models) {
        return response.data.results.models.map((model) => ({
          label: `#${model.id} ${model.manufacturer} - ${model.name} (${model.price})`,
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
        dayjs.extend(utc);

        const response = await instance.get(`/offer/${id}/detail`);

        setFormData({
          title: response.data.offer.title,
          description: response.data.offer.description,
          downloadSpeed: response.data.offer.downloadSpeed,
          uploadSpeed: response.data.offer.uploadSpeed,
          newUsers: response.data.offer.forNewUsers,
          price: response.data.offer.price,
          discount: response.data.offer.discount,
          type: response.data.offer.type,
          duration: response.data.offer.duration,
          devices: response.data.offer.devices,
          numberOfCanals: response.data.offer.numberOfCanals,
          forStudents: response.data.offer.forStudents,
          discountType: response.data.offer.discountType,
          validDue: response.data.offer.validDue ? dayjs.utc(response.data.offer.validDue.date).local() : null,
          deleted: response.data.offer.deleted,
        });

        if (response.data.offer.devices) {
          const deviceArray = [];

          response.data.offer.devices.forEach(device => {
            const deviceObj = {
              label: `#${device.id} ${device.manufacturer} - ${device.name} (${FormatUtils.getDeviceType(device.type)})`,
              value: device.id,
            }

            deviceArray.push(deviceObj);
          });

          setSelectedModels(deviceArray);
        }
      }
    } catch (error) {
      notification.error({
        message: "Can't fetch device data.",
        description: error.message,
        type: "error",
        placement: "bottomRight",
      });
    }
  };

  const createRequestObj = () => {
    const offer = {
      title: formData.title,
      description: formData.description,
      newUsers: formData.newUsers,
      price: parseFloat(formData.price),
      type: parseInt(formData.type),
      duration: parseInt(formData.duration),
      forStudents: formData.forStudents,
    };

    if (formData.discountType != null) {
      offer.discountType = parseInt(formData.discountType);
      offer.discount = parseFloat(formData.discount);
    }

    if (formData.type == 2 || formData.type == 1) {
      offer.numberOfCanals = parseInt(formData.numberOfCanals);
    }

    if (formData.type == 0 || formData.type == 2) {
      offer.downloadSpeed = parseFloat(formData.downloadSpeed);
      offer.uploadSpeed = parseFloat(formData.uploadSpeed);
    }

    if (formData.validDue != null) {
      offer.validDue = formData.validDue;
    }

    if (selectedModels.length > 0) {
      const devicesIds = [];

      selectedModels.forEach(element => {
        devicesIds.push(element.value)
      });

      offer.devices = devicesIds;
    }

    return offer;
  }

  const saveOffer = async () => {
    try {
      const payload = createRequestObj();
      const response = await instance.patch(`/offer/${id}/edit`, payload);

      if (response.status != 200) {
        notification.error({
          message: "Can 't update offer.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      notification.success({
        message: "Successfully updated offer.",
        type: 'success',
        placement: 'bottomRight'
      });

      navigate(`/office/offers/detail/${id}`);
    } catch (error) {
      notification.error({
        message: "Can 't update offer.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
    }
  }

  const addOffer = async () => {
    try {
      const payload = createRequestObj();
      console.log(payload);
      const response = await instance.post(`/offer/add`, payload);

      if (response.status != 200) {
        notification.error({
          message: "Can 't add offer.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      notification.success({
        message: "Successfully created offer.",
        type: 'success',
        placement: 'bottomRight'
      });

      navigate(`/office/offers/detail/${response.data.offer.id}`);
    } catch (error) {
      notification.error({
        message: "Can 't add offer.",
        description: error.message,
        type: 'error',
        placement: 'bottomRight'
      });
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
      <MainCard title={id ? `Edit Offer #${id}` : 'Add Offer'}>
        <Row>
          <Col span={22} offset={1} style={{ textAlign: "right" }}>
            {id ? (
              <Button type="primary" onClick={saveOffer}>
                Save Offer
              </Button>
            ) : (
              <Button type="primary" onClick={addOffer}>
                Add Offer
              </Button>
            )}
          </Col>
        </Row>
        <Form layout="vertical" style={{ width: 100 + "%" }}>
          <Row>
            <Col span={22} offset={1}>
              <h4>Offer:</h4>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Title"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please input offer title!",
                  },
                ]}
              >
                <Input
                  value={formData.title}
                  name="title"
                  onChange={handleInputChange}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item label="Valid Due">
                <DatePicker
                  style={{ width: '100%' }}
                  name="validDue"
                  value={formData.validDue}                  
                  onChange={(value) => handleInputChange({ target: { name: "validDue", value } })}
                />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Price"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please insert price!",
                  },
                ]}
              >
                <Input type="number" name="price" value={formData.price} onChange={handleInputChange} />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Discount Type"
              >
                <Select
                  defaultValue={formData.discountType}
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
          </Row>
          {formData.discountType != null ?
            <Row>
              <Col span={10} offset={1}>
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
                  <Input type="number" name="discount" value={formData.discount} onChange={handleInputChange}/>
                </Form.Item>
              </Col>
            </Row>
            : null}
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
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Type"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please select the offer type!",
                  },
                ]}
              >
                <Select
                  defaultValue={formData.type}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "type", value } })
                  }
                  options={[
                    { value: 0, label: "INTERNET" },
                    { value: 1, label: "TELEVISION" },
                    { value: 2, label: "INTERNET & TELEVISION" },
                  ]}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Duration"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please select the duration!",
                  },
                ]}
              >
                <Select
                  defaultValue={formData.duration}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "duration", value } })
                  }
                  options={[
                    { value: 0, label: "12 MONTHS" },
                    { value: 1, label: "24 MONTHS" },
                  ]}
                />
              </Form.Item>
            </Col>
          </Row>
          {formData.type == 0 || formData.type == 2 ?
            <Row>
              <Col span={10} offset={1}>
                <Form.Item
                  label="Download Speed"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: "Please insert the download speed!",
                    },
                  ]}
                >
                  <Input
                    type="number"
                    name="downloadSpeed" 
                    value={formData.downloadSpeed}
                    onChange={handleInputChange}
                  />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item
                  label="Upload Speed"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: "Please insert the upload speed!",
                    },
                  ]}
                >
                  <Input
                    type="number"
                    name="uploadSpeed" 
                    value={formData.uploadSpeed}
                    onChange={handleInputChange}
                  />
                </Form.Item>
              </Col>
            </Row>
            : null}
          {formData.type == 1 || formData.type == 2 ?
            <Row>
              <Col span={10} offset={1}>
                <Form.Item
                  label="Number of canals"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: "Please insert the number of canals!",
                    },
                  ]}
                >
                  <Input
                    type="number"
                    name="numberOfCanals"
                    value={formData.numberOfCanals}
                    onChange={handleInputChange}
                  />
                </Form.Item>
              </Col>
            </Row>
            : null}
            <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Devices"
              >
                <DebounceSelect
                    mode="multiple"
                    value={selectedModels}
                    placeholder="Search model..."
                    fetchOptions={searchModels}
                    onChange={(value) => {
                      setSelectedModels(value);
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
              <Form.Item
                label="For new users"
              >
                <Switch
                  name="newUsers"
                  checked={formData.newUsers}
                  onChange={(value) => handleInputChange({ target: { name: "newUsers", value } })}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="For Students"
              >
                <Switch
                  name="forStudents"
                  checked={formData.forStudents}
                  onChange={(value) => handleInputChange({ target: { name: "forStudents", value } })}
                />
              </Form.Item>
            </Col>
          </Row>
        </Form>
      </MainCard>
    </>
  )
};

export default OfferForm;
