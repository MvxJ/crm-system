import MainCard from 'components/MainCard';
import { Button, Col, Form, Input, Row, Spin, Switch, notification } from '../../../node_modules/antd/es/index';
import { useEffect, useState } from 'react';
import instance from 'utils/api';

const EditProfile = () => {
  const [formData, setFormData] = useState(
    {
      name: "",
      surname: "",
      phoneNumber: "",
      email: "",
      twoFactorAuth: "",
    }
  );
  const [loading, setLoading] = useState(false);

  const saveForm = async () => {
    try {
      setLoading(true);
      const user = JSON.parse(localStorage.getItem("user"));
      const response = await instance.patch(`/users/${user.id}/edit`, {
        phoneNumber: formData.phoneNumber,
        email: formData.email,
        surname: formData.surname,
        name: formData.name,
        emailAuth: formData.twoFactorAuth
      })

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          message: "Error while saving.",
          description: response.data.message,
          type: 'error',
          placement: 'bottomRight'
        });
        return;
      }

      setLoading(false);
      notification.success({
        message: "Successfully saved!",
        type: 'success',
        placement: 'bottomRight'
      });
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Error while saving.",
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

  const fetchData = async () => {
    try {
      setLoading(true);
      const user = JSON.parse(localStorage.getItem("user"));
      const response = await instance.get(`/users/${user.id}/detail`);

      setFormData({
        name: response.data.user.name,
        surname: response.data.user.surname,
        phoneNumber: response.data.user.phoneNumber,
        email: response.data.user.email,
        twoFactorAuth: response.data.user.twoFactorAuth,
    });
    setLoading(false);
    } catch (error) {
      setLoading(false);
      notification.error({
        message: "Can't fetch user data.",
        type: 'error',
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
};

  useEffect(() => {
    fetchData();
  }, []
  );

  return (
    <>
    <Spin spinning={loading}>
      <MainCard title="Edit Profile">
        <Form
          layout="vertical"
          style={{ width: 100 + '%' }}
        >
          <Row>
            <Col span={22} offset={1} style={{ textAlign: 'right', marginBottom: '5px' }}>
              <Button type="primary" onClick={saveForm}>
                Save
              </Button>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item label="Name">
                <Input name="name" value={formData.name} onChange={handleInputChange} />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item label="Surname">
                <Input name="surname" value={formData.surname} onChange={handleInputChange} />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item label="Email Address">
                <Input name="email" value={formData.email} onChange={handleInputChange} />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item label="Phone Number">
                <Input name="phoneNumber" value={formData.phoneNumber} onChange={handleInputChange} />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <Form.Item label="Email 2fa Authentication">
                <Switch 
                  name="twoFactorAuth"
                  checked={formData.twoFactorAuth}
                  onChange={(value) => handleInputChange({ target: { name: "twoFactorAuth", value } })} 
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

export default EditProfile;
