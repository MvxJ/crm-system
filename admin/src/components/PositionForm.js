import React, { useEffect, useState } from "react";
import instance from "utils/api";
import { notification } from "antd";
import {
  Button,
  Col,
  Form,
  Input,
  Row,
  Select,
  Spin,
} from "../../node_modules/antd/es/index";
import "./TabStyles.css";
import { useNavigate } from "../../node_modules/react-router-dom/dist/index";
import { IconButton } from "../../node_modules/@mui/material/index";
import { FormOutlined, CheckSquareOutlined, CloseSquareOutlined, DeleteOutlined } from '@ant-design/icons';

const PositionForm = ({ position, billId }) => {
  const [edit, setEdit] = useState(false);
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    bill: billId, 
    type: position ? position.type : 0,
    price: position?.price,
    amount: position?.amount,
    name: position?.name,
    description: position?.description
  })

  const savePosition = async () => {
    try {
      setLoading(true);

      const response = await instance.patch(`/bill/position/${position.id}/edit`, {
        type: parseInt(formData.type),
        price: parseFloat(formData.price),
        amount: parseInt(formData.amount),
        name: formData.name,
        description: formData.description
      });

      if (response.status != 200) {
        setLoading(false);
        setEdit(false);

        notification.error({
          message: "Can't update position.",
          type: "error",
          placement: "bottomRight"
        });

        return;
      }

      setLoading(false);
      setEdit(false);
      notification.success({
        message: "Successfully updated position.",
        type: "success",
        placement: "bottomRight"
      });
    } catch (e) {
      console.log(e);
      setEdit(false);
      setLoading(false);
      notification.error({
        message: "Can't update position.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      });
    } finally {
      setEdit(false);
      setLoading(false);
    }
  }

  const deletePosition = async () => {
    try {
      setLoading(true);

      const response = await instance.delete(`/bill/position/${position.id}/delete`);

      if (response.status != 200) {
        setLoading(false);
        
        notification.error({
          message: "Can't delete position.",
          placement: "bottomRight",
          type: "error"
        });

        return;
      }

      setLoading(false);
      setTimeout(() => {
        window.location.reload();
      }, 1000);
      notification.success({
        message: "Successfully deleted position.",
        type: "success",
        placement: "bottomRight"
      })
    } catch (e) {
      setLoading(false);

      notification.error({
        message: "Can't delete position.",
        description: e.message,
        placement: "bottomRight",
        type: "error"
      })
    } finally {
      setLoading(false);
    }
  }

  const addPosition = async () => {
    try {
      setLoading(true);

      const response = await instance.post(`/bill/position/add`, {
        bill: formData.bill, 
        type: parseInt(formData.type),
        price: parseFloat(formData.price),
        amount: parseInt(formData.amount),
        name: formData.name,
        description: formData.description
      });

      if (response.status != 200) {
        setLoading(false);

        notification.error({
          message: "Can't add position.",
          type: "error",
          placement: "bottomRight"
        });

        return;
      }

      setLoading(false);
      setTimeout(() => {
        window.location.reload();
      }, 1000);
      notification.success({
        message: "Successfully added position.",
        type: "success",
        placement: "bottomRight"
      });
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Can't add position.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      });
    } finally {
      setLoading(false);
    }
  }

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  return (
    <>
    <Spin spinning={loading}>
      <Form layout="vertical" style={{ width: 100 + "%" }}>
        <Row gutter={[16, 16]}>
          <Col span={4}>
            <Form.Item 
              label="Name"
              required
              tooltip="This is a required field"
            >
              <Input
                onChange={handleInputChange}
                name="name"
                defaultValue={position?.name}
                disabled={position && !edit}
              />
            </Form.Item>
          </Col>
          <Col span={4}>
            <Form.Item 
              label="Type"
              required
              tooltip="This is a required field"
            >
              <Select
                name="type"
                onChange={(value) => {
                  setFormData((prevFormData) => ({ ...prevFormData, type: value }));
                }}
                disabled={position && !edit}
                value={formData.type}
                options={[
                  { value: 0, label: "Contract" },
                  { value: 1, label: "Service" },
                  { value: 2, label: "Device" },
                ]}
              />
            </Form.Item>
          </Col>
          <Col span={4}>
            <Form.Item 
              label="Amount"
              required
              tooltip="This is a required field"
            >
              <Input
                type="number"
                onChange={handleInputChange}
                name="amount"
                defaultValue={position?.amount}
                disabled={position && !edit}
              />
            </Form.Item>
          </Col>
          <Col span={4}>
            <Form.Item 
              label="Price"
              required
              tooltip="This is a required field"
            >
              <Input
                type="number"
                onChange={handleInputChange}
                name="price"
                defaultValue={position?.price}
                disabled={position && !edit}
              />
            </Form.Item>
          </Col>
          <Col span={4}>
            <Form.Item label="Description">
              <Input
                onChange={handleInputChange}
                name="description"
                defaultValue={position?.description}
                disabled={position && !edit}
              />
            </Form.Item>
          </Col>
          {position && !edit ? (
            <Col span={4} style={{paddingTop: '30px'}}>
              <IconButton
                color="primary"
                onClick={() => {
                  setEdit(true);
                }}
              >
                <FormOutlined />
              </IconButton>
              <IconButton
                color="error"
                onClick={deletePosition}
              >
                <DeleteOutlined />
              </IconButton>
            </Col>
          ) : null}
          {position && edit ? (
            <Col span={4} style={{paddingTop: '30px'}}>
              <IconButton
                color="error"
                onClick={() => {
                  setEdit(false);
                }}
              >
                <CloseSquareOutlined />
              </IconButton>
              <IconButton
                onClick={savePosition}
                color="success"
                style={{ marginLeft: "15px" }}
              >
                <CheckSquareOutlined />
              </IconButton>
            </Col>
          ) : null}
          {position == null ? (
            <Col span={4} style={{paddingTop: '30px'}}>
              <Button type="primary" onClick={addPosition}>Add</Button>
            </Col>
          ) : null}
        </Row>
      </Form>
      </Spin>
    </>
  );
};

export default PositionForm;
