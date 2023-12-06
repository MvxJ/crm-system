import MainCard from 'components/MainCard';
import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { notification } from 'antd';
import instance from 'utils/api';
import { Button, Col, Row, Spin } from '../../../../node_modules/antd/es/index';
import './Users.css';
import { PhoneOutlined, MailOutlined, UserOutlined, DeleteOutlined, SendOutlined, SafetyOutlined, RightOutlined, EditOutlined } from '@ant-design/icons';
import { useNavigate } from '../../../../node_modules/react-router-dom/dist/index';
import UsersService from 'utils/UserService';

const UserDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
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

  const getUserNameAndSurname = () => {
    return user.name + ' ' + user.surname;
  }

  const getUserInicials = () => {
    return user.name.slice(0, 1).toUpperCase() + user.surname.slice(0, 1).toUpperCase();
  }

  const getBadgeDetails = () => {
    if (!user.isActive) {
      return { text: 'Deleted', color: '#f50' };
    }

    return user.isVerified ? { text: 'Verified', color: 'hsl(102, 53%, 61%)' } : { text: 'No-Verified', color: '#f50' };
  }

  const deleteUser = async () => {
    try {
      const response = await UsersService.deleteUser(id);

      if (response.status != 200) {
        notification.error({
          message: "An error ocured during deleting user.",
          type: 'error',
          placement: 'bottomRight'
        });

        return;
      }

      notification.success({
        message: "Successfully deleted user.",
        type: 'success',
        placement: 'bottomRight'
      });

      navigate("/administration/users");
    } catch (e) {
      notification.error({
        message: "An error occured during deleting user.",
        description: e.message,
        type: 'error',
        placement: 'bottomRight'
      })
    }
  }

  const editUser = () => {
    navigate(`/administration/users/edit/${id}`);
  }

  useEffect(() => {
    const fetchData = async () => {
      try {
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

  return (
    <>
      <Spin
        spinning={loading}
      >
        <MainCard title={"User Detail"}>
          <Row>
            <Col span={22} offset={1} style={{ textAlign: 'right' }}>
              {
                user.isActive ?
                  <Button type="primary" style={{ marginLeft: '5px' }} onClick={editUser}>
                    <EditOutlined /> Edit
                  </Button> : null
              }
              {
                user.isActive ?
                  <Button type="primary" danger style={{ marginLeft: '5px' }} onClick={deleteUser}>
                    <DeleteOutlined /> Delete user
                  </Button> : null
              }
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <div className='headerContainer'>
                <div className='userProfileIcon'>
                  {getUserInicials()}
                </div>
                <div className='userName' style={{ marginLeft: '15px' }}>
                  {getUserNameAndSurname()}
                </div>
                <div className='badge' style={{ backgroundColor: getBadgeDetails().color, marginLeft: '15px' }}>
                  {getBadgeDetails().text}
                </div>
              </div>
            </Col>
          </Row>
          <Row style={{ marginTop: '15px' }}>
            <Col span={22} offset={1}>
              <h4>User details:</h4>
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <UserOutlined /> Username - {user.username ? user.username : 'empty'}
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <MailOutlined /> Email address - {user.email ? user.email : 'empty'}
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <PhoneOutlined /> Phone - {user.phoneNumber ? user.phoneNumber : 'empty'}
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <div style={{ display: 'flex' }}>
                <SafetyOutlined /> &nbsp; 2fa&nbsp;-&nbsp;{user.twoFactorAuth == true ? <div style={{ color: 'hsl(102, 53%, 61%)' }}> Active</div> : <div style={{ color: '#f50' }}> Inactive</div>}
              </div>
            </Col>
          </Row>
          <Row>
            <Col span={22} offset={1}>
              <h4>User roles:</h4>
            </Col>
          </Row>
          {
            user.roles.map((role, index) => (
              <Row key={index}>
                <Col span={22} offset={1}>
                  <RightOutlined /> {role.name + ' (' + role.role + ')'}
                </Col>
              </Row>
            ))
          }
        </MainCard>
      </Spin>
    </>
  );
};

export default UserDetail;