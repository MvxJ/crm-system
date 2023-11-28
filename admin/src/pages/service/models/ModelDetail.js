import MainCard from 'components/MainCard';
import { useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import { Button, Col, Row, Spin, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';
import './Model.css';
import { DeleteOutlined, EditOutlined } from '@ant-design/icons';

const ModelDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [model, setModel] = useState({
    id: id,
    isDeleted: false,
    manufacturer: "",
    name: "",
    type: 0,
    price: 0,
    description: "",
    params: "",
    availableDevices: 0
  });

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const response = await instance.get(`/models/${id}`);

        setModel({
          id: response.data.model.id,
          isDeleted: response.data.model.isDeleted,
          manufacturer: response.data.model.manufacturer,
          name: response.data.model.name,
          type: response.data.model.type,
          price: response.data.model.price,
          description: response.data.model.description,
          params: response.data.model.params,
          availableDevices: response.data.model.availableDevices
        });
        setLoading(false);
      } catch (error) {
        setLoading(false);
        notification.error({
          message: "Can't fetch device detail.",
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

  const navigateEdit = () => {
    navigate(`/service/models/edit/${id}`);
  }

  const handleDelete = async () => {
    try {
      const response = await instance.delete(`/models/${id}/delete`);

      if (response.status != 200) {
        notification.error({
          message: "An erro ocured during deleting model.",
          type: "error",
          placement: "bottomRight"
        });
      }

      navigate("/service/models");

      notification.success({
        message: "Successfully deleted model.",
        type: "success",
        placement: "bottomRight"
      });
    } catch (e) {
      notification.error({
        message: "An erro ocured during deleting model.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      });
    }
  }

  return (
    <>
      <Spin spinning={loading}>
      <MainCard title={
        <div className='title-container'>
          <span>{"Model Detail #" + id}</span>
          <div className="badge" style={{ backgroundColor: model.isDeleted ? '#ff7875' : '#95de64' }}>
            {model.isDeleted ? 'Deleted' : 'Available'}
          </div>
        </div>
      }>
        <Row>
          <Col span={10}>
            <b>{model.manufacturer} - {model.name}</b>
          </Col>
          <Col span={14} style={{ textAlign: 'right' }}>

            {model.isDeleted == true ? null :
              <Button type='primary' onClick={navigateEdit}>
                <EditOutlined /> Edit
              </Button>
            }
            {model.isDeleted == true ? null :
              <Button style={{ marginLeft: '5px' }} type='primary' danger onClick={handleDelete}>
                <DeleteOutlined /> Delete
              </Button>}
          </Col>
        </Row>
        <Row>
          <Col>
            <h4>Price:</h4>
            <p>{model.price}</p>
          </Col>
        </Row>
        <Row>
          <Col>
            <b>
            Available devices:
            </b> {model.availableDevices}
          </Col>
        </Row>
        <Row>
          <Col>
            <h4>Params:</h4>
            <p>{model.params}</p>
          </Col>
        </Row>
        <Row>
          <Col>
            <h4>Description:</h4>
            <p>{model.description}</p>
          </Col>
        </Row>
      </MainCard>
      </Spin>
    </>
  )
};

export default ModelDetail;
