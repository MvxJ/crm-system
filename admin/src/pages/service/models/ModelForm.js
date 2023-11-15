import MainCard from "components/MainCard";
import { useEffect, useState } from "react";
import {
  useNavigate,
  useParams,
} from "../../../../node_modules/react-router-dom/dist/index";
import instance from "utils/api";
import {
  Button,
  Col,
  Form,
  Input,
  Row,
  Select,
  Switch,
  notification,
} from "../../../../node_modules/antd/es/index";

const ModelForm = () => {
  const { id } = useParams();
  const { TextArea } = Input;
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    manufacturer: "",
    name: "",
    description: "",
    params: "",
    type: 0,
    price: 0,
    isDeleted: false,
  });

  const fetchData = async () => {
    try {
      if (id) {
        const response = await instance.get(`/models/${id}`);

        setFormData({
          manufacturer: response.data.model.manufacturer,
          name: response.data.model.name,
          description: response.data.model.description,
          params: response.data.model.params,
          type: response.data.model.type,
          price: response.data.model.price,
          isDeleted: response.data.model.isDeleted,
        });
      }
    } catch (error) {
      notification.error({
        message: "Can't fetch user data.",
        description: error.message,
        type: "error",
        placement: "bottomRight",
      });
    }
  };

  useEffect(() => {
    fetchData();
  }, [id]);

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  const createModel = async () => {
    try {
      const response = await instance.post(`/models/add`, {
        manufacturer: formData.manufacturer,
        name: formData.name,
        description: formData.description,
        params: formData.params,
        type: parseInt(formData.type),
        price: parseFloat(formData.price),
      });

      if (response.status != 200) {
        notification.error({
          message: "Can't create model.",
          type: "error",
          placement: "bottomRight",
        });

        return;
      }

      notification.success({
        message: "Successfully created model.",
        type: "success",
        placement: "bottomRight",
      });

      navigate(`/service/models/detail/${response.data.model.id}`);
    } catch (e) {
      notification.error({
        message: "An error ocured during creating model.",
        description: e.message,
        type: "error",
        placement: "bottomRight",
      });
    }
  };

  const saveModel = async () => {
    try {
      const response = await instance.patch(`/models/${id}/edit`, formData);

      if (response.status != 200) {
        notification.error({
          message: "Can't update model.",
          type: "error",
          placement: "bottomRight",
        });

        return;
      }

      notification.success({
        message: "Successfully updated model.",
        type: "success",
        placement: "bottomRight",
      });

      navigate(`/service/models/detail/${id}`);
    } catch (e) {
      notification.error({
        message: "An error ocured during updating model.",
        description: e.message,
        type: "error",
        placement: "bottomRight",
      });
    }
  };

  return (
    <>
      <MainCard title={id ? `Edit Model #${id}` : "Create Model"}>
        <Row>
          <Col span={22} offset={1} style={{ textAlign: "right" }}>
            {id ? (
              <Button type="primary" onClick={saveModel}>
                Save Model
              </Button>
            ) : (
              <Button type="primary" onClick={createModel}>
                Add Model
              </Button>
            )}
          </Col>
        </Row>
        <Form layout="vertical" style={{ width: 100 + "%" }}>
          <Row>
            <Col span={22} offset={1}>
              <h4>Model:</h4>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Manufacturer"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the manufacturer!",
                  },
                ]}
              >
                <Input
                  name="manufacturer"
                  value={formData.manufacturer}
                  onChange={handleInputChange}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Name"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the name!",
                  },
                ]}
              >
                <Input
                  name="name"
                  value={formData.name}
                  onChange={handleInputChange}
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
                    message: "Please select type!",
                  },
                ]}
              >
                <Select
                  defaultValue={formData.type}
                  onChange={(value) =>
                    handleInputChange({ target: { name: "type", value } })
                  }
                  options={[
                    { value: 0, label: "Router" },
                    { value: 1, label: "Decoder" },
                    { value: 2, label: "Phone" },
                  ]}
                />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item
                label="Price"
                required
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                    message: "Please enter the price!",
                  },
                ]}
              >
                <Input
                  type="number"
                  name="price"
                  value={formData.price}
                  onChange={handleInputChange}
                />
              </Form.Item>
            </Col>
          </Row>
          { id ?
          <Row>
            <Col span={10} offset={1}>
              <Form.Item
                label="Deleted"
              >
                <Switch
                  name="isDeleted"
                  checked={formData.isDeleted}
                  onChange={(value) => handleInputChange({ target: { name: "isDeleted", value } })}
                />
              </Form.Item>
            </Col>
          </Row> : null }
          <Row>
            <Col span={10} offset={1}>
              <Form.Item label="Description">
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
            <Col span={10} offset={2}>
              <Form.Item label="Params">
                <TextArea
                  showCount
                  maxLength={1500}
                  style={{ height: 120, resize: "none" }}
                  placeholder="Params..."
                  value={formData.params}
                  onChange={handleInputChange}
                  name="params"
                />
              </Form.Item>
            </Col>
          </Row>
        </Form>
      </MainCard>
    </>
  );
};

export default ModelForm;
