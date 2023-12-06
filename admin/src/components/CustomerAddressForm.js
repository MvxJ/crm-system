import { useState } from "react";
import {
  Button,
  Col,
  Form,
  Input,
  Row,
  Select,
  Spin,
  notification,
} from "../../node_modules/antd/es/index";
import "./SharedStyles.css";
import instance from "utils/api";

const CustomerAddressForm = ({
  customerId,
  address,
  onSuccessSaveFunction,
  type,
  email,
  phoneNumber,
}) => {
  const [loading, setLoading] = useState(false);
  const [disabled, setDisabled] = useState(false);
  const [formData, setFormData] = useState({
    type: type,
    city: "",
    zipCode: "",
    address: "",
    phoneNumber: phoneNumber,
    emailAddress: email,
    companyName: "",
    taxId: "",
    customer: "",
    country: "",
  });

  const createRequetsObj = () => {
    const obj = {
      type: formData.type,
      city: formData.city,
      zipCode: formData.zipCode,
      address: formData.address,
      phoneNumber: formData.phoneNumber,
      emailAddress: formData.emailAddress,
      customer: customerId,
      country: formData.country,
    };

    if (formData.type == 0) {
      obj.taxId = formData.taxId;
      obj.companyName = formData.companyName;
    }

    return obj;
  };

  const saveAddress = () => {};

  const addAddress = async () => {
    try {
      setLoading(true);
      const request = createRequetsObj();

      const response = await instance.post(`/customers/address/add`, request);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Can't add customer address.",
          description: e.message,
          type: "error",
          placement: "bottomRight",
        });

        return;
      }

      setLoading(false);
      notification.success({
        message: `Successfully addedd ${
          formData.type == 0 ? "Billing" : "Contact"
        } address.`,
        type: "success",
        placement: "bottomRight",
      });

      if (onSuccessSaveFunction) {
        setDisabled(true);
        onSuccessSaveFunction();
      }
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Can't add customer address.",
        description: e.message,
        type: "error",
        placement: "bottomRight",
      });
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  return (
    <>
      <Spin spinning={loading}>
        <Form
          layout="vertical"
          style={{ width: 100 + "%" }}
          disabled={disabled}
        >
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="City"
                required
                tooltip="This is a required field"
              >
                <Input
                  name="city"
                  value={formData.city}
                  onChange={handleInputChange}
                  placeholder="City"
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Zip Code"
                required
                tooltip="This is a required field"
              >
                <Input
                  name="zipCode"
                  value={formData.zipCode}
                  onChange={handleInputChange}
                  placeholder="Zip-Code"
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
              >
                <Input
                  name="address"
                  value={formData.address}
                  onChange={handleInputChange}
                  placeholder="Street 12"
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Country"
                required
                tooltip="This is a required field"
              >
                <Input
                  name="country"
                  value={formData.country}
                  onChange={handleInputChange}
                  placeholder="Poland"
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
              >
                <Select
                  placeholder="Select address type..."
                  style={{ textAlign: "left" }}
                  name="type"
                  value={formData.type}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "type", value } })
                  }
                  options={[
                    { value: 0, label: "Billing" },
                    { value: 1, label: "Contact" },
                  ]}
                />
              </Form.Item>
            </Col>
          </Row>
          {formData.type == 0 ? (
            <Row>
              <Col span={10} offset={1}>
                <Form.Item
                  label="Company Name"
                  required
                  tooltip="This is a required field"
                >
                  <Input
                    name="companyName"
                    value={formData.companyName}
                    onChange={handleInputChange}
                    placeholder="Company name"
                  />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item
                  label="Tax ID"
                  required
                  tooltip="This is a required field"
                >
                  <Input
                    name="taxId"
                    value={formData.taxId}
                    onChange={handleInputChange}
                    placeholder="Tax ID"
                  />
                </Form.Item>
              </Col>
            </Row>
          ) : null}
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                rules={[
                  {
                    type: "email",
                    message: "Please enter a valid email address",
                  },
                ]}
                label="Email Address"
              >
                <Input
                  name="emailAddress"
                  value={formData.emailAddress}
                  onChange={handleInputChange}
                  placeholder="Email address.."
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item label="Phone Number">
                <Input
                  name="phoneNumber"
                  value={formData.phoneNumber}
                  onChange={handleInputChange}
                  placeholder="Phone Number"
                />
              </Form.Item>
            </Col>
          </Row>
          {disabled == false ? (
            <Row style={{ textAlign: "right" }}>
              <Col span={22} offset={1}>
                <Button
                  type="primary"
                  onClick={addAddress}
                  style={{ marginBototm: "15px" }}
                >
                  Add Address
                </Button>
              </Col>
            </Row>
          ) : null}
        </Form>
      </Spin>
    </>
  );
};

export default CustomerAddressForm;
