import MainCard from 'components/MainCard';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { Button, Col, Form, Input, Row, Switch, notification, Checkbox, Spin } from '../../../../node_modules/antd/es/index';
import { useEffect, useState } from 'react';
import instance from 'utils/api';
import UsersService from 'utils/UserService';
import axios from '../../../../node_modules/axios/index';

const UserForm = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const [loading, setLoading] = useState(false);
  const [user, setUser] = useState({
    id: null,
    name: null,
    surname: null,
    username: null,
    email: null,
    phoneNumber: null,
    isVerified: null,
    isActive: null,
    roles: [],
    twoFactorAuth: false,
    password: null,
    repeatedPassword: null
  });
  const [allRoles, setAllRoles] = useState([]);
  const [selectedRoles, setSelectedRoles] = useState([]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        if (id) {
          setLoading(true);
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
          const selectedRolesArray = [];

          response.data.user.roles.forEach(role => {
            selectedRolesArray.push(role.id);
          });

          setSelectedRoles(selectedRolesArray);
        }

        const rolesResponse = await instance.get('/users/roles');
        var rolesValues = [];

        rolesResponse.data.roles.forEach(role => {
          rolesValues.push({ label: role.name, value: role.id });
        });

        setAllRoles(rolesValues);
        setLoading(false);
      } catch (error) {
        setLoading(false);
        notification.error({
          message: "Can't fetch user data.",
          description: error.message,
          type: 'error',
          placement: 'bottomRight'
        });
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [id]);

  const handleInputChange = (event) => {
    const { name, value } = event.target;
    setUser((prevFormData) => ({ ...prevFormData, [name]: value }));
  };

  const handleRoleChange = (values) => {
    setSelectedRoles(values);
  };

  const createUser = async () => {
    try {
      setLoading(true);
      const response = await instance.post(`/users/add`, {
        emailAuth: user.twoFactorAuth,
        phoneNumber: user.phoneNumber,
        username: user.username,
        email: user.email,
        roles: selectedRoles,
        name: user.name,
        surname: user.surname,
        password: user.password,
        repeatedPassword: user.repeatedPassword
      });

      if (response.status != 200) {
        notification.error({
          message: "Can't create user.",
          type: 'error',
          placement: 'bottomRight'
        })
      }

      notification.success({
        message: "Successfully updated user.",
        type: 'success',
        placement: 'bottomRight'
      })

      setLoading(false);
      navigate(`/administration/users/detail/${response.data.user.id}`);
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "An error ocured during creating user.",
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      })
    } finally {
      setLoading(false);
    }
  }

  const saveUser = async () => {
    try {
      setLoading(true);
      const response = await instance.patch(`/users/${id}/edit`, {
        emailAuth: user.twoFactorAuth,
        phoneNumber: user.phoneNumber,
        username: user.username,
        email: user.email,
        roles: selectedRoles,
        name: user.name,
        surname: user.surname
      });

      if (response.status != 200) {
        notification.error({
          message: "Can't update user.",
          type: 'error',
          placement: 'bottomRight'
        })
      }

      notification.success({
        message: "Successfully updated user.",
        type: 'success',
        placement: 'bottomRight'
      })

      setLoading(false);
      navigate(`/administration/users/detail/${id}`);
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "An error ocured during updating user.",
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      })
    } finally {
      setLoading(false);
    }
  }

  return (
    <>
      <Spin
        spinning={loading}
      >
        <MainCard title={id ? 'Edit User' : 'Create User'}>
          <Row>
            <Col span={22} offset={1} style={{ textAlign: 'right' }}>
              {id ?
                <Button type='primary' onClick={saveUser}>
                  Save
                </Button>
                :
                <Button type='primary' onClick={createUser}>
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
                <Form.Item
                  label="Name"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: 'Please enter the name!',
                    }]}
                >
                  <Input name="name" value={user.name} onChange={handleInputChange} />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item
                  label="Surname"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: 'Please enter the surname!',
                    },
                  ]}
                >
                  <Input name="surname" value={user.surname} onChange={handleInputChange} />
                </Form.Item>
              </Col>
            </Row>
            <Row>
              <Col span={10} offset={1}>
                <Form.Item
                  label="Email Address"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: 'Please enter the email address!',
                    },
                  ]}
                >
                  <Input name="email" value={user.email} onChange={handleInputChange} />
                </Form.Item>
              </Col>
              <Col span={10} offset={2}>
                <Form.Item
                  label="Phone Number"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: 'Please enter the phone number!',
                    },
                  ]}
                >
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
                <Form.Item
                  label="Username"
                  required
                  tooltip="This is a required field"
                  rules={[
                    {
                      required: true,
                      message: 'Please enter the username!',
                    },
                  ]}
                >
                  <Input name="username" value={user.username} onChange={handleInputChange} />
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
                    <Form.Item
                      label="Password"
                      required
                      name="pass"
                      tooltip="This is a required field"
                      rules={[
                        {
                          required: true,
                          message: 'Please enter the password!',
                        }]}
                    >
                      <Input.Password name="password" onChange={handleInputChange} />
                    </Form.Item>
                  </Col>
                  <Col span={10} offset={2}>
                    <Form.Item
                      label="Repeat password"
                      required
                      name="rPass"
                      tooltip="This is a required field"
                      rules={[
                        {
                          required: true,
                          message: 'Please repeat the password!',
                        },
                        ({ getFieldValue }) => ({
                          validator(_, value) {
                            if (!value || getFieldValue('pass') === value) {
                              return Promise.resolve();
                            }
                            return Promise.reject(new Error('The passwords doesnt match please correct them!'));
                          },
                        }),
                      ]}
                    >
                      <Input.Password name="repeatedPassword" onChange={handleInputChange} />
                    </Form.Item>
                  </Col>
                </Row>
            }
            <Row>
              <Col span={22} offset={1}>
                <h4>User roles:</h4>
              </Col>
            </Row>
            <Row>
              <Checkbox.Group style={{ width: '100%', display: 'flex' }} value={selectedRoles} onChange={handleRoleChange}>
                <Col span={22} offset={1}>

                  {allRoles.map((role) => (
                    <Row key={role.value}>
                      <Col span={22} offset={1}>
                        <Form.Item>
                          <Checkbox
                            value={role.value}
                            onChange={() => handleRoleChange(role.id)}
                            style={{ width: '100%' }}
                          >
                            {role.label}
                          </Checkbox>
                        </Form.Item>
                      </Col>
                    </Row>
                  ))}
                </Col>
              </Checkbox.Group>
            </Row>
          </Form>
        </MainCard>
      </Spin>
    </>
  )
};

export default UserForm;
