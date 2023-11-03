import MainCard from 'components/MainCard';
import { Button, Col, Form, Input, Row, Switch, notification } from '../../../node_modules/antd/es/index';
import AuthService from 'utils/auth';
import { useNavigate } from '../../../node_modules/react-router-dom/dist/index';
import { useState } from 'react';
import instance from 'utils/api';

const ProfileSettings = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState(
    {
      oldPassword: "",
      newPassword: "",
      repeatedPassword: ""
    }
  );

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setFormData((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  const changeUserPassword = async () => {
    try {
      if (formData.oldPassword.length == 0 || formData.newPassword.length == 0 || formData.repeatedPassword.length == 0) {
        notification.error({
          message: "Can't submit this form.",
          description: 'Please fill up entire form.',
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      if (formData.newPassword.length != formData.repeatedPassword.length) {
        notification.error({
          message: "Can't submit this form.",
          description: "New password doesn't match repeated password.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      const user = JSON.parse(localStorage.getItem("user"));
      const response = await instance.post(`/users/${user.id}/password/change`, {
        newPassword: formData.newPassword,
        oldPassword: formData.oldPassword
      });

      if (response.status != 200) {
        notification.error({
          message: "Error ocured during changing user password..",
          description: response.data.message ?? 'There was an error.',
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      notification.success({
        message: "Successfully changed user password.",
        type: 'success',
        placement: 'bottomRight'
      });

      AuthService.logout();
      navigate('/login');
    } catch (exception) {
      notification.error({
        message: "Can't change user password",
        description: exception.message,
        type: 'error',
        placement: 'bottomRight'
      });
    }
  }

  return (
    <>
      <MainCard title="Profile Settings">
        <Form
          layout="vertical"
          style={{ width: 100 + '%' }}
        >
          <Row>
            <h4>Change user password</h4>
          </Row>
          <Row>
            <Col span={10} offset={7}>
              <Form.Item
                label="Old Password"
                requaired tooltip="This is a required field"
                name="oPassword"
                rules={[
                  {
                    required: true,
                  }]}
              >
                <Input.Password value={formData.oldPassword} name="oldPassword" onChange={handleInputChange} />
              </Form.Item>
            </Col>
          </Row>

          <Row>
            <Col span={10} offset={7}>
              <Form.Item
                label="New Password"
                requaired
                tooltip="This is a required field"
                name="nPassword"
                rules={[
                  {
                    required: true,
                  },
                ]}
              >
                <Input.Password value={formData.newPassword} name="newPassword" onChange={handleInputChange} />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={7}>
              <Form.Item
                name="nPasswordRepeated"
                label="Repeat New Password"
                requaired
                tooltip="This is a required field"
                rules={[
                  {
                    required: true,
                  },
                  ({ getFieldValue }) => ({
                    validator(_, value) {
                      if (!value || getFieldValue('nPassword') === value) {
                        return Promise.resolve();
                      }
                      return Promise.reject(new Error('The new password doesnt match please correct them!'));
                    },
                  }),
                ]}
              >
                <Input.Password value={formData.repeatedPassword} name="repeatedPassword" onChange={handleInputChange} />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={7} style={{ textAlign: 'center' }}>
              <Button type='primary' onClick={changeUserPassword}>
                Change Password
              </Button>
            </Col>
          </Row>
        </Form>
      </MainCard>
    </>
  )
};

export default ProfileSettings;
