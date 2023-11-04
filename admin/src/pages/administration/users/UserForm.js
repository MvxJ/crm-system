import MainCard from 'components/MainCard';
import { useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { Button, Col, Form, Input, Row, Switch, notification, Checkbox } from '../../../../node_modules/antd/es/index';
import { useEffect, useState } from 'react';
import instance from 'utils/api';

const UserForm = () => {
  const { id } = useParams();
  const [user, setUser] = useState({
    id: '',
    name: '',
    surname: '',
    username: '',
    email: '',
    phoneNumber: '',
    isVerified: '',
    isActive: '',
    roles: [],
    twoFactorAuth: ''
  });

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await instance.get(`/users/${id}/detail`);

        setUser({
          id: response.data.user.id,
          name: response.data.user.name,
          surname: response.data.user.surname,
          username: response.data.user.username,
          email: response.data.user.email,
          phoneNumber: response.data.user.phoneNumber,
          isVerified: response.data.user.isVerified,
          isActive: response.data.user.isActive,
          roles: response.data.user.roles,
          twoFactorAuth: response.data.user.twoFactorAuth
        });
      } catch (error) {
        notification.error({
          message: "Can't fetch user data.",
          description: error.message,
          type: 'error',
          placement: 'bottomRight'
        });
      }
    };

    if (id) {
      fetchData();
    }
  }, [id]);

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setUser((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  return (
    <>
      <MainCard title="User Form">
        <Row>
          <Col span={22} offset={1} style={{ textAlign: 'right' }}>
            {id ?
              <Button type='primary'>
                Save
              </Button>
              :
              <Button type='primary'>
                Add
              </Button>
            }
          </Col>
        </Row>
        <Form layout="vertical" style={{ width: 100 + '%' }}>
          <Row>
            <Col span={22} offset={1}>
              <h4>User data:</h4>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item label="Name">
                <Input name="name" value={user.name} onChange={handleInputChange} />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item label="Surname">
                <Input name="surname" value={user.surname} onChange={handleInputChange} />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item label="Email Address">
                <Input name="email" value={user.email} onChange={handleInputChange} />
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item label="Phone Number">
                <Input name="phoneNumber" value={user.phoneNumber} onChange={handleInputChange} />
              </Form.Item>
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <h4>Login data:</h4>
            </Col>
          </Row>
          <Row>
            <Col span={10} offset={1}>
              <Form.Item label="Username">
                <Input name="username" value={user.username} onChange={handleInputChange}/>
              </Form.Item>
            </Col>
            <Col span={10} offset={2}>
              <Form.Item label="2fa Authentication">
                <Switch 
                  name="twoFactorAuth"
                  checked={user.twoFactorAuth}
                  onChange={(value) => handleInputChange({ target: { name: "twoFactorAuth", value } })} 
                />
              </Form.Item>
            </Col>
          </Row>
          {
            id ?
              null
              :
              <Row>
                <Col span={10} offset={1}>
                  <Form.Item label="Password">
                    <Input />
                  </Form.Item>
                </Col>
                <Col span={10} offset={2}>
                  <Form.Item label="Repeat password">
                    <Input />
                  </Form.Item>
                </Col>
              </Row>
          }
          <Row>
            <Col span={22} offset={1}>
              <h4>User roles:</h4>
            </Col>
          </Row>
          <Checkbox.Group style={{width: '100%'}}>
                {/*IN FOREACH FOR EVERY ROLE ADD ROW WITH COL AND CHECKBOX*/}
          </Checkbox.Group>
        </Form>
      </MainCard>
    </>
  )
};

export default UserForm;
