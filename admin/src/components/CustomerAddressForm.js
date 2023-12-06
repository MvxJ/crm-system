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
import { IconButton } from "../../node_modules/@mui/material/index";
import { FormOutlined, DeleteOutlined, CloseCircleOutlined } from '@ant-design/icons';

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
  const [edit, setEdit] = useState(false);
  const [formData, setFormData] = useState({
    type: address ? address.type : type,
    city: address ? address.city : '',
    zipCode: address ? address.zipCode : '',
    address: address ? address.address : '',
    phoneNumber: address ? address.phoneNumber : phoneNumber,
    emailAddress: address ? address.emailAddress : email,
    companyName: address ? address.companyName : '',
    taxId: address ? address.taxId : '',
    customer: address ? address.customerId : '',
    country: address ? address.country : '',
  });

  const createRequetsObj = () => {
    const obj = {
      type: parseInt(formData.type),
      city: formData.city,
      zipCode: formData.zipCode,
      address: formData.address,
      phoneNumber: formData.phoneNumber,
      emailAddress: formData.emailAddress,
      country: formData.country,
    };

    if (!address) {
      obj.customer = parseInt(customerId);
    }

    if (formData.type == 0) {
      obj.taxId = formData.taxId;
      obj.companyName = formData.companyName;
    }

    return obj;
  };

  const saveAddress = async () => {
    try {
      setLoading(true);

      const request = createRequetsObj();
      const response = await instance.patch(`/customers/address/${address.id}/edit`, request);
      
      if (response.status != 200) {
        setLoading(false);

        notification.error({
          message: "Can't update customer address.",
          placement: "bottomRight",
          type: "error"
        })

        return;
      }

      setLoading(false);
      notification.success({
        message: "Successfully updated customer adderss.",
        type: "success",
        placement: "bottomRight"
      });
      setEdit(false);
    } catch (e) {
      setLoading(false);
      notification.error({
        placement: "bottomRight",
        message: "Can't save customer address.",
        type: "error",
        description: e.message
      });
    } finally {
      setLoading(false);
    }
  };

  const deleteAddress = async () => {
    try {
      setLoading(true);

      const response = await instance.delete(`/customers/address/${address.id}/delete`);

      if (response.status != 200) {
        setLoading(false);

        notification.error({
          type: "error",
          placement: "bottomRight",
          message: "Can't delete customer address."
        });

        return;
      }

      setLoading(false);
      notification.success({
        type: "success",
        message: "Successfully deleted customer address.",
        placement: "bottomRight"
      });

      if (onSuccessSaveFunction) {
        onSuccessSaveFunction();
      }
    } catch (e) {
      setLoading(false);
      notification.error({
        type: "error",
        message: "Can't delete customer address.",
        description: e.message,
        placement: "bottomRight"
      })
    } finally {
      setLoading(false)
    }
  }

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
          disabled={disabled || (address && !edit)}
        >
          { address ?
            <Row style={{display: 'flex', alignItems: 'center'}}>
              <Col span={10} offset={1}>
                <h4>{address.type == 0 ? 'Billing address ' : 'Contact Address '} #{address.id}</h4>
              </Col>
              <Col span={10} offset={2} style={{textAlign: 'right'}}>
                  { !edit ?
                  <>
                    <IconButton 
                      onClick={() => setEdit(true)}
                      color="primary"
                    >
                      <FormOutlined />
                    </IconButton>
                    <IconButton color="error" onClick={deleteAddress}>
                      <DeleteOutlined />
                    </IconButton>
                  </> : 
                  <IconButton 
                    color="error" 
                    onClick={() => setEdit(false)}
                  >
                    <CloseCircleOutlined />
                  </IconButton> }
              </Col>
            </Row>
          : null }
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
          {!address && disabled == false ? (
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
          ) : edit && (
            <Row style={{ textAlign: "right" }}>
              <Col span={22} offset={1}>
                <Button
                  type="primary"
                  onClick={saveAddress}
                  style={{ marginBototm: "15px" }}
                >
                  Save Address
                </Button>
              </Col>
            </Row>
          )}
        </Form>
      </Spin>
    </>
  );
};

export default CustomerAddressForm;
