import MainCard from 'components/MainCard';
import './Contract.css'
import { Link, useNavigate, useParams } from '../../../../node_modules/react-router-dom/dist/index';
import { useEffect, useState } from 'react';
import { Button, Col, Row, Spin, notification } from '../../../../node_modules/antd/es/index';
import instance from 'utils/api';
import { EditOutlined, DeleteOutlined } from '@ant-design/icons';
import FormatUtils from 'utils/format-utils';
import './Contract.css'

const ContractDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [contract, setContract] = useState({
    id: id,
    contractNumber: "",
    customer: null,
    status: 0,
    startDate: null,
    totalSum: 0,
    city: "",
    zipCode: "",
    address: "",
    createdAt: null,
    updatedAt: null,
    price: 0,
    discount: null,
    discountType: null,
    offer: null,
    description: ""
  });

  const styles = {
    userInicials: {
      width: '35px',
      height: '35px',
      borderRadius: '50%',
      border: '1px solid #1890ff',
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
      backgroundColor: '#e6f7ff',
      color: '#1890ff',
      fontWeight: 'bold'
    }
  }

  const fetchData = async () => {
    try {
      setLoading(true);
      const response = await instance.get(`/contracts/${id}/details`);

      if (response.status != 200) {
        setLoading(false);
        notification.error({
          type: 'error',
          messsage: "Can't fetch contract detail.",
          placement: 'bottomRight'
        });

        return;
      }

      setContract({
        id: response.data.contract.id,
        contractNumber: response.data.contract.contractNumber,
        customer: response.data.contract.customer,
        status: response.data.contract.status,
        startDate: response.data.contract.startDate,
        totalSum: response.data.contract.totalSum,
        city: response.data.contract.city,
        zipCode: response.data.contract.zipCode,
        address: response.data.contract.address,
        createdAt: response.data.contract.createdAt,
        updatedAt: response.data.contract.updatedAt,
        price: response.data.contract.price,
        discount: response.data.contract.discount,
        discountType: response.data.contract.discountType,
        offer: response.data.contract.offer,
        description: response.data.contract.description
      });
      setLoading(false);
    } catch (e) {
      setLoading(false);
      notification.error({
        type: 'error',
        messsage: "Can't fetch contract detail.",
        description: e.message,
        placement: 'bottomRight'
      });
    } finally {
      setLoading(false);
    }
  }

  const getUserInicials = () => {
    if (contract.customer) {
      const name = contract.customer?.name;
      const surname = contract.customer?.surname;

      return name.slice(0, 1).toUpperCase() + surname.slice(0, 1).toUpperCase();
    }

    return '';
  }

  const handleEditClick = () => {
    navigate(`/office/contracts/edit/${id}`);
  }

  const handleDeleteContract = async () => {
    try {
      setLoading(true);
      const response = await instance.delete(`/contracts/${id}/delete`);

      if (response.status != 200) {
        setLoading(false);
        return;
      }

      setLoading(false);
      notification.success({
        message: "Successfully closed contract.",
        type: "success",
        placement: "bottomRight"
      });

      setTimeout(() => {
        window.location.reload();
      }, 1000);
    } catch (e) {
      setLoading(false);
      notification.error({
        message: "Can't close contract.",
        description: e.message,
        type: "error",
        placement: "bottomRight"
      })
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    fetchData();
  }, [id]);

  return (
    <>
      <Spin spinning={loading}>
      <MainCard title={
        <div className='title-container'>
          <span>{'Contract Detail #' + id}</span>
          <div className='badge' style={{ backgroundColor: FormatUtils.getContractBadgeDetails(contract.status).color }}>
            {FormatUtils.getContractBadgeDetails(contract.status).text}
          </div>
        </div>
      }>
        <Row style={{ textAlign: 'right' }}>
          <Col span={24}>
            {contract.status != 1 ?
              <div>
                <Button onClick={handleEditClick} type="primary">
                  <EditOutlined /> Edit Contract
                </Button>
                <Button onClick={handleDeleteContract} type="primary" danger style={{marginLeft: '5px'}}>
                  <DeleteOutlined /> Close Contract
                </Button>
              </div>
              : null}
          </Col>
        </Row>
        <Row>
          <h4>Contract details: </h4>
        </Row>
        <Row>
          <Col span={24}>
            <b>Number: </b> {contract.contractNumber}
          </Col>
          <Col span={24}>
            <b>Created At: </b> {contract.createdAt != null ? FormatUtils.formatDateWithTime(contract.createdAt.date) : 'empty'}
          </Col>
          <Col span={24}>
            <b>Edited At: </b> {contract.updatedAt != null ? FormatUtils.formatDateWithTime(contract.updatedAt.date) : 'empty'}
          </Col>
          <Col span={24}>
            <b>Start Service Date: </b> {contract.startDate != null ? FormatUtils.formatDateWithTime(contract.startDate.date) : 'empty'}
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <Row>
              <Col span={12}>
                <b>Customer: </b>
              </Col>
              <Col span={12}>
                <b>Offer: </b>
              </Col>
            </Row>
            <Row style={{ marginBottom: '15px', marginTop: '15px' }}>
              <Col span={12}>
                <Link to={`/customers/detail/${contract.customer?.id}`}>
                  <div style={{ display: 'flex', alignItems: 'center', marginTop: '5px' }}>
                    <div style={styles.userInicials}>
                      {getUserInicials(contract.customer)}
                    </div>
                    <div style={{ marginLeft: '5px' }}>
                      {contract.customer?.name + ' ' + contract.customer?.surname}
                    </div>
                  </div>
                </Link>
              </Col>
              <Col span={12}>
                <Link to={`/office/offers/detail/${contract.offer?.id}`}>
                  #{contract.offer?.name}
                </Link>
              </Col>
            </Row>
          </Col>
        </Row>
        <Row>
          <Col span={24}>
            <Row>
              <Col span={12}>
                <b>Address: </b>
              </Col>
              <Col span={12}>
                <b>Pircing: </b>
              </Col>
            </Row>
            <Row>
              <Col span={12}>
                <Row>
                  <Col span={24}>
                    <b>City: </b> {contract.city}
                  </Col>
                  <Col span={24}>
                    <b>Zip-Code: </b> {contract.zipCode}
                  </Col>
                  <Col span={24}>
                    <b>Street: </b> {contract.address}
                  </Col>
                </Row>
              </Col>
              <Col span={12}>
                <Row>
                  <Col span={24}>
                    <b>Price: </b> {contract.price}
                  </Col>
                  <Col span={24}>
                    <b>Discount: </b> {FormatUtils.getOfferDiscount(contract.discount, contract.discountType, contract.price)}
                  </Col>
                  <Col span={24}>
                    <b>Total Sum: </b> {contract.totalSum}
                  </Col>
                </Row>
              </Col>
            </Row>
          </Col>
        </Row>
        <Row>
              <Col span={24}>
                <h4>Description:</h4>
                <p>{contract.description}</p>
              </Col>
            </Row>
      </MainCard>
      </Spin>
    </>
  )
};

export default ContractDetail;
